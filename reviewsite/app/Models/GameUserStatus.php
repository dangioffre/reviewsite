<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class GameUserStatus extends Model
{
    use HasFactory;

    // Completion status constants
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_STARTED = 'started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FULLY_COMPLETED = 'fully_completed';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_REPLAYING = 'replaying';

    // Difficulty constants
    const DIFFICULTY_EASY = 'easy';
    const DIFFICULTY_NORMAL = 'normal';
    const DIFFICULTY_HARD = 'hard';
    const DIFFICULTY_EXTREME = 'extreme';
    const DIFFICULTY_CUSTOM = 'custom';

    protected $fillable = [
        'user_id',
        'product_id',
        'have',
        'want',
        'played',
        'completion_status',
        'hours_played',
        'completion_percentage',
        'started_date',
        'completed_date',
        'notes',
        'rating',
        'is_favorite',
        'platform_played',
        'times_replayed',
        'achievements',
        'difficulty_played',
        'dropped',
        'dropped_date',
        'drop_reason',
    ];

    protected $casts = [
        'have' => 'boolean',
        'want' => 'boolean',
        'played' => 'boolean',
        'is_favorite' => 'boolean',
        'dropped' => 'boolean',
        'started_date' => 'date',
        'completed_date' => 'date',
        'dropped_date' => 'date',
        'achievements' => 'array',
        'times_replayed' => 'integer',
        'hours_played' => 'integer',
        'completion_percentage' => 'integer',
        'rating' => 'integer',
    ];

    /**
     * Get all available completion statuses
     */
    public static function getCompletionStatuses(): array
    {
        return [
            self::STATUS_NOT_STARTED => [
                'label' => 'Not Started',
                'description' => 'Haven\'t started playing yet',
                'color' => '#6B7280',
                'icon' => 'M12 4v16m8-8H4',
            ],
            self::STATUS_STARTED => [
                'label' => 'Just Started',
                'description' => 'Recently began playing',
                'color' => '#3B82F6',
                'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            self::STATUS_IN_PROGRESS => [
                'label' => 'In Progress',
                'description' => 'Currently playing through',
                'color' => '#F59E0B',
                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
            ],
            self::STATUS_COMPLETED => [
                'label' => 'Completed',
                'description' => 'Finished main story/campaign',
                'color' => '#10B981',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            self::STATUS_FULLY_COMPLETED => [
                'label' => '100% Complete',
                'description' => 'Everything done, all achievements',
                'color' => '#8B5CF6',
                'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
            ],
            self::STATUS_ABANDONED => [
                'label' => 'Abandoned',
                'description' => 'Stopped playing, don\'t plan to continue',
                'color' => '#EF4444',
                'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728',
            ],
            self::STATUS_ON_HOLD => [
                'label' => 'On Hold',
                'description' => 'Taking a break, plan to return',
                'color' => '#F97316',
                'icon' => 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            self::STATUS_REPLAYING => [
                'label' => 'Replaying',
                'description' => 'Playing through again',
                'color' => '#06B6D4',
                'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            ],
        ];
    }

    /**
     * Get available difficulty levels
     */
    public static function getDifficultyLevels(): array
    {
        return [
            self::DIFFICULTY_EASY => [
                'label' => 'Easy',
                'color' => '#10B981',
            ],
            self::DIFFICULTY_NORMAL => [
                'label' => 'Normal',
                'color' => '#3B82F6',
            ],
            self::DIFFICULTY_HARD => [
                'label' => 'Hard',
                'color' => '#F59E0B',
            ],
            self::DIFFICULTY_EXTREME => [
                'label' => 'Extreme',
                'color' => '#EF4444',
            ],
            self::DIFFICULTY_CUSTOM => [
                'label' => 'Custom',
                'color' => '#8B5CF6',
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get status details
     */
    public function getStatusDetails(): array
    {
        $statuses = self::getCompletionStatuses();
        return $statuses[$this->completion_status] ?? $statuses[self::STATUS_NOT_STARTED];
    }

    /**
     * Get difficulty details
     */
    public function getDifficultyDetails(): ?array
    {
        if (!$this->difficulty_played) return null;
        
        $difficulties = self::getDifficultyLevels();
        return $difficulties[$this->difficulty_played] ?? null;
    }

    /**
     * Check if game is currently being played
     */
    public function isCurrentlyPlaying(): bool
    {
        return in_array($this->completion_status, [
            self::STATUS_STARTED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REPLAYING
        ]);
    }

    /**
     * Check if game is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this->completion_status, [
            self::STATUS_COMPLETED,
            self::STATUS_FULLY_COMPLETED
        ]);
    }

    /**
     * Get formatted play time
     */
    protected function formattedPlayTime(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->hours_played) return 'Not tracked';
                
                if ($this->hours_played < 1) {
                    return '< 1 hour';
                } elseif ($this->hours_played === 1) {
                    return '1 hour';
                } else {
                    return $this->hours_played . ' hours';
                }
            }
        );
    }

    /**
     * Get completion badge color based on percentage
     */
    public function getCompletionBadgeColor(): string
    {
        if (!$this->completion_percentage) return '#6B7280';
        
        if ($this->completion_percentage >= 100) return '#8B5CF6';
        if ($this->completion_percentage >= 80) return '#10B981';
        if ($this->completion_percentage >= 50) return '#F59E0B';
        if ($this->completion_percentage >= 25) return '#3B82F6';
        
        return '#EF4444';
    }

    /**
     * Scope for games currently being played
     */
    public function scopeCurrentlyPlaying($query)
    {
        return $query->whereIn('completion_status', [
            self::STATUS_STARTED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REPLAYING
        ]);
    }

    /**
     * Scope for completed games
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('completion_status', [
            self::STATUS_COMPLETED,
            self::STATUS_FULLY_COMPLETED
        ]);
    }

    /**
     * Scope for abandoned games
     */
    public function scopeAbandoned($query)
    {
        return $query->where('completion_status', self::STATUS_ABANDONED);
    }

    /**
     * Scope for games on hold
     */
    public function scopeOnHold($query)
    {
        return $query->where('completion_status', self::STATUS_ON_HOLD);
    }

    /**
     * Scope for favorite games
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
} 