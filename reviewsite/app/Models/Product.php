<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'image',
        'video',
        'staff_review',
        'staff_rating',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the staff review for the product.
     */
    public function staffReview()
    {
        return $this->hasOne(Review::class)->where('is_staff_review', true);
    }

    /**
     * Get all user reviews for the product.
     */
    public function userReviews()
    {
        return $this->hasMany(Review::class)->where('is_staff_review', false);
    }

    /**
     * Get the full URL for the product's image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
