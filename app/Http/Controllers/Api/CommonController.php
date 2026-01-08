<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommonController extends ApiBaseController
{
    public function getAllBlogs()
    {
        try {
            $blogs = Blog::where('status', 1)
                ->orderBy('id', 'desc')
                ->paginate(12);

            $blogs->getCollection()
                ->transform(function ($blog) {

                    if (!$blog) {
                        return null;
                    }

                    $blog->image = $blog->image
                        ? asset($blog->image)
                        : null;

                    $blog->short_desc = trim(preg_replace("/\r|\n/", ' ', $blog->short_desc));
                    $blog->content    = trim(preg_replace("/\r|\n/", ' ', $blog->content));

                    // ✅ Same tags logic
                    $blog->tags = collect(
                        is_string($blog->tags) ? explode(',', $blog->tags) : []
                    )
                        ->map(fn($tag) => '#' . ltrim(trim($tag), '#'))
                        ->values()
                        ->toArray();

                    return $blog->makeHidden(['updated_at', 'slug']);
                });

            return $this->sendResponse(true, 'Blogs data found', ['blogs' => $blogs]);
        } catch (Exception $e) {
            Log::error('error in getBlogs', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }

    public function getProductCategories()
    {
        try {
            $getCategories = ProductCategory::orderBy('lft', 'ASC')->get();
            $getCategories->makeHidden(['created_at', 'updated_at', 'parent_id', 'lft', 'rgt', 'depth']);

            return $this->sendResponse(true, 'Product Category data found', ['categories_data' => $getCategories]);
        } catch (Exception $e) {
            Log::error('error in getProductCategories', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }

    public function getBlogDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'blog_id' => 'required:exists:blogs,id',
            ], [
                'blog_id.required' => 'Blog ID is required.',
                'blog_id.exists'   => 'Blog not found.',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return $this->sendResponse(false, $message);
            }

            $blog = Blog::where('id', $request->blog_id)
                ->where('status', 1)
                ->first();

            if (!$blog) {
                return $this->sendResponse(false, 'Blog not found.');
            }

            $blog->image = $blog->image ? asset($blog->image) : null;
            $blog->short_desc = trim(preg_replace("/\r|\n/", ' ', $blog->short_desc));
            $blog->content    = trim(preg_replace("/\r|\n/", ' ', $blog->content));

            $tagsArray = collect(
                is_string($blog->tags) ? explode(',', $blog->tags) : []
            )
                ->map(fn($tag) => trim($tag))
                ->filter()
                ->values()
                ->toArray();

            $blog->tags = collect($tagsArray)
                ->map(fn($tag) => '#' . ltrim($tag, '#'))
                ->toArray();

            $blog->makeHidden(['updated_at', 'slug']);

            $relatedBlogs = collect();

            if (!empty($tagsArray)) {
                $relatedBlogs = Blog::where('status', 1)
                    ->where('id', '!=', $blog->id)
                    ->where(function ($q) use ($tagsArray) {
                        foreach ($tagsArray as $tag) {
                            $q->orWhere('tags', 'LIKE', "%{$tag}%");
                        }
                    })
                    ->orderBy('id', 'desc')
                    ->limit(6)
                    ->get()
                    ->map(function ($related) {

                        $related->image = $related->image
                            ? asset($related->image)
                            : null;

                        $related->short_desc = trim(preg_replace("/\r|\n/", ' ', $related->short_desc));
                        $related->content    = trim(preg_replace("/\r|\n/", ' ', $related->content));

                        $related->tags = collect(
                            is_string($related->tags) ? explode(',', $related->tags) : []
                        )
                            ->map(fn($tag) => '#' . ltrim(trim($tag), '#'))
                            ->values()
                            ->toArray();

                        return $related->makeHidden(['updated_at', 'slug']);
                    });
            }

            return $this->sendResponse(true, 'Blog details found', [
                'blog' => $blog,
                'related_blogs' => $relatedBlogs,
            ]);
        } catch (Exception $e) {
            Log::error('error in getBlogDetails', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }

    public function getItemsSearch(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'type'  => 'required|in:blog,product',
                    'query' => 'required|string|min:1',
                ],
                [
                    'type.required'  => 'Type is required.',
                    'type.in'        => 'Type must be blog or product.',
                    'query.required' => 'Search query is required.',
                ]
            );

            if ($validator->fails()) {
                return $this->sendResponse(false, $validator->errors()->first());
            }

            $type  = $request->type;
            $query = trim($request->query);

            /* ================= BLOG SEARCH ================= */
            if ($type === 'blog') {

                $results = Blog::where('status', 1)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                            ->orWhere('tags', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('id', 'desc')
                    ->limit(20)
                    ->get()
                    ->map(function ($blog) {

                        $blog->image = $blog->image
                            ? asset($blog->image)
                            : null;

                        $blog->short_desc = trim(preg_replace("/\r|\n/", ' ', $blog->short_desc));
                        $blog->content    = trim(preg_replace("/\r|\n/", ' ', $blog->content));

                        // Tags formatting
                        $blog->tags = collect(
                            is_string($blog->tags) ? explode(',', $blog->tags) : []
                        )
                            ->map(fn($tag) => '#' . ltrim(trim($tag), '#'))
                            ->values()
                            ->toArray();

                        return $blog->makeHidden(['updated_at', 'slug']);
                    });

                return $this->sendResponse(true, 'Blog search results found', [
                    'type'    => 'blog',
                    'results' => $results,
                ]);
            }

            /* ================= PRODUCT SEARCH ================= */
            // if ($type === 'product') {

            //     $results = Product::where('status', 1)
            //         ->where(function ($q) use ($query) {
            //             $q->where('name', 'LIKE', "%{$query}%")
            //                 ->orWhere('description', 'LIKE', "%{$query}%");
            //         })
            //         ->orderBy('id', 'desc')
            //         ->limit(20)
            //         ->get();

            //     return $this->sendResponse(true, 'Product search results found', [
            //         'type'    => 'product',
            //         'results' => $results,
            //     ]);
            // }
        } catch (Exception $e) {
            Log::error('error in getItemsSearch', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }
}
