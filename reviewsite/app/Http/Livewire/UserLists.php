<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ListModel;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\ListCollaborator;

class UserLists extends Component
{
    public $lists = [];
    public $pendingInvitations = [];
    public $showCreate = false;
    public $newListName = '';
    public $newListDescription = '';
    public $editingList = null;
    public $editingName = '';
    public $viewingList = null;
    public $searchTerm = '';
    public $searchResults = [];
    public $showSearch = false;
    public $successMessage = '';
    
    // New properties for enhanced features
    public $selectedCategory = 'general';
    public $selectedSortBy = 'date_added';
    public $selectedSortDirection = 'desc';
    public $allowCollaboration = false;
    public $allowComments = true;
    
    // Category editing
    public $editingCategoryListId = null;
    public $editingCategoryValue = 'general';
    
    // Description editing
    public $editingDescriptionListId = null;
    public $editingDescriptionValue = '';
    public $showDescriptionModal = false;

    public $showCollaborationManager = false;
    public $managingListId = null;
    public $inviteEmail = '';
    public $invitePermissions = [
        'can_add_games' => true,
        'can_delete_games' => true,
        'can_rename_list' => false,
        'can_manage_users' => false,
        'can_change_privacy' => false,
        'can_change_category' => false,
    ];

    // Delete confirmation modal
    public $showDeleteModal = false;
    public $deletingListId = null;
    public $deletingListName = '';



    protected $rules = [
        'newListName' => 'required|string|max:255',
        'newListDescription' => 'nullable|string|max:1000',
        'editingName' => 'required|string|max:255',
        'editingDescriptionValue' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $this->refreshLists();
        $this->loadPendingInvitations();
    }

    public function loadPendingInvitations()
    {
        // Get invitations sent to the current user that haven't been accepted yet
        $this->pendingInvitations = \App\Models\ListCollaborator::where('user_id', auth()->id())
            ->whereNull('accepted_at')
            ->where('invited_by_owner', true) // Only owner invitations, not user requests
            ->with(['list.user'])
            ->get();
    }

