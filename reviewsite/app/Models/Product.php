<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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
        'esrb_rating_id',
        'pegi_rating_id',
        'official_website',
        'affiliate_links',
        'type',
        'is_featured',
        'genre_id',
        'platform_id',
        'genre_ids',
        'platform_ids',
    ];

    protected $casts = [
        'release_date' => 'date',
        'photos' => 'array',
        'videos' => 'array',
        'affiliate_links' => 'array',
        'is_featured' => 'boolean',
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

        // Image resizing/compression for main image and photos
        static::saving(function ($product) {
            // Main image (local upload only)
            if ($product->isDirty('image') && $product->image && !Str::startsWith($product->image, ['http://', 'https://'])) {
                $path = storage_path('app/public/' . $product->image);
                if (file_exists($path)) {
                    $img = Image::make($path)
                        ->resize(1200, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', 80); // 80% quality
                    $img->save($path);
                }
            }
            // Photos (repeater uploads)
            if (is_array($product->photos)) {
                $photos = $product->photos;
                $changed = false;
                
                foreach ($photos as $key => $photo) {
                    if (!empty($photo['upload']) && !Str::startsWith($photo['upload'], ['http://', 'https://'])) {
                        $photoPath = storage_path('app/public/' . $photo['upload']);
                        if (file_exists($photoPath)) {
                            $img = Image::make($photoPath)
                                ->resize(1000, 700, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                ->encode('jpg', 80);
                            $img->save($photoPath);
                            $changed = true;
                        }
                    }
                }
                
                if ($changed) {
                    $product->photos = $photos; // re-assign the modified array
                }
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

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Many-to-many relationship with Genre.
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_product');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * Many-to-many relationship with Platform.
     */
    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'platform_product');
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
     * Many-to-many relationship with PlayerPerspective.
     */
    public function playerPerspectives()
    {
        return $this->belongsToMany(PlayerPerspective::class, 'player_perspective_product');
    }

    /**
     * Many-to-many relationship with Keyword.
     */
    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'keyword_product');
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
     * Get the image URL attribute.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            } else {
                return Storage::url($this->image);
            }
        }
        return null;
    }

    public function esrbRating()
    {
        return $this->belongsTo(AgeRating::class, 'esrb_rating_id')->where('type', 'esrb');
    }
    public function pegiRating()
    {
        return $this->belongsTo(AgeRating::class, 'pegi_rating_id')->where('type', 'pegi');
    }

    public function gameTips()
    {
        return $this->hasMany(GameTip::class);
    }
}
