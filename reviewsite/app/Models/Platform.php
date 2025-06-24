<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Platform extends Model
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($platform) {
            if (!$platform->slug) {
                $platform->slug = Str::slug($platform->name);
            }
        });

        static::updating(function ($platform) {
            if ($platform->isDirty('name') && !$platform->isDirty('slug')) {
                $platform->slug = Str::slug($platform->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
