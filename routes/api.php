<?php

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Api\HomePageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('home-page', [HomePageController::class, 'getHomePageData']);

Route::post('support/enquiry-submit', [EnquiryController::class, 'submitEnquiry']);
Route::get('get-all-blogs', [CommonController::class, 'getAllBlogs']);
Route::get('get-all-product-categories', [CommonController::class, 'getProductCategories']);
Route::post('get-blog-details',[CommonController::class, 'getBlogDetails']);
Route::post('search-items',[CommonController::class, 'getItemsSearch']);
// Route::get('get-all-product',[CommonController::class, 'getAllProducts']);
Route::get('get-all-product',[CommonController::class, 'getAllProductsV2']);
Route::get('get-all-product-v2',[CommonController::class, 'getAllProductsV2']);
Route::post('get-product-details',[CommonController::class, 'getProductDetails']);
Route::get('get-all-reviews',[CommonController::class, 'getAllReviews']);