<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GameMode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'type',
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

        static::creating(function ($gameMode) {
            if (empty($gameMode->slug)) {
                $gameMode->slug = static::generateUniqueSlug($gameMode->name);
            }
        });

        static::updating(function ($gameMode) {
            if ($gameMode->isDirty('name') && empty($gameMode->slug)) {
                $gameMode->slug = static::generateUniqueSlug($gameMode->name);
            }
        });
    }

    /**
     * Generate a unique slug for the game mode.
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
     * Scope for active game modes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for game game modes.
     */
    public function scopeGame($query)
    {
        return $query->where('type', 'game');
    }

    /**
     * Scope for hardware game modes.
     */
    public function scopeHardware($query)
    {
        return $query->where('type', 'hardware');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get all products that have this game mode.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'game_mode_product');
    }
}
