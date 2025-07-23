<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlayerPerspective extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($perspective) {
            if (empty($perspective->slug)) {
                $perspective->slug = Str::slug($perspective->name);
            }
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'player_perspective_product');
    }
} 