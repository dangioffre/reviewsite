<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameTipComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_tip_id',
        'user_id',
        'content',
        'status'
    ];

    // Relationships
    public function tip(): BelongsTo
    {
        return $this->belongsTo(GameTip::class, 'game_tip_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
