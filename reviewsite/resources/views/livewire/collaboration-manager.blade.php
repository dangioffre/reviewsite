<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if($successMessage)
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ $errorMessage }}
        </div>
    @endif

    <!-- Collaboration Header -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Collaboration Management</h3>
                    <p class="text-sm text-gray-600">Manage collaborators and permissions for this list</p>
                </div>
            </div>
            
            @if($list->user_id === auth()->id())
                <button 
                    wire:click="toggleInviteForm"
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Invite Collaborator
                </button>
            @endif
        </div>
    </div>

    <!-- Invite Form -->
    @if($showInviteForm && $list->user_id === auth()->id())
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Invite New Collaborator</h4>
            
            <div class="space-y-4">
                <div>
                    <label for="inviteEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        id="inviteEmail"
                        wire:model="inviteEmail"
                        placeholder="Enter user's email address"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                    @error('inviteEmail') 
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <input type="checkbox" wire:model="invitePermissions.can_add_games" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Add Games</div>
                                <div class="text-xs text-gray-600">Can add games to the list</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <input type="checkbox" wire:model="invitePermissions.can_delete_games" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Delete Games</div>
                                <div class="text-xs text-gray-600">Can remove games from the list</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <input type="checkbox" wire:model="invitePermissions.can_rename_list" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Rename List</div>
                                <div class="text-xs text-gray-600">Can change the list name</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <input type="checkbox" wire:model="invitePermissions.can_manage_users" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Manage Users</div>
                                <div class="text-xs text-gray-600">Can add/remove collaborators</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <input type="checkbox" wire:model="invitePermissions.can_change_privacy" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Change Privacy</div>
                                <div class="text-xs text-gray-600">Can make list public/private</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <input type="checkbox" wire:model="invitePermissions.can_change_category" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Change Category</div>
                                <div class="text-xs text-gray-600">Can change list category</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button 
                        wire:click="inviteCollaborator"
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg"
                    >
                        Send Invitation
                    </button>
                    <button 
                        wire:click="toggleInviteForm"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Pending Requests (for list owner) -->
    @if($list->user_id === auth()->id())
        @php
            $pendingRequests = $list->collaborators->where('accepted_at', null);
            $sentInvitations = $pendingRequests->where('invited_by_owner', true);
            $userRequests = $pendingRequests->where('invited_by_owner', false);
        @endphp
        
        <!-- Collaboration Requests from Users -->
        @if($userRequests->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Collaboration Requests ({{ $userRequests->count() }})
                </h4>
                <p class="text-sm text-gray-600 mb-4">Users requesting to collaborate on your list</p>
                
                <div class="space-y-3">
                    @foreach($userRequests as $collaborator)
                        <div class="bg-white border border-yellow-200 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="bg-yellow-100 p-2 rounded-full">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $collaborator->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $collaborator->user->email }} • Requested collaboration</p>
                                    <p class="text-xs text-gray-500">{{ $collaborator->getPermissionSummary() }}</p>
                                </div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button 
                                    wire:click="acceptCollaborationRequest({{ $collaborator->id }})"
                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors"
                                >
                                    Accept
                                </button>
                                <button 
                                    wire:click="rejectCollaborationRequest({{ $collaborator->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors"
                                >
                                    Reject
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pending Invitations Sent by Owner -->
        @if($sentInvitations->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Pending Invitations ({{ $sentInvitations->count() }})
                </h4>
                <p class="text-sm text-gray-600 mb-4">Invitations you sent that haven't been accepted yet</p>
                
                <div class="space-y-3">
                    @foreach($sentInvitations as $collaborator)
                        <div class="bg-white border border-blue-200 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $collaborator->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $collaborator->user->email }} • Invitation sent</p>
                                    <p class="text-xs text-gray-500">{{ $collaborator->getPermissionSummary() }}</p>
                                </div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm font-medium">
                                    Waiting for response
                                </span>
                                <button 
                                    wire:click="cancelInvitation({{ $collaborator->id }})"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- Active Collaborators -->
    @php
        $activeCollaborators = $list->collaborators->where('accepted_at', '!=', null);
    @endphp
    
    @if($activeCollaborators->count() > 0)
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Active Collaborators ({{ $activeCollaborators->count() }})
            </h4>
            
            <div class="space-y-4">
                @foreach($activeCollaborators as $collaborator)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4" x-data="{ editingPermissions: false }">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $collaborator->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $collaborator->user->email }}</p>
                                    <p class="text-xs text-gray-500">{{ $collaborator->getPermissionSummary() }}</p>
                                </div>
                            </div>
                            
                            @if($list->user_id === auth()->id())
                                <div class="flex items-center space-x-3">
                                    <button 
                                        @click="editingPermissions = !editingPermissions"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors"
                                    >
                                        <span x-show="!editingPermissions">Edit Permissions</span>
                                        <span x-show="editingPermissions">Cancel</span>
                                    </button>
                                    <button 
                                        wire:click="removeCollaborator({{ $collaborator->id }})"
                                        onclick="return confirm('Are you sure you want to remove this collaborator?')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium transition-colors"
                                    >
                                        Remove
                                    </button>
                                </div>
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium">
                                    {{ $collaborator->getPermissionSummary() }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Permission Editing Form -->
                        @if($list->user_id === auth()->id())
                            <div x-show="editingPermissions" x-collapse class="mt-4 pt-4 border-t border-gray-200">
                                <h5 class="text-sm font-medium text-gray-900 mb-3">Edit Permissions for {{ $collaborator->user->name }}</h5>
                                <form wire:submit.prevent="updateCollaboratorPermissions({{ $collaborator->id }})">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                                        <label class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50 transition-colors cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model="editPermissions.{{ $collaborator->id }}.can_add_games" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">Add Games</div>
                                                <div class="text-xs text-gray-600">Can add games to the list</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50 transition-colors cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model="editPermissions.{{ $collaborator->id }}.can_delete_games" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">Delete Games</div>
                                                <div class="text-xs text-gray-600">Can remove games from the list</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50 transition-colors cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model="editPermissions.{{ $collaborator->id }}.can_rename_list" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">Rename List</div>
                                                <div class="text-xs text-gray-600">Can change the list name</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50 transition-colors cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model="editPermissions.{{ $collaborator->id }}.can_manage_users" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">Manage Users</div>
                                                <div class="text-xs text-gray-600">Can add/remove collaborators</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50 transition-colors cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model="editPermissions.{{ $collaborator->id }}.can_change_privacy" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">Change Privacy</div>
                                                <div class="text-xs text-gray-600">Can make list public/private</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50 transition-colors cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model="editPermissions.{{ $collaborator->id }}.can_change_category" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">Change Category</div>
                                                <div class="text-xs text-gray-600">Can change list category</div>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex space-x-3">
                                        <button type="submit" 
                                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                                            Save Changes
                                        </button>
                                        <button type="button" 
                                                @click="editingPermissions = false"
                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-medium transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Collaborators</h3>
            <p class="text-gray-600">
                @if($list->user_id === auth()->id())
                    Invite users to collaborate on this list and share the gaming experience!
                @else
                    This list doesn't have any active collaborators yet.
                @endif
            </p>
        </div>
    @endif
</div> 
