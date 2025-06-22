<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model and set up event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($theme) {
            if (empty($theme->slug)) {
                $theme->slug = static::generateUniqueSlug($theme->name);
            }
        });

        static::updating(function ($theme) {
            if ($theme->isDirty('name') && empty($theme->slug)) {
                $theme->slug = static::generateUniqueSlug($theme->name);
            }
        });
    }

    /**
     * Generate a unique slug for the theme.
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
     * Scope for active themes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get all products that have this theme.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_theme');
    }
}
