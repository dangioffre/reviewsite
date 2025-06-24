<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ListModel extends Model
{
    use HasFactory;
    protected $table = 'lists';
    protected $fillable = [
        'user_id', 'name', 'slug', 'is_public', 'category', 'sort_by', 'sort_direction',
        'cloned_from', 'allow_collaboration', 'allow_comments', 'followers_count', 'comments_count'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'allow_collaboration' => 'boolean',
        'allow_comments' => 'boolean',
    ];

    protected $attributes = [
        'sort_by' => 'date_added',
        'sort_direction' => 'desc',
        'is_public' => false,
        'allow_collaboration' => false,
        'allow_comments' => true,
        'category' => 'general',
    ];

    // Categories available for lists
    public static $categories = [
        'general' => 'General',
        'wishlist' => 'Wishlist',
        'completed' => 'Completed',
        'playing' => 'Currently Playing',
        'favorites' => 'Favorites',
        'multiplayer' => 'Multiplayer',
        'singleplayer' => 'Single Player',
        'indie' => 'Indie Games',
        'retro' => 'Retro/Classic',
        'upcoming' => 'Upcoming'
    ];

    // Sort options available
    public static $sortOptions = [
        'date_added' => 'Date Added',
        'name' => 'Name',
        'rating' => 'Rating',
        'release_date' => 'Release Date',
        'manual' => 'Manual Order'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        $query = $this->hasMany(ListItem::class, 'list_id');
        
        // Apply sorting based on list preferences
        switch ($this->sort_by) {
            case 'name':
                $query->join('products', 'list_items.product_id', '=', 'products.id')
                      ->orderBy('products.name', $this->sort_direction);
                break;
            case 'rating':
                $query->join('products', 'list_items.product_id', '=', 'products.id')
                      ->leftJoin('reviews', function($join) {
                          $join->on('products.id', '=', 'reviews.product_id')
                               ->where('reviews.is_staff_review', true);
                      })
                      ->orderBy('reviews.rating', $this->sort_direction);
                break;
            case 'release_date':
                $query->join('products', 'list_items.product_id', '=', 'products.id')
                      ->orderBy('products.release_date', $this->sort_direction);
                break;
            case 'manual':
                $query->orderBy('sort_order', 'asc');
                break;
            default: // date_added
                $query->orderBy('created_at', $this->sort_direction);
        }
        
        return $query;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'list_items', 'list_id', 'product_id');
    }

    // New relationships for enhanced features
    public function collaborators()
    {
        return $this->hasMany(ListCollaborator::class, 'list_id');
    }

    public function activeCollaborators()
    {
        return $this->collaborators()->whereNotNull('accepted_at');
    }

    public function followers()
    {
        return $this->hasMany(ListFollower::class, 'list_id');
    }

    public function comments()
    {
        return $this->hasMany(ListComment::class, 'list_id')->whereNull('parent_id')->with('replies', 'user');
    }

    public function clonedFrom()
    {
        return $this->belongsTo(ListModel::class, 'cloned_from');
    }

    public function clones()
    {
        return $this->hasMany(ListModel::class, 'cloned_from');
    }

    // Helper methods
    public function isFollowedBy($user)
    {
        // Handle both user objects and user IDs
        $userId = is_object($user) ? $user->id : $user;
        return $this->followers()->where('user_id', $userId)->exists();
    }

    public function canUserEdit($user)
    {
        $userId = is_object($user) ? $user->id : $user;
        if ($this->user_id == $userId) return true;
        
        return $this->collaborators()
            ->where('user_id', $userId)
            ->whereNotNull('accepted_at')
            ->whereIn('permission', ['edit', 'admin'])
            ->exists();
    }

    public function canUserView($user)
    {
        $userId = is_object($user) ? $user->id : $user;
        if ($this->is_public) return true;
        if ($this->user_id == $userId) return true;
        
        return $this->collaborators()
            ->where('user_id', $userId)
            ->whereNotNull('accepted_at')
            ->exists();
    }

    public function getCategoryLabelAttribute()
    {
        return self::$categories[$this->category] ?? ucfirst($this->category);
    }

    public function getSortLabelAttribute()
    {
        return self::$sortOptions[$this->sort_by] ?? ucfirst(str_replace('_', ' ', $this->sort_by));
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(6);
            }
            // Ensure sort_direction is valid
            if (!in_array($model->sort_direction, ['asc', 'desc'])) {
                $model->sort_direction = 'desc';
            }
        });
        
        static::updating(function ($model) {
            // Ensure sort_direction is valid
            if (!in_array($model->sort_direction, ['asc', 'desc'])) {
                $model->sort_direction = 'desc';
            }
        });
    }
} 