<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'review',
        'rating',
        'is_staff_review',
    ];

    protected $casts = [
        'is_staff_review' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStaff($query)
    {
        return $query->where('is_staff_review', true);
    }

    public function scopeUser($query)
    {
        return $query->where('is_staff_review', false);
    }
}
