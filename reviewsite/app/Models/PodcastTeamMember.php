<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastTeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'podcast_id',
        'user_id',
        'role',
        'invited_at',
        'accepted_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    // Relationships
    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeOwners($query)
    {
        return $query->where('role', 'owner');
    }

    public function scopeMembers($query)
    {
        return $query->where('role', 'member');
    }

    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at');
    }

    // Helper methods
    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isMember()
    {
        return $this->role === 'member';
    }

    public function isPending()
    {
        return is_null($this->accepted_at);
    }

    public function isAccepted()
    {
        return !is_null($this->accepted_at);
    }

    public function accept()
    {
        $this->update(['accepted_at' => now()]);
    }

    public function canPostReviews()
    {
        return $this->isAccepted() && $this->podcast->canPostReviews();
    }

    public function canManageTeam()
    {
        return $this->isOwner();
    }

    public function getDisplayRoleAttribute()
    {
        return ucfirst($this->role);
    }

    public function getStatusAttribute()
    {
        if ($this->isPending()) {
            return 'Pending';
        }

        return 'Active';
    }

    // Role constants
    const ROLE_OWNER = 'owner';
    const ROLE_MEMBER = 'member';

    public static function getRoles()
    {
        return [
            self::ROLE_OWNER => 'Owner',
            self::ROLE_MEMBER => 'Member',
        ];
    }
} 