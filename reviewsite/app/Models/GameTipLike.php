<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameTipLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_tip_id',
        'user_id'
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
}
