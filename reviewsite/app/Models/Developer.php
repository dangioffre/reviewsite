<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Developer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'country',
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

        static::creating(function ($developer) {
            if (empty($developer->slug)) {
                $developer->slug = static::generateUniqueSlug($developer->name);
            }
        });

        static::updating(function ($developer) {
            if ($developer->isDirty('name') && empty($developer->slug)) {
                $developer->slug = static::generateUniqueSlug($developer->name);
            }
        });
    }

    /**
     * Generate a unique slug for the developer.
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
     * Scope for active developers.
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
     * Get all products that have this developer.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'developer_product');
    }
}