    public function refreshLists()
    {
        // Get user's own lists
        $ownLists = auth()->user()->lists()
            ->withCount(['items', 'followers', 'comments'])
            ->with(['items.product', 'collaborators.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($list) {
                $list->user_role = 'owner';
                return $list;
            });

        // Get lists where user is a collaborator
        $collaborativeLists = \App\Models\ListModel::whereHas('collaborators', function ($query) {
                $query->where('user_id', auth()->id())
                      ->whereNotNull('accepted_at');
            })
            ->withCount(['items', 'followers', 'comments'])
            ->with(['items.product', 'collaborators.user', 'user'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($list) {
                $collaboration = $list->collaborators->where('user_id', auth()->id())->first();
                if ($collaboration) {
                    // Create a role summary based on permissions
                    $permissions = [];
                    if ($collaboration->can_add_games) $permissions[] = 'Add Games';
                    if ($collaboration->can_delete_games) $permissions[] = 'Remove Games';
                    if ($collaboration->can_rename_list) $permissions[] = 'Rename';
                    if ($collaboration->can_manage_users) $permissions[] = 'Manage Users';
                    if ($collaboration->can_change_privacy) $permissions[] = 'Privacy';
                    if ($collaboration->can_change_category) $permissions[] = 'Category';
                    
                    $list->user_role = empty($permissions) ? 'view' : 'collaborator';
                    
                    // Create a more user-friendly summary
                    if (empty($permissions)) {
                        $list->permissions_summary = 'View Only';
                    } elseif (count($permissions) >= 4) {
                        $list->permissions_summary = 'Full Access';
                    } elseif (in_array('Add Games', $permissions) && in_array('Remove Games', $permissions)) {
                        $list->permissions_summary = 'Edit Games';
                    } else {
                        $list->permissions_summary = implode(', ', array_slice($permissions, 0, 2)) . (count($permissions) > 2 ? ' +' . (count($permissions) - 2) . ' more' : '');
                    }
                } else {
                    $list->user_role = 'view';
                    $list->permissions_summary = 'View Only';
                }
                return $list;
            });

        // Combine and sort all lists
        $this->lists = $ownLists->concat($collaborativeLists)
            ->sortByDesc('updated_at')
            ->values();
            
        // Also refresh pending invitations
        $this->loadPendingInvitations();
    }

    public function createList()
    {
        $this->validate([
            'newListName' => 'required|string|max:255',
        ]);

        auth()->user()->lists()->create([
            'name' => $this->newListName,
            'description' => $this->newListDescription,
            'slug' => Str::slug($this->newListName),
            'is_public' => false,
            'category' => $this->selectedCategory,
            'sort_by' => $this->selectedSortBy,
            'sort_direction' => $this->selectedSortDirection,
            'allow_collaboration' => $this->allowCollaboration,
            'allow_comments' => $this->allowComments,
        ]);

        $this->newListName = '';
        $this->newListDescription = '';
        $this->showCreate = false;
        $this->selectedCategory = 'general';
        $this->selectedSortBy = 'date_added';
        $this->selectedSortDirection = 'desc';
        $this->allowCollaboration = false;
        $this->allowComments = true;
        $this->successMessage = 'List created successfully!';
        $this->refreshLists();
    }

    public function startEditing($listId)
    {
        $list = $this->findListById($listId);
        
        if (!$list || !$this->canRenameList($list)) {
            $this->successMessage = 'You do not have permission to rename this list.';
            return;
        }
        
        $this->editingList = $listId;
        $this->editingName = $list->name;
    }

    public function saveEdit()
    {
        $this->validate([
            'editingName' => 'required|string|max:255',
        ]);

        $list = $this->findListById($this->editingList);
        
        if (!$list || !$this->canRenameList($list)) {
            $this->successMessage = 'You do not have permission to rename this list.';
            return;
        }

        $list->update([
            'name' => $this->editingName,
            'slug' => Str::slug($this->editingName),
        ]);

        $this->editingList = null;
        $this->editingName = '';
        $this->successMessage = 'List updated successfully!';
        $this->refreshLists();
    }

    public function cancelEdit()
    {
        $this->editingList = null;
        $this->editingName = '';
    }

    public function togglePublic($listId)
    {
        Log::info('togglePublic called for list ID: ' . $listId);
        
        $list = $this->findListById($listId);
        
        if (!$list || !$this->canChangePrivacy($list)) {
            $this->successMessage = 'You do not have permission to change privacy settings for this list.';
            return;
        }
        
        Log::info('Found list: ' . $list->name . ', current public status: ' . ($list->is_public ? 'true' : 'false'));
        
        $list->update(['is_public' => !$list->is_public]);
        
        $status = $list->is_public ? 'public' : 'private';
        $this->successMessage = "List is now {$status}!";
        Log::info('Updated list to: ' . $status);
        
        $this->refreshLists();
    }

    public function setDeleteTarget($listId)
    {
        $list = $this->findListById($listId);
        if ($list && $list->user_id === auth()->id()) {
            $this->deletingListId = $listId;
            $this->deletingListName = $list->name;
            $this->showDeleteModal = true;
        }
    }

    public function deleteList()
    {
        if (!$this->deletingListId) {
            return;
        }

        $list = $this->findListById($this->deletingListId);
        
        if (!$list || $list->user_id !== auth()->id()) {
            $this->successMessage = 'You can only delete your own lists.';
            $this->closeDeleteModal();
            return;
        }
        
        $listName = $list->name;
        $list->delete();
        
        $this->successMessage = "'{$listName}' has been deleted successfully!";
        $this->refreshLists();
        
        // Close view if we're viewing the deleted list
        if ($this->viewingList == $this->deletingListId) {
            $this->viewingList = null;
        }

        $this->closeDeleteModal();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingListId = null;
        $this->deletingListName = '';
    }

    public function viewList($listId)
    {
        $this->viewingList = $listId;
        $this->searchTerm = '';
        $this->searchResults = [];
        $this->showSearch = false;
    }

    public function closeView()
    {
        $this->viewingList = null;
        $this->searchTerm = '';
        $this->searchResults = [];
        $this->showSearch = false;
    }

    public function copyPublicLink($listId)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        $this->successMessage = "Public link copied to clipboard!";
        
        // The actual copying will be handled by JavaScript
        $this->dispatch('copy-to-clipboard', [
            'text' => route('lists.public', $list->slug)
        ]);
    }

