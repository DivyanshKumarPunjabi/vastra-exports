<?php

namespace App\Console\Commands;

use App\Models\GoogleReview;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncGoogleReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-google-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/place/details/json',
            [
                'place_id' => env('GOOGLE_PLACE_ID'),
                'fields'   => 'name,rating,reviews',
                'key'      => env('GOOGLE_API_KEY'),
            ]
        );

        $reviews = $response['result']['reviews'] ?? [];

        foreach ($reviews as $review) {
            GoogleReview::updateOrCreate(
                ['google_review_id' => $review['time']],
                [
                    'author_name' => $review['author_name'],
                    'author_url'  => $review['author_url'] ?? null,
                    'profile_photo_url'  => $review['profile_photo_url'] ?? null,
                    'rating'      => $review['rating'],
                    'review_text' => $review['text'] ?? null,
                    'review_time' => now()->setTimestamp($review['time']),
                ]
            );
        }
    }
}
