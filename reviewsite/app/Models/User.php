<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function likedReviews()
    {
        return $this->belongsToMany(\App\Models\Review::class, 'review_likes')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function gameStatuses()
    {
        return $this->hasMany(GameUserStatus::class);
    }

    public function lists()
    {
        return $this->hasMany(ListModel::class);
    }

    public function podcasts()
    {
        return $this->hasMany(Podcast::class, 'owner_id');
    }

    public function podcastTeamMemberships()
    {
        return $this->hasMany(PodcastTeamMember::class);
    }

    public function activePodcastTeamMemberships()
    {
        return $this->podcastTeamMemberships()->whereNotNull('accepted_at');
    }

    public function pendingPodcastInvitations()
    {
        return $this->podcastTeamMemberships()->whereNull('accepted_at');
    }

    public function podcastReviews()
    {
        return $this->reviews()->whereNotNull('podcast_id');
    }

    public function canPostAsPodcast(Podcast $podcast)
    {
        return $podcast->userCanPostAsThisPodcast($this);
    }

    public function getAvailablePodcastsForReviews()
    {
        return $this->podcasts()
            ->approved()
            ->union(
                Podcast::whereHas('activeTeamMembers', function ($query) {
                    $query->where('user_id', $this->id);
                })->approved()
            )
            ->get();
    }

    public function streamerProfile()
    {
        return $this->hasOne(StreamerProfile::class);
    }

    public function followedStreamers()
    {
        return $this->belongsToMany(StreamerProfile::class, 'streamer_followers')
                    ->withPivot('notification_preferences')
                    ->withTimestamps();
    }

    public function isStreamer(): bool
    {
        return $this->streamerProfile !== null;
    }

    public function canCreateStreamerProfile(): bool
    {
        return $this->streamerProfile === null;
    }

    public function reviewComments()
    {
        return $this->hasMany(ReviewComment::class);
    }

    public function gameTips()
    {
        return $this->hasMany(GameTip::class);
    }
}