    public function searchGames()
    {
        if (strlen($this->searchTerm) < 2) {
            $this->searchResults = [];
            return;
        }

        try {
            $this->searchResults = Product::where('type', 'game')
                ->where(function($query) {
                    $query->where('name', 'ILIKE', '%' . $this->searchTerm . '%')
                          ->orWhere('description', 'ILIKE', '%' . $this->searchTerm . '%');
                })
                ->with(['genre', 'platform'])
                ->limit(10)
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            $this->searchResults = [];
        }
    }

    public function addGameToList($gameId)
    {
        $list = $this->findListById($this->viewingList);
        
        if (!$list || !$this->canEditList($list)) {
            $this->successMessage = 'You do not have permission to edit this list.';
            return;
        }
        
        // Check if game is already in the list
        if (!$list->items()->where('product_id', $gameId)->exists()) {
            $list->items()->create(['product_id' => $gameId]);
            $this->successMessage = 'Game added to list!';
            $this->refreshLists();
        } else {
            $this->successMessage = 'Game is already in this list!';
        }
        
        $this->searchTerm = '';
        $this->searchResults = [];
    }

    public function removeGameFromList($gameId)
    {
        $list = $this->findListById($this->viewingList);
        
        if (!$list || !$this->canEditList($list)) {
            $this->successMessage = 'You do not have permission to edit this list.';
            return;
        }
        
        $list->items()->where('product_id', $gameId)->delete();
        
        $this->successMessage = 'Game removed from list!';
        $this->refreshLists();
    }

    public function updatedSearchTerm()
    {
        $this->searchGames();
    }

    // New methods for enhanced features
    public function duplicateList($listId)
    {
        $originalList = auth()->user()->lists()->with('items.product')->findOrFail($listId);
        
        $newList = auth()->user()->lists()->create([
            'name' => $originalList->name . ' (Copy)',
            'slug' => Str::slug($originalList->name . ' Copy') . '-' . Str::random(6),
            'is_public' => false, // Always create as private
            'category' => $originalList->category,
            'sort_by' => $originalList->sort_by,
            'sort_direction' => $originalList->sort_direction,
            'cloned_from' => $originalList->id,
            'allow_collaboration' => $originalList->allow_collaboration,
            'allow_comments' => $originalList->allow_comments,
        ]);
        
        // Copy all items
        foreach ($originalList->items as $item) {
            $newList->items()->create([
                'product_id' => $item->product_id,
                'sort_order' => $item->sort_order,
            ]);
        }
        
        $this->successMessage = 'List duplicated successfully!';
        $this->refreshLists();
    }

    public function updateListSort($listId, $sortBy, $direction = null)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        
        // Ensure direction is always valid
        if ($direction && in_array($direction, ['asc', 'desc'])) {
            // Use provided direction if valid
            $newDirection = $direction;
        } elseif ($list->sort_by === $sortBy) {
            // Toggle direction if same sort field
            $newDirection = $list->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            // Default to 'asc' for new sort field
            $newDirection = 'asc';
        }
        
        $list->update([
            'sort_by' => $sortBy,
            'sort_direction' => $newDirection,
        ]);
        
