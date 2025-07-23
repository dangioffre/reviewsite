<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgeRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($rating) {
            if (empty($rating->slug)) {
                $rating->slug = Str::slug($rating->name);
            }
        });
    }
} 