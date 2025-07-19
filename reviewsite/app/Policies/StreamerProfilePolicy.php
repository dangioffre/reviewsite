<?php

namespace App\Policies;

use App\Models\StreamerProfile;
use App\Models\User;

class StreamerProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StreamerProfile $streamerProfile): bool
    {
        return $streamerProfile->is_approved || $user->id === $streamerProfile->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canCreateStreamerProfile();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StreamerProfile $streamerProfile): bool
    {
        return $user->id === $streamerProfile->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StreamerProfile $streamerProfile): bool
    {
        return $user->id === $streamerProfile->user_id;
    }
}