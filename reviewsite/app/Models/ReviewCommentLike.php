<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewCommentLike extends Model
{
    protected $fillable = [
        'review_comment_id',
        'user_id',
    ];

    public function comment()
    {
        return $this->belongsTo(ReviewComment::class, 'review_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
