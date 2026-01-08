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
            $getCategories = ProductCategory::get();
            $getCategories->makeHidden(['created_at', 'updated_at']);

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

            $blog->tags = collect(
                is_string($blog->tags) ? explode(',', $blog->tags) : []
            )
                ->map(fn($tag) => '#' . ltrim(trim($tag), '#'))
                ->values()
                ->toArray();
            $blog->makeHidden(['updated_at', 'slug']);

            return $this->sendResponse(true, 'Blog details found', [
                'blog' => $blog,
            ]);
        } catch (Exception $e) {
            Log::error('error in getBlogDetails', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }
}
