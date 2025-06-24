<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListCollaborator extends Model
{
    use HasFactory;

    protected $fillable = [
        'list_id', 
        'user_id', 
        'invited_by_owner',
        'can_add_games',
        'can_delete_games',
        'can_rename_list',
        'can_manage_users',
        'can_change_privacy',
        'can_change_category',
        'invited_at', 
        'accepted_at'
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
        'invited_by_owner' => 'boolean',
        'can_add_games' => 'boolean',
        'can_delete_games' => 'boolean',
        'can_rename_list' => 'boolean',
        'can_manage_users' => 'boolean',
        'can_change_privacy' => 'boolean',
        'can_change_category' => 'boolean',
    ];

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accept()
    {
        $this->update(['accepted_at' => now()]);
    }

    public function isPending()
    {
        return is_null($this->accepted_at);
    }

    public function isAccepted()
    {
        return !is_null($this->accepted_at);
    }

    // Legacy compatibility methods - deprecated but kept for backward compatibility
    public function canEdit()
    {
        return $this->can_add_games || $this->can_delete_games;
    }

    public function canAdmin()
    {
        return $this->can_manage_users;
    }

    // New granular permission methods
    public function canAddGames()
    {
        return $this->can_add_games;
    }

    public function canDeleteGames()
    {
        return $this->can_delete_games;
    }

    public function canRenameList()
    {
        return $this->can_rename_list;
    }

    public function canManageUsers()
    {
        return $this->can_manage_users;
    }

    public function canChangePrivacy()
    {
        return $this->can_change_privacy;
    }

    public function canChangeCategory()
    {
        return $this->can_change_category;
    }

    // Helper method to get permission summary
    public function getPermissionSummary()
    {
        $permissions = [];
        if ($this->can_add_games) $permissions[] = 'Add Games';
        if ($this->can_delete_games) $permissions[] = 'Delete Games';
        if ($this->can_rename_list) $permissions[] = 'Rename List';
        if ($this->can_manage_users) $permissions[] = 'Manage Users';
        if ($this->can_change_privacy) $permissions[] = 'Change Privacy';
        if ($this->can_change_category) $permissions[] = 'Change Category';
        
        return empty($permissions) ? 'View Only' : implode(', ', $permissions);
    }

    // Helper method to set permissions from an array
    public function setPermissions(array $permissions)
    {
        $this->update([
            'can_add_games' => in_array('can_add_games', $permissions),
            'can_delete_games' => in_array('can_delete_games', $permissions),
            'can_rename_list' => in_array('can_rename_list', $permissions),
            'can_manage_users' => in_array('can_manage_users', $permissions),
            'can_change_privacy' => in_array('can_change_privacy', $permissions),
            'can_change_category' => in_array('can_change_category', $permissions),
        ]);
    }
}
