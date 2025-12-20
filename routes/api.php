<?php

use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Api\HomePageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/update-club-records', [BlogController::class, 'updateClubRecords']);
Route::get('home-page', [HomePageController::class, 'getHomePageData']);

Route::prefix('support')->group(function () {
    Route::post('enquiry-submit', [EnquiryController::class, 'submitEnquiry']);
});
