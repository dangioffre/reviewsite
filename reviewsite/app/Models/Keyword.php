<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($keyword) {
            if (empty($keyword->slug)) {
                $keyword->slug = Str::slug($keyword->name);
            }
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'keyword_product');
    }
} 