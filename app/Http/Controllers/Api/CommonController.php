<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Product;
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
            $query = trim($request->input('query', ''));
            $query = trim($query);
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
            if ($type === 'product') {

                $results = Product::where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%");
                })
                    ->orderBy('id', 'desc')
                    ->limit(20)
                    ->get();

                return $this->sendResponse(true, 'Product search results found', [
                    'type'    => 'product',
                    'results' => $results,
                ]);
            }
        } catch (Exception $e) {
            Log::error('error in getItemsSearch', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }

    // public function getAllProducts()
    // {
    //     try {
    //         $products = Product::with('category:id,name')
    //             ->orderBy('lft', 'ASC')
    //             ->get()
    //             ->map(function ($product) {
    //                 $product->category_name = $product->category->name ?? null;

    //                 // hide unwanted fields
    //                 $product->makeHidden([
    //                     'created_at',
    //                     'updated_at',
    //                     'parent_id',
    //                     'lft',
    //                     'rgt',
    //                     'depth',
    //                     'category',
    //                 ]);

    //                 return $product;
    //             });

    //         return $this->sendResponse(true, 'Products data found', [
    //             'products_data' => $products
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error('error in getAllProducts', [
    //             'message' => $e->getMessage()
    //         ]);

    //         return $this->sendCatchLog($e->getMessage());
    //     }
    // }

    public function getAllProducts()
    {
        try {
            $products = Product::with('category:id,name')
                ->orderBy('lft', 'ASC')
                ->get()
                ->map(function ($product) {

                    // ✅ Category name
                    $product->category_name = $product->category->name ?? null;

                    // ✅ Images as array with full URL
                    $images = [];

                    foreach (['image', 'image_1', 'image_2', 'image_3', 'image_4'] as $field) {
                        if (!empty($product->$field)) {
                            $images[] = asset($product->$field);
                        }
                    }

                    $product->images = $images;

                    // ❌ Remove old image fields + unwanted columns
                    $product->makeHidden([
                        'image',
                        'image_1',
                        'image_2',
                        'image_3',
                        'image_4',
                        'created_at',
                        'updated_at',
                        'parent_id',
                        'lft',
                        'rgt',
                        'depth',
                        'category',
                    ]);

                    return $product;
                });

            return $this->sendResponse(true, 'Products data found', [
                'products_data' => $products
            ]);
        } catch (Exception $e) {
            Log::error('error in getAllProducts', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }


    // public function getProductDetails(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'product_id' => 'required|exists:products,id',
    //         ], [
    //             'product_id.required' => 'Product ID is required.',
    //             'product_id.exists'   => 'Product not found.',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->sendResponse(false, $validator->errors()->first());
    //         }

    //         $product = Product::with('category:id,name')
    //             ->where('id', $request->product_id)
    //             ->first();

    //         $relatedProducts = Product::where('category_id', $product->category_id)
    //             ->with('category:id,name')
    //             ->where('id', '!=', $product->id)
    //             ->select('id', 'title', 'category_id')
    //             ->get();

    //         $productData = $product->makeHidden([
    //             'created_at',
    //             'updated_at',
    //             'parent_id',
    //             'lft',
    //             'rgt',
    //             'depth',
    //             'category',
    //             'gsm',
    //             'moq',
    //         ]);

    //         $productData->category_name = $product->category->name ?? null;

    //         return $this->sendResponse(true, 'Product data found', [
    //             'product'          => $productData,
    //             'related_products' => $relatedProducts,
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error('error in getProductDetails', ['message' => $e->getMessage()]);
    //         return $this->sendCatchLog($e->getMessage());
    //     }
    // }

    public function getProductDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ], [
                'product_id.required' => 'Product ID is required.',
                'product_id.exists'   => 'Product not found.',
            ]);

            if ($validator->fails()) {
                return $this->sendResponse(false, $validator->errors()->first());
            }

            // 🔹 Main Product
            $product = Product::with('category:id,name')
                ->where('id', $request->product_id)
                ->first();

            if (!$product) {
                return $this->sendResponse(false, 'Product not found.');
            }

            // 🔹 Format main product
            $product = $this->formatProduct($product);

            // 🔹 Related products (same category)
            $relatedProducts = Product::with('category:id,name')
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->get()
                ->map(function ($item) {
                    return $this->formatProduct($item);
                });

            return $this->sendResponse(true, 'Product data found', [
                'product'          => $product,
                'related_products' => $relatedProducts,
            ]);
        } catch (\Exception $e) {
            Log::error('error in getProductDetails', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }

    private function formatProduct($product)
    {
        // Category name
        $product->category_name = $product->category->name ?? null;

        // Images array with full URL
        $images = [];
        foreach (['image', 'image_1', 'image_2', 'image_3', 'image_4'] as $field) {
            if (!empty($product->$field)) {
                $images[] = asset($product->$field);
            }
        }

        $product->images = $images;

        // Hide unwanted fields
        $product->makeHidden([
            'image',
            'image_1',
            'image_2',
            'image_3',
            'image_4',
            'created_at',
            'updated_at',
            'parent_id',
            'lft',
            'rgt',
            'depth',
            'category',
            'gsm',
            'moq',
            'stock_status'
        ]);

        return $product;
    }
}
