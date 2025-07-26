<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTip extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'game_tip_category_id',
        'title',
        'content',
        'youtube_link',
        'tags',
        'status',
        'likes_count',
        'comments_count'
    ];

    protected $casts = [
        'tags' => 'array',
        'likes_count' => 'integer',
        'comments_count' => 'integer'
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GameTipCategory::class, 'game_tip_category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(GameTipComment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(GameTipLike::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Methods
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getYoutubeVideoId(): ?string
    {
        if (!$this->youtube_link) {
            return null;
        }

        // Extract video ID from various YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->youtube_link, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public function getEmbedUrl(): ?string
    {
        $videoId = $this->getYoutubeVideoId();
        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
    }
}
