<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'list_id', 'user_id', 'parent_id', 'content', 'likes_count'
    ];

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ListComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ListComment::class, 'parent_id')->with('user', 'replies');
    }

    public function likes()
    {
        return $this->hasMany(ListCommentLike::class, 'comment_id');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function toggleLike($userId)
    {
        $like = $this->likes()->where('user_id', $userId)->first();
        
        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            return false;
        } else {
            $this->likes()->create(['user_id' => $userId]);
            $this->increment('likes_count');
            return true;
        }
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($comment) {
            if (is_null($comment->parent_id)) {
                $comment->list->increment('comments_count');
            }
        });
        
        static::deleted(function ($comment) {
            if (is_null($comment->parent_id)) {
                $comment->list->decrement('comments_count');
            }
        });
    }
}

// Also create the ListCommentLike model
class ListCommentLike extends Model
{
    use HasFactory;

    protected $fillable = ['comment_id', 'user_id'];

    public function comment()
    {
        return $this->belongsTo(ListComment::class, 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
