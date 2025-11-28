<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use Exception;
use Illuminate\Support\Facades\Log;

class HomePageController extends ApiBaseController
{
    public function getHomePageData()
    {
        try {
            $blogs = Blog::where('status', 1)->get()->map(function ($blog) {
                $blog->image = $blog->image
                    ? asset($blog->image)
                    : null;

                return $blog->makeHidden(['created_at', 'updated_at']);
            });

            return $this->sendResponse(true, 'Home Page data found', ['blogs' => $blogs]);
        } catch (Exception $e) {
            Log::error('error in getBlogs', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }
}
