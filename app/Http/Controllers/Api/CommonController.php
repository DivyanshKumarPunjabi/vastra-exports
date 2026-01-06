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
            $blogs = Blog::where('status', 1)->orderBy('id', 'desc')->get()->map(function ($blog) {
                $blog->image = $blog->image
                    ? asset($blog->image)
                    : null;

                $blog->short_desc = trim(preg_replace("/\r|\n/", ' ', $blog->short_desc));
                $blog->content    = trim(preg_replace("/\r|\n/", ' ', $blog->content));

                return $blog->makeHidden(['created_at', 'updated_at', 'slug']);
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

            // 3. Transform response
            $blog->image = $blog->image ? asset($blog->image) : null;
            $blog->short_desc = trim(preg_replace("/\r|\n/", ' ', $blog->short_desc));
            $blog->content    = trim(preg_replace("/\r|\n/", ' ', $blog->content));

            $blog->makeHidden(['created_at', 'updated_at']);

            // 4. Success response
            return $this->sendResponse(true, 'Blog details found', [
                'blog' => $blog,
            ]);
        } catch (Exception $e) {
            Log::error('error in getBlogDetails', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }
}
