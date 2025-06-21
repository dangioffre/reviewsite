<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
