<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'google_reviews';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    protected $fillable = [
        'google_review_id',
        'author_name',
        'author_url',
        'rating',
        'review_text',
        'review_time',
        'profile_photo_url',
    ];

    public function getRatingStars()
    {
        $rating = (int) $this->rating;
        $maxStars = 5;

        $stars = '<span class="d-inline-flex gap-1">';

        for ($i = 1; $i <= $maxStars; $i++) {
            $stars .= $i <= $rating
                ? '<i class="la la-star text-warning"></i>'
                : '<i class="la la-star text-muted"></i>';
        }

        $stars .= '</span>';

        return $stars;
    }
}
