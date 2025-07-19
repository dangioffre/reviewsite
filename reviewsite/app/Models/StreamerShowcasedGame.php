<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamerShowcasedGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'streamer_profile_id',
        'game_user_status_id',
        'display_order',
        'showcase_note',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Get the streamer profile that owns this showcased game.
     */
    public function streamerProfile(): BelongsTo
    {
        return $this->belongsTo(StreamerProfile::class);
    }

    /**
     * Get the game user status (collection entry) being showcased.
     */
    public function gameUserStatus(): BelongsTo
    {
        return $this->belongsTo(GameUserStatus::class);
    }



    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get the next display order for a streamer.
     */
    public static function getNextDisplayOrder(int $streamerProfileId): int
    {
        return static::where('streamer_profile_id', $streamerProfileId)
            ->max('display_order') + 1;
    }

    /**
     * Reorder showcased games after deletion or reordering.
     */
    public static function reorderForStreamer(int $streamerProfileId): void
    {
        $showcasedGames = static::where('streamer_profile_id', $streamerProfileId)
            ->ordered()
            ->get();

        foreach ($showcasedGames as $index => $game) {
            $game->update(['display_order' => $index + 1]);
        }
    }
}