        $this->successMessage = 'List sorting updated!';
        $this->refreshLists();
    }

    public function followList($listId)
    {
        $list = ListModel::where('is_public', true)->findOrFail($listId);
        
        if (!$list->isFollowedBy(auth()->id())) {
            $list->followers()->create(['user_id' => auth()->id()]);
            $list->increment('followers_count');
            $this->successMessage = 'Now following this list!';
        } else {
            $list->followers()->where('user_id', auth()->id())->delete();
            $list->decrement('followers_count');
            $this->successMessage = 'Unfollowed list.';
        }
        
        $this->refreshLists();
    }
    
    public function unfollowList($listId)
    {
        $list = ListModel::where('is_public', true)->findOrFail($listId);
        
        if ($list->isFollowedBy(auth()->id())) {
            $list->followers()->where('user_id', auth()->id())->delete();
            $list->decrement('followers_count');
            $this->successMessage = 'Unfollowed list.';
        }
        
        $this->refreshLists();
    }

    public function startEditingCategory($listId)
    {
        $list = $this->findListById($listId);
        
        if (!$list || !$this->canChangeCategory($list)) {
            $this->successMessage = 'You do not have permission to change the category for this list.';
            return;
        }
        
        $this->editingCategoryListId = $listId;
        $this->editingCategoryValue = $list->category ?? 'general';
    }
    
    public function saveCategory()
    {
        $list = $this->findListById($this->editingCategoryListId);
        
        if (!$list || !$this->canChangeCategory($list)) {
            $this->successMessage = 'You do not have permission to change the category for this list.';
            return;
        }
        
        $list->update(['category' => $this->editingCategoryValue]);
        
        $this->editingCategoryListId = null;
        $this->editingCategoryValue = 'general';
        $this->successMessage = 'List category updated!';
        $this->refreshLists();
    }
    
    public function cancelCategoryEdit()
    {
        $this->editingCategoryListId = null;
        $this->editingCategoryValue = 'general';
    }
    
    public function startEditingDescription($listId)
    {
        Log::info('startEditingDescription called with listId: ' . $listId);
        
        $list = $this->findListById($listId);
        
        if (!$list || $list->user_id !== auth()->id()) {
            $this->successMessage = 'You do not have permission to edit the description for this list.';
            return;
        }
        
        $this->editingDescriptionListId = $listId;
        $this->editingDescriptionValue = $list->description ?? '';
        $this->showDescriptionModal = true;
        
        Log::info('Description modal opened for list: ' . $list->name);
    }
    
    public function saveDescription()
    {
        $this->validate([
            'editingDescriptionValue' => 'nullable|string|max:1000',
        ]);
        
        $list = $this->findListById($this->editingDescriptionListId);
        
        if (!$list || $list->user_id !== auth()->id()) {
            $this->successMessage = 'You do not have permission to edit the description for this list.';
            return;
        }
        
        $list->update([
            'description' => $this->editingDescriptionValue ?: null,
        ]);
        
        $this->successMessage = 'List description updated successfully!';
        $this->closeDescriptionModal();
        $this->refreshLists();
    }
    
    public function closeDescriptionModal()
    {
        $this->showDescriptionModal = false;
        $this->editingDescriptionListId = null;
        $this->editingDescriptionValue = '';
    }

    public function cancelDescriptionEdit()
    {
        $this->closeDescriptionModal();
    }

    // Collaboration Methods
    public function requestCollaboration($listId)
    {
        $list = ListModel::where('is_public', true)->where('allow_collaboration', true)->findOrFail($listId);
        
        // Check if user already has a collaboration request/invitation
        $existingCollaboration = $list->collaborators()->where('user_id', auth()->id())->first();
        
        if ($existingCollaboration) {
            if ($existingCollaboration->isPending()) {
                $this->successMessage = 'You already have a pending collaboration request for this list.';
            } else {
                $this->successMessage = 'You are already a collaborator on this list.';
            }
            return;
        }
        
        // Create collaboration request with default permissions
        $list->collaborators()->create([
            'user_id' => auth()->id(),
            'invited_by_owner' => false, // This is a user request
            'can_add_games' => true,
            'can_delete_games' => true,
            'can_rename_list' => false,
            'can_manage_users' => false,
            'can_change_privacy' => false,
            'can_change_category' => false,
            'invited_at' => now(),
            // accepted_at remains null for pending requests
        ]);
        
        $this->successMessage = 'Collaboration request sent! The list owner will be notified.';
    }

    public function acceptCollaborationRequest($collaboratorId)
    {
        $collaborator = \App\Models\ListCollaborator::findOrFail($collaboratorId);
        
        // Verify the current user owns the list
        if ($collaborator->list->user_id !== auth()->id()) {
            $this->successMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        $collaborator->accept();
        $this->successMessage = 'Collaboration request accepted!';
        $this->refreshLists();
    }

    public function rejectCollaborationRequest($collaboratorId)
    {
        $collaborator = \App\Models\ListCollaborator::findOrFail($collaboratorId);
        
        // Verify the current user owns the list
        if ($collaborator->list->user_id !== auth()->id()) {
            $this->successMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        $collaborator->delete();
        $this->successMessage = 'Collaboration request rejected.';
        $this->refreshLists();
    }

    public function removeCollaborator($collaboratorId)
    {
        $collaborator = \App\Models\ListCollaborator::findOrFail($collaboratorId);
        
        // Verify the current user owns the list
        if ($collaborator->list->user_id !== auth()->id()) {
            $this->successMessage = 'You can only manage collaborators for your own lists.';
            return;
        }
        
        $collaborator->delete();
        $this->successMessage = 'Collaborator removed from list.';
        $this->refreshLists();
    }

    public function inviteCollaborator($listId, $email, $permissions = null)
    {
        $list = auth()->user()->lists()->findOrFail($listId);
        
        // Find user by email
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            $this->successMessage = 'User with that email address not found.';
            return;
        }
        
        // Check if already a collaborator
        $existingCollaboration = $list->collaborators()->where('user_id', $user->id)->first();
        
        if ($existingCollaboration) {
            $this->successMessage = 'User is already a collaborator or has a pending invitation.';
            return;
        }
        
        // Set default permissions if none provided
        if (!$permissions) {
            $permissions = [
                'can_add_games' => true,
                'can_delete_games' => true,
                'can_rename_list' => false,
                'can_manage_users' => false,
                'can_change_privacy' => false,
                'can_change_category' => false,
            ];
        }
        
        // Create invitation (sent by owner)
        $list->collaborators()->create([
            'user_id' => $user->id,
            'invited_by_owner' => true, // This is an owner invitation
            'can_add_games' => $permissions['can_add_games'] ?? true,
            'can_delete_games' => $permissions['can_delete_games'] ?? true,
            'can_rename_list' => $permissions['can_rename_list'] ?? false,
            'can_manage_users' => $permissions['can_manage_users'] ?? false,
            'can_change_privacy' => $permissions['can_change_privacy'] ?? false,
            'can_change_category' => $permissions['can_change_category'] ?? false,
            'invited_at' => now(),
            // accepted_at remains null for pending invitations
        ]);
        
        $this->successMessage = "Collaboration invitation sent to {$user->name}!";
        $this->refreshLists();
    }

    // Permission checking methods
    private function canEditList($list)
    {
        if ($list->user_id === auth()->id()) {
            return true;
        }
        
        $collaboration = $list->collaborators()->where('user_id', auth()->id())->whereNotNull('accepted_at')->first();
        return $collaboration && ($collaboration->can_add_games || $collaboration->can_delete_games);
    }
    
    private function canManageList($list)
    {
        if ($list->user_id === auth()->id()) {
            return true;
        }
        
        $collaboration = $list->collaborators()->where('user_id', auth()->id())->whereNotNull('accepted_at')->first();
        return $collaboration && $collaboration->can_manage_users;
    }
    
    private function canChangePrivacy($list)
    {
        if ($list->user_id === auth()->id()) {
            return true;
        }
        
        $collaboration = $list->collaborators()->where('user_id', auth()->id())->whereNotNull('accepted_at')->first();
        return $collaboration && $collaboration->can_change_privacy;
    }
    
    private function canChangeCategory($list)
    {
        if ($list->user_id === auth()->id()) {
            return true;
        }
        
        $collaboration = $list->collaborators()->where('user_id', auth()->id())->whereNotNull('accepted_at')->first();
        return $collaboration && $collaboration->can_change_category;
    }
    
    private function canRenameList($list)
    {
        if ($list->user_id === auth()->id()) {
            return true;
        }
        
        $collaboration = $list->collaborators()->where('user_id', auth()->id())->whereNotNull('accepted_at')->first();
        return $collaboration && $collaboration->can_rename_list;
    }

    // Only owners can delete lists
    private function canDeleteList($list)
    {
        if (!$list) return false;
        return $list->user_id === auth()->id();
    }

    private function findListById($listId)
    {
        // Try to find in user's own lists first
        $list = auth()->user()->lists()->find($listId);
        
        // If not found, try collaborative lists
        if (!$list) {
            $list = \App\Models\ListModel::whereHas('collaborators', function ($query) use ($listId) {
                $query->where('user_id', auth()->id())
                      ->whereNotNull('accepted_at');
            })->find($listId);
        }
        
        return $list;
    }

    // Methods for handling invitations received by the user
    public function acceptInvitation($collaboratorId)
    {
        $collaborator = \App\Models\ListCollaborator::where('id', $collaboratorId)
            ->where('user_id', auth()->id())
            ->where('invited_by_owner', true)
            ->whereNull('accepted_at')
            ->first();
            
        if (!$collaborator) {
            $this->successMessage = 'Invitation not found or already processed.';
            return;
        }
        
        $collaborator->accept();
        $this->successMessage = "You've joined the list '{$collaborator->list->name}'!";
        $this->refreshLists();
    }
    
    public function declineInvitation($collaboratorId)
    {
        $collaborator = \App\Models\ListCollaborator::where('id', $collaboratorId)
            ->where('user_id', auth()->id())
            ->where('invited_by_owner', true)
            ->whereNull('accepted_at')
            ->first();
            
        if (!$collaborator) {
            $this->successMessage = 'Invitation not found or already processed.';
            return;
        }
        
        $listName = $collaborator->list->name;
        $collaborator->delete();
        $this->successMessage = "You've declined the invitation to '{$listName}'.";
        $this->refreshLists();
    }

    public function openCollaborationManager($listId)
    {
        $this->managingListId = $listId;
        $this->showCollaborationManager = true;
    }

    public function closeCollaborationManager()
    {
        $this->showCollaborationManager = false;
        $this->managingListId = null;
        $this->inviteEmail = '';
        $this->invitePermissions = [
            'can_add_games' => true,
            'can_delete_games' => true,
            'can_rename_list' => false,
            'can_manage_users' => false,
            'can_change_privacy' => false,
            'can_change_category' => false,
        ];
    }

    public function sendInvitation()
    {
        $this->validate([
            'inviteEmail' => 'required|email|exists:users,email',
        ]);

        $list = $this->findListById($this->managingListId);
        if (!$list || !$this->canManageUsers($list)) {
            $this->successMessage = 'You do not have permission to manage users for this list.';
            return;
        }

        $user = User::where('email', $this->inviteEmail)->first();
        
        // Check if user already has collaboration
        if ($list->collaborators()->where('user_id', $user->id)->exists()) {
            $this->successMessage = 'User is already a collaborator or has a pending invitation.';
            return;
        }

        $list->collaborators()->create([
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

        $this->successMessage = 'Invitation sent successfully!';
        $this->inviteEmail = '';
        $this->refreshLists();
    }

    public function updateCollaboratorPermissions($collaboratorId, $permissions)
    {
        $collaboration = ListCollaborator::findOrFail($collaboratorId);
        $list = $collaboration->list;
        
        if (!$this->canManageUsers($list)) {
            $this->successMessage = 'You do not have permission to manage users for this list.';
            return;
        }

        $collaboration->update($permissions);
        $this->successMessage = 'Permissions updated successfully!';
        $this->refreshLists();
    }

    public function removeCollaboratorFromManager($collaboratorId)
    {
        $collaboration = ListCollaborator::findOrFail($collaboratorId);
        $list = $collaboration->list;
        
        if (!$this->canManageUsers($list)) {
            $this->successMessage = 'You do not have permission to manage users for this list.';
            return;
        }

        $collaboration->delete();
        $this->successMessage = 'Collaborator removed successfully!';
        $this->refreshLists();
    }

    private function canManageUsers($list)
    {
        if ($list->user_id === auth()->id()) {
            return true;
        }
        
        $collaboration = $list->collaborators()->where('user_id', auth()->id())->first();
        return $collaboration && $collaboration->can_manage_users;
    }

    public function toggleCollaboration($listId)
    {
        $list = $this->findListById($listId);
        
        if (!$list || $list->user_id !== auth()->id()) {
            $this->successMessage = 'You do not have permission to change collaboration settings for this list.';
            return;
        }

        $list->update([
            'allow_collaboration' => !$list->allow_collaboration
        ]);

        $status = $list->allow_collaboration ? 'enabled' : 'disabled';
        $this->successMessage = "Collaboration has been {$status} for this list.";
        
        // If disabling collaboration, remove all existing collaborators
        if (!$list->allow_collaboration) {
            $list->collaborators()->delete();
            $this->successMessage = "Collaboration has been disabled and all collaborators have been removed.";
        }
        
        $this->refreshLists();
    }

    public function render()
    {
        return view('livewire.user-lists', [
            'categories' => ListModel::$categories,
            'sortOptions' => ListModel::$sortOptions,
        ]);
    }
} 