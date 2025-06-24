<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListFollower extends Model
{
    use HasFactory;

    protected $fillable = ['list_id', 'user_id'];

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($follower) {
            $follower->list->increment('followers_count');
        });
        
        static::deleted(function ($follower) {
            $follower->list->decrement('followers_count');
        });
    }
}
