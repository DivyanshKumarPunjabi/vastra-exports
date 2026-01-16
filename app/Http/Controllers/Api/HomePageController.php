<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use App\Models\Blog;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Log;

class HomePageController extends ApiBaseController
{
    public function getHomePageData()
    {
        try {
            $blogs = Blog::where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(6)
                ->get()
                ->map(function ($blog) {

                    if (!$blog) {
                        return null;
                    }

                    $blog->image = $blog->image
                        ? asset($blog->image)
                        : null;

                    $blog->short_desc = trim(preg_replace("/\r|\n/", ' ', $blog->short_desc));
                    $blog->content    = trim(preg_replace("/\r|\n/", ' ', $blog->content));

                    // ✅ Always initialize tags
                    $blog->tags = collect(
                        is_string($blog->tags) ? explode(',', $blog->tags) : []
                    )
                        ->map(fn($tag) => '#' . ltrim(trim($tag), '#'))
                        ->values()
                        ->toArray();

                    return $blog->makeHidden(['updated_at', 'slug']);
                })
                ->filter(); // remove nulls

            $banners = Banner::where('status', 1)->orderBy('id', 'desc')->get()->map(function ($banner) {
                $banner->image = $banner->image
                    ? asset($banner->image)
                    : null;

                $banner->short_descp = trim(preg_replace("/\r|\n/", ' ', $banner->short_descp));
                $banner->long_description = trim(preg_replace("/\r|\n/", ' ', $banner->long_description));

                return $banner->makeHidden(['created_at', 'updated_at']);
            });
            $setting = Setting::all();
            // Exact coordinates from your screenshot
            $lat = 26.9831516;
            $long = 75.7773922;
            /**
             * 1. Embed URL: This goes into <iframe src="...">
             * The 'output=embed' part is what makes it work inside a frame.
             */
            $mapEmbedUrl = "https://maps.google.com/maps?q=$lat,$long&output=embed";
            /**
             * 2. View URL: This goes into <a href="...">
             * This opens the full Google Maps site for the user.
             */
            $mapViewUrl = "https://www.google.com/maps?q=$lat,$long";
            $setting->makeHidden(['field', 'active', 'created_at', 'updated_at']);
            return $this->sendResponse(true, 'Home Page data found', ['blogs' => $blogs, 'banners' => $banners, 'setting' => $setting, 'map' => [
                'embed_url' => $mapEmbedUrl,   // ✅ for iframe
                'view_url'  => $mapViewUrl     // ✅ for click
            ]]);
        } catch (Exception $e) {
            Log::error('error in getBlogs', ['message' => $e->getMessage()]);
            return $this->sendCatchLog($e->getMessage());
        }
    }
}
