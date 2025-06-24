<?php

namespace App\Livewire;

use App\Models\ListModel;
use App\Models\ListCollaborator;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CollaborationManager extends Component
{
    public $list;
    public $inviteEmail = '';
    public $invitePermissions = [
        'can_add_games' => true,
        'can_delete_games' => true,
        'can_rename_list' => false,
        'can_manage_users' => false,
        'can_change_privacy' => false,
        'can_change_category' => false,
    ];
    public $editPermissions = [];
    public $showInviteForm = false;
    public $successMessage = '';
    public $errorMessage = '';

    public function mount(ListModel $list)
    {
        $this->list = $list;
        $this->initializeEditPermissions();
    }

    public function initializeEditPermissions()
    {
        foreach ($this->list->collaborators as $collaborator) {
            $this->editPermissions[$collaborator->id] = [
                'can_add_games' => $collaborator->can_add_games,
                'can_delete_games' => $collaborator->can_delete_games,
                'can_rename_list' => $collaborator->can_rename_list,
                'can_manage_users' => $collaborator->can_manage_users,
                'can_change_privacy' => $collaborator->can_change_privacy,
                'can_change_category' => $collaborator->can_change_category,
            ];
        }
    }

    public function toggleInviteForm()
    {
        $this->showInviteForm = !$this->showInviteForm;
        $this->inviteEmail = '';
        $this->clearMessages();
    }

    public function inviteCollaborator()
    {
        $this->validate([
            'inviteEmail' => 'required|email'
        ]);

        // Check if user owns the list
        if ($this->list->user_id !== Auth::id()) {
            $this->errorMessage = 'You can only invite collaborators to your own lists.';
            return;
        }

        // Find user by email
        $user = User::where('email', $this->inviteEmail)->first();
        
        if (!$user) {
            $this->errorMessage = 'No user found with that email address.';
            return;
        }

        // Check if user is the list owner
        if ($user->id === $this->list->user_id) {
            $this->errorMessage = 'You cannot invite yourself to collaborate on your own list.';
            return;
        }

        // Check if already a collaborator
        $existingCollaboration = $this->list->collaborators()->where('user_id', $user->id)->first();
        
        if ($existingCollaboration) {
            if ($existingCollaboration->isPending()) {
                $this->errorMessage = 'This user already has a pending invitation.';
            } else {
                $this->errorMessage = 'This user is already a collaborator on this list.';
            }
            return;
        }

        // Create invitation (sent by owner)
        $collaborator = $this->list->collaborators()->create([
            'user_id' => $user->id,
            'invited_by_owner' => true,
            'can_add_games' => $this->invitePermissions['can_add_games'],
            'can_delete_games' => $this->invitePermissions['can_delete_games'],
            'can_rename_list' => $this->invitePermissions['can_rename_list'],
            'can_manage_users' => $this->invitePermissions['can_manage_users'],
            'can_change_privacy' => $this->invitePermissions['can_change_privacy'],
            'can_change_category' => $this->invitePermissions['can_change_category'],
            'invited_at' => now(),
        ]);

        // Initialize edit permissions for the new collaborator
        $this->editPermissions[$collaborator->id] = $this->invitePermissions;

        $this->successMessage = "Invitation sent to {$user->name} ({$user->email})!";
        $this->inviteEmail = '';
        $this->showInviteForm = false;
        $this->refreshData();
    }

    public function acceptCollaborationRequest($collaboratorId)
    {
        $collaborator = ListCollaborator::findOrFail($collaboratorId);
        
        if ($collaborator->list->user_id !== Auth::id()) {
            $this->errorMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        $collaborator->accept();
        $this->successMessage = "Accepted collaboration request from {$collaborator->user->name}!";
        $this->refreshData();
    }

    public function rejectCollaborationRequest($collaboratorId)
    {
        $collaborator = ListCollaborator::findOrFail($collaboratorId);
        
        if ($collaborator->list->user_id !== Auth::id()) {
            $this->errorMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        $collaborator->delete();
        $this->successMessage = "Rejected collaboration request from {$collaborator->user->name}.";
        $this->refreshData();
    }

    public function cancelInvitation($collaboratorId)
    {
        $collaborator = ListCollaborator::findOrFail($collaboratorId);
        
        if ($collaborator->list->user_id !== Auth::id()) {
            $this->errorMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        if (!$collaborator->invited_by_owner) {
            $this->errorMessage = 'You can only cancel invitations you sent.';
            return;
        }
        
        $collaborator->delete();
        $this->successMessage = "Invitation to {$collaborator->user->name} has been canceled.";
        $this->refreshData();
    }

    public function removeCollaborator($collaboratorId)
    {
        $collaborator = ListCollaborator::findOrFail($collaboratorId);
        
        if ($collaborator->list->user_id !== Auth::id()) {
            $this->errorMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        $collaborator->delete();
        $this->successMessage = "Removed {$collaborator->user->name} from collaborators.";
        unset($this->editPermissions[$collaboratorId]);
        $this->refreshData();
    }

    public function updateCollaboratorPermissions($collaboratorId)
    {
        $collaborator = ListCollaborator::findOrFail($collaboratorId);
        
        if ($collaborator->list->user_id !== Auth::id()) {
            $this->errorMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        $permissions = $this->editPermissions[$collaboratorId] ?? [];
        
        $collaborator->update([
            'can_add_games' => $permissions['can_add_games'] ?? false,
            'can_delete_games' => $permissions['can_delete_games'] ?? false,
            'can_rename_list' => $permissions['can_rename_list'] ?? false,
            'can_manage_users' => $permissions['can_manage_users'] ?? false,
            'can_change_privacy' => $permissions['can_change_privacy'] ?? false,
            'can_change_category' => $permissions['can_change_category'] ?? false,
        ]);
        
        $this->successMessage = "Updated {$collaborator->user->name}'s permissions.";
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->list = $this->list->fresh(['collaborators.user']);
        $this->initializeEditPermissions();
    }

    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    public function render()
    {
        return view('livewire.collaboration-manager');
    }
} 