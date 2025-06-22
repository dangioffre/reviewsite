<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'story',
        'image',
        'video_url',
        'photos',
        'videos',
        'release_date',
        'developer',
        'publisher',
        'game_modes',
        'theme',
        'type',
        'genre_id',
        'platform_id',
        'hardware_id',
        'genres',
        'platforms',
        'developers',
        'publishers',
        'themes',
        'game_modes_list',
    ];

    protected $casts = [
        'release_date' => 'date',
        'photos' => 'array',
        'videos' => 'array',
        'genres' => 'array',
        'platforms' => 'array',
        'developers' => 'array',
        'publishers' => 'array',
        'themes' => 'array',
        'game_modes_list' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Boot the model and set up event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });
    }

    /**
     * Generate a unique slug for the product.
     */
    public static function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function hardware()
    {
        return $this->belongsTo(Hardware::class);
    }

    /**
     * Many-to-many relationship with GameMode.
     */
    public function gameModes()
    {
        return $this->belongsToMany(GameMode::class, 'game_mode_product');
    }

    /**
     * Many-to-many relationship with Developer.
     */
    public function developers()
    {
        return $this->belongsToMany(Developer::class, 'developer_product');
    }

    /**
     * Many-to-many relationship with Publisher.
     */
    public function publishers()
    {
        return $this->belongsToMany(Publisher::class, 'product_publisher');
    }

    /**
     * Many-to-many relationship with Theme.
     */
    public function themes()
    {
        return $this->belongsToMany(Theme::class, 'product_theme');
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
     * Get the staff rating (average of all staff reviews).
     */
    public function getStaffRatingAttribute()
    {
        return $this->reviews()->where('is_staff_review', true)->avg('rating');
    }

    /**
     * Get the community rating (average of all user reviews).
     */
    public function getCommunityRatingAttribute()
    {
        return $this->reviews()->where('is_staff_review', false)->avg('rating');
    }

    /**
     * Get the count of community reviews.
     */
    public function getCommunityReviewsCountAttribute()
    {
        return $this->reviews()->where('is_staff_review', false)->count();
    }

    /**
     * Get the count of staff reviews.
     */
    public function getStaffReviewsCountAttribute()
    {
        return $this->reviews()->where('is_staff_review', true)->count();
    }

    /**
     * Get the full URL for the product's image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
