<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewCommentReport extends Model
{
    protected $fillable = [
        'review_comment_id',
        'user_id',
        'reason',
        'details',
        'resolved',
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
