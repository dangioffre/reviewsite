<div class="space-y-8">
    <!-- Success Message -->
    @if($successMessage)
        <div class="p-4 bg-gradient-to-r from-[#22C55E]/20 to-[#16A34A]/20 border border-[#22C55E]/30 rounded-xl backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-[#22C55E]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="text-[#22C55E] font-semibold text-sm font-['Inter']">{{ $successMessage }}</div>
            </div>
        </div>
    @endif

    @if(!$viewingList)
        <!-- Main Lists View -->
        <div>
            <!-- Enhanced Header with Stats -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-white font-['Share_Tech_Mono'] mb-2">My Game Lists</h1>
                    <p class="text-[#A1A1AA] font-['Inter'] text-lg">Organize, share, and discover your favorite games</p>
                </div>
                
                <!-- Quick Stats -->
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $lists->count() }}</div>
                        <div class="text-[#A1A1AA] text-sm font-['Inter']">Total Lists</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-[#22C55E] font-['Share_Tech_Mono']">{{ $lists->where('is_public', true)->count() }}</div>
                        <div class="text-[#A1A1AA] text-sm font-['Inter']">Public</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-[#2563EB] font-['Share_Tech_Mono']">{{ $lists->sum('items_count') }}</div>
                        <div class="text-[#A1A1AA] text-sm font-['Inter']">Total Games</div>
                    </div>
                    
                    <!-- Create New List Button -->
                    <button wire:click="$set('showCreate', true)" 
                            class="bg-gradient-to-r from-[#7C3AED] to-[#6D28D9] hover:from-[#6D28D9] hover:to-[#5B21B6] text-white px-6 py-3 rounded-xl font-bold transition-all duration-200 font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create New List
                    </button>
                </div>
            </div>

            <!-- Pending Invitations -->
            @if($pendingInvitations && $pendingInvitations->count() > 0)
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-[#F59E0B]/20 to-[#D97706]/20 border border-[#F59E0B]/30 rounded-2xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-[#F59E0B] to-[#D97706] rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white font-['Share_Tech_Mono']">Pending Invitations</h2>
                                <p class="text-[#A1A1AA] font-['Inter'] text-sm">You've been invited to collaborate on {{ $pendingInvitations->count() }} list{{ $pendingInvitations->count() !== 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($pendingInvitations as $invitation)
                                <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-[#7C3AED] to-[#A855F7] rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-white font-semibold font-['Inter']">{{ $invitation->list->name }}</h3>
                                            <p class="text-[#A1A1AA] text-sm font-['Inter']">
                                                Invited by {{ $invitation->list->user->name }} • {{ $invitation->getPermissionSummary() }}
                                            </p>
                                            <p class="text-[#71717A] text-xs font-['Inter']">{{ $invitation->invited_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3">
                                        <button wire:click="acceptInvitation({{ $invitation->id }})"
                                                class="bg-gradient-to-r from-[#22C55E] to-[#16A34A] hover:from-[#16A34A] hover:to-[#15803D] text-white px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200 font-['Inter'] flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Accept
                                        </button>
                                        <button wire:click="declineInvitation({{ $invitation->id }})"
                                                class="bg-[#3F3F46] hover:bg-[#52525B] text-[#A1A1AA] hover:text-white px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200 font-['Inter'] flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Decline
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Enhanced Create New List Form -->
            @if($showCreate)
                <div class="bg-gradient-to-br from-[#18181B] to-[#0F0F10] border border-[#3F3F46] rounded-2xl p-8 mb-8 shadow-2xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#7C3AED] to-[#A855F7] rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Create New List</h2>
                            <p class="text-[#A1A1AA] font-['Inter']">Build your perfect game collection</p>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="createList" class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-white font-semibold font-['Inter'] text-sm">List Name</label>
                            <input type="text" wire:model.defer="newListName" placeholder="e.g., My Favorite RPGs, Games to Play, Best of 2024..." 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-4 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-[#7C3AED] transition-all font-['Inter'] text-lg" />
                            @error('newListName')
                                <div class="text-[#E53E3E] text-sm mt-2 font-['Inter'] flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Category Selection -->
                            <div class="space-y-2">
                                <label class="block text-white font-semibold font-['Inter'] text-sm">Category</label>
                                <select wire:model="selectedCategory" 
                                        class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-[#7C3AED] transition-all font-['Inter']">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Sort By Selection -->
                            <div class="space-y-2">
                                <label class="block text-white font-semibold font-['Inter'] text-sm">Default Sort</label>
                                <select wire:model="selectedSortBy" 
                                        class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-[#7C3AED] transition-all font-['Inter']">
                                    @foreach($sortOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Advanced Options -->
                        <div class="space-y-4">
                            <h4 class="text-white font-semibold font-['Inter']">List Settings</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="flex items-center p-4 bg-[#18181B] border border-[#3F3F46] rounded-xl hover:border-[#52525B] transition-colors cursor-pointer">
                                    <input type="checkbox" wire:model="allowCollaboration" 
                                           class="w-5 h-5 text-[#7C3AED] bg-[#18181B] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <div class="ml-3">
                                        <div class="text-white font-semibold text-sm font-['Inter']">Allow Collaboration</div>
                                        <div class="text-[#A1A1AA] text-xs font-['Inter']">Let others help build this list</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 bg-[#18181B] border border-[#3F3F46] rounded-xl hover:border-[#52525B] transition-colors cursor-pointer">
                                    <input type="checkbox" wire:model="allowComments" 
                                           class="w-5 h-5 text-[#7C3AED] bg-[#18181B] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <div class="ml-3">
                                        <div class="text-white font-semibold text-sm font-['Inter']">Allow Comments</div>
                                        <div class="text-[#A1A1AA] text-xs font-['Inter']">Enable community discussion</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="submit" 
                                    class="bg-gradient-to-r from-[#7C3AED] to-[#6D28D9] hover:from-[#6D28D9] hover:to-[#5B21B6] text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                Create List
                            </button>
                            <button type="button" wire:click="$set('showCreate', false)" 
                                    class="px-8 py-3 text-[#A1A1AA] hover:text-white hover:bg-[#27272A] rounded-xl transition-all duration-200 font-['Inter']">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Category Edit Modal -->
            @if($editingCategoryListId)
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 mb-6">
                    <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono'] mb-4">Change List Category</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-white font-semibold mb-2 font-['Inter'] text-sm">Select Category</label>
                            <select wire:model="editingCategoryValue" 
                                    class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex gap-3">
                            <button wire:click="saveCategory" 
                                    class="bg-[#22C55E] hover:bg-[#16A34A] text-white px-6 py-2 rounded-lg font-semibold text-sm transition-colors font-['Inter']">
                                Save Category
                            </button>
                            <button wire:click="cancelCategoryEdit" 
                                    class="px-6 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Enhanced Lists Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($lists as $list)
                    <div class="group bg-gradient-to-br from-[#27272A] via-[#1F1F23] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] hover:border-[#52525B] transition-all duration-300 hover:shadow-2xl hover:shadow-[#7C3AED]/10 hover:-translate-y-1">
                        <!-- List Header -->
                        <div class="p-6 pb-4">
                            @if($editingList === $list->id)
                                <form wire:submit.prevent="saveEdit" class="space-y-3">
                                    <input type="text" wire:model.defer="editingName" 
                                           class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white text-lg font-bold font-['Share_Tech_Mono'] focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-[#7C3AED] transition-all" />
                                    <div class="flex gap-3">
                                        <button type="submit" class="bg-[#22C55E] hover:bg-[#16A34A] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">Save</button>
                                        <button type="button" wire:click="cancelEdit" class="text-[#A1A1AA] hover:text-white px-4 py-2 text-sm transition-colors">Cancel</button>
                                    </div>
                                </form>
                            @else
                                <div class="space-y-3">
                                    <!-- Title and Category -->
                                    <div class="flex items-start justify-between">
                                        <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono'] leading-tight group-hover:text-[#7C3AED] transition-colors">
                                            {{ $list->name }}
                                        </h3>
                                        @if($list->category && $list->category !== 'general')
                                            <span class="bg-gradient-to-r from-[#7C3AED]/20 to-[#6D28D9]/20 text-[#7C3AED] px-3 py-1 rounded-full text-xs font-bold border border-[#7C3AED]/30">
                                                {{ $categories[$list->category] ?? ucfirst($list->category) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Games Count - Prominent Display -->
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-[#2563EB] to-[#1D4ED8] rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-white font-bold text-lg font-['Inter']">{{ $list->items_count }}</span>
                                        <span class="text-[#A1A1AA] font-['Inter']">{{ $list->items_count === 1 ? 'game' : 'games' }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- List Stats & Features -->
                        <div class="px-6 pb-4 space-y-4">
                            <!-- Community Stats -->
                            @if($list->followers_count > 0 || $list->comments_count > 0)
                                <div class="flex items-center gap-4">
                                    @if($list->followers_count > 0)
                                        <div class="flex items-center gap-2 text-sm">
                                            <div class="w-6 h-6 bg-[#22C55E]/20 rounded-lg flex items-center justify-center">
                                                <svg class="w-3 h-3 text-[#22C55E]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                                </svg>
                                            </div>
                                            <span class="text-[#22C55E] font-semibold font-['Inter']">{{ $list->followers_count }}</span>
                                            <span class="text-[#A1A1AA] font-['Inter']">{{ $list->followers_count === 1 ? 'follower' : 'followers' }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($list->comments_count > 0)
                                        <div class="flex items-center gap-2 text-sm">
                                            <div class="w-6 h-6 bg-[#2563EB]/20 rounded-lg flex items-center justify-center">
                                                <svg class="w-3 h-3 text-[#2563EB]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-[#2563EB] font-semibold font-['Inter']">{{ $list->comments_count }}</span>
                                            <span class="text-[#A1A1AA] font-['Inter']">{{ $list->comments_count === 1 ? 'comment' : 'comments' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Features & Status -->
                            <div class="space-y-3">
                                <!-- Top Row: Visibility and User Role -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <!-- Visibility Status -->
                                        @if($list->is_public)
                                            <span class="inline-flex items-center gap-1 bg-[#22C55E]/20 text-[#22C55E] px-2 py-1 rounded-lg text-xs font-semibold border border-[#22C55E]/30">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Public
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-[#71717A]/20 text-[#71717A] px-2 py-1 rounded-lg text-xs font-semibold border border-[#71717A]/30">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Private
                                            </span>
                                        @endif
                                        
                                        <!-- User Role Indicator -->
                                        @if(isset($list->user_role) && $list->user_role !== 'owner')
                                            <span class="inline-flex items-center gap-1 bg-[#7C3AED]/20 text-[#7C3AED] px-2 py-1 rounded-lg text-xs font-semibold border border-[#7C3AED]/30 cursor-help" 
                                                  title="@if(isset($list->permissions_summary))Permissions: {{ $list->permissions_summary }}@endif">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                                </svg>
                                                @if(isset($list->permissions_summary) && $list->permissions_summary !== 'View Only')
                                                    Collaborator
                                                @else
                                                    View Only
                                                @endif
                                            </span>
                                        @elseif($list->allow_collaboration)
                                            <span class="inline-flex items-center gap-1 bg-[#2563EB]/20 text-[#2563EB] px-2 py-1 rounded-lg text-xs font-semibold border border-[#2563EB]/30">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                                </svg>
                                                Collaborative
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Special Indicators -->
                                    <div class="flex items-center gap-2">
                                        @if($list->allow_comments)
                                            <div class="w-6 h-6 bg-[#22C55E]/20 rounded-lg flex items-center justify-center" title="Comments Enabled">
                                                <svg class="w-3 h-3 text-[#22C55E]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        @if($list->cloned_from)
                                            <div class="w-6 h-6 bg-[#F59E0B]/20 rounded-lg flex items-center justify-center" title="Cloned List">
                                                <svg class="w-3 h-3 text-[#F59E0B]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/>
                                                    <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Bottom Row: Permissions Summary for Collaborators -->
                                @if(isset($list->user_role) && $list->user_role !== 'owner' && isset($list->permissions_summary) && $list->permissions_summary !== 'View Only')
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center gap-1 bg-[#059669]/20 text-[#059669] px-2 py-1 rounded-lg text-xs font-medium border border-[#059669]/30">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $list->permissions_summary }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Enhanced Action Buttons -->
                        <div class="p-6 pt-0 space-y-3">
                            <!-- Primary Action -->
                            <button wire:click="viewList({{ $list->id }})" 
                                    class="w-full bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] hover:from-[#1D4ED8] hover:to-[#1E40AF] text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 font-['Inter'] flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 group">
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View & Manage
                            </button>

                            <!-- Quick Actions -->
                            @php
                                $canRename = $editingList !== $list->id && (($list->user_role ?? 'owner') === 'owner' || in_array($list->user_role ?? 'owner', ['admin', 'edit']));
                                $canClone = ($list->user_role ?? 'owner') === 'owner';
                                $hasQuickActions = $canRename || $canClone;
                            @endphp
                            
                            @if($hasQuickActions)
                                <div class="grid {{ ($canRename && $canClone) ? 'grid-cols-2' : 'grid-cols-1' }} gap-2">
                                    @if($canRename)
                                        <button wire:click="startEditing({{ $list->id }})" 
                                                class="bg-[#18181B] hover:bg-[#27272A] text-[#A1A1AA] hover:text-white py-2 px-3 rounded-lg transition-all duration-200 font-['Inter'] text-sm flex items-center justify-center border border-[#3F3F46] hover:border-[#52525B]">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Rename
                                        </button>
                                    @endif
                                    
                                    @if($canClone)
                                        <button wire:click="duplicateList({{ $list->id }})" 
                                                class="bg-[#7C3AED]/10 hover:bg-[#7C3AED]/20 text-[#7C3AED] hover:text-[#6D28D9] py-2 px-3 rounded-lg transition-all duration-200 font-['Inter'] text-sm flex items-center justify-center border border-[#7C3AED]/30 hover:border-[#7C3AED]/50">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Clone
                                        </button>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Dropdown Menu for More Actions -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="w-full bg-[#18181B] hover:bg-[#27272A] text-[#A1A1AA] hover:text-white py-2 px-3 rounded-lg transition-all duration-200 font-['Inter'] text-sm flex items-center justify-center border border-[#3F3F46] hover:border-[#52525B]">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                    More Options
                                    <svg class="w-4 h-4 ml-auto" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     @click.away="open = false"
                                     class="absolute bottom-full left-0 right-0 mb-2 bg-[#27272A] border border-[#3F3F46] rounded-xl shadow-2xl z-10 overflow-hidden">
                                    
                                    <!-- Show different options based on user role -->
                                    @if(($list->user_role ?? 'owner') === 'owner')
                                        <!-- Owner-only actions -->
                                        <button wire:click="togglePublic({{ $list->id }})" 
                                                @click="open = false"
                                                class="w-full text-left px-4 py-3 text-[#22C55E] hover:bg-[#22C55E]/10 transition-colors font-['Inter'] text-sm flex items-center">
                                            @if($list->is_public)
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Make Private
                                            @else
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Make Public
                                            @endif
                                        </button>
                                        
                                        <button wire:click="startEditingCategory({{ $list->id }})" 
                                                @click="open = false"
                                                class="w-full text-left px-4 py-3 text-[#2563EB] hover:bg-[#2563EB]/10 transition-colors font-['Inter'] text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            Change Category
                                        </button>
                                        
                                        @if($list->allow_collaboration)
                                            <button onclick="window.open('{{ route('lists.public', $list->slug) }}?manage=collaboration', '_blank')" 
                                                    @click="open = false"
                                                    class="w-full text-left px-4 py-3 text-[#7C3AED] hover:bg-[#7C3AED]/10 transition-colors font-['Inter'] text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                Manage Collaborators
                                            </button>
                                        @endif
                                    @else
                                        <!-- Collaborator actions -->
                                        <div class="px-4 py-3 bg-[#18181B] border-b border-[#3F3F46]">
                                            <div class="flex items-center gap-2 text-[#A1A1AA] text-xs">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                                </svg>
                                                <span>You're a {{ ucfirst($list->user_role) }} collaborator</span>
                                            </div>
                                            <div class="text-[#71717A] text-xs mt-1">
                                                Owner: {{ $list->user->name ?? 'Unknown' }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($list->is_public)
                                        <button wire:click="copyPublicLink({{ $list->id }})" 
                                                @click="open = false"
                                                class="w-full text-left px-4 py-3 text-[#F59E0B] hover:bg-[#F59E0B]/10 transition-colors font-['Inter'] text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Copy Public Link
                                        </button>
                                    @endif
                                    
                                    @if(($list->user_role ?? 'owner') === 'owner')
                                        <div class="border-t border-[#3F3F46]">
                                            <button wire:click="deleteList({{ $list->id }})" 
                                                    @click="open = false"
                                                    onclick="return confirm('Are you sure you want to delete this list? This action cannot be undone.')"
                                                    class="w-full text-left px-4 py-3 text-[#E53E3E] hover:bg-[#E53E3E]/10 transition-colors font-['Inter'] text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete List
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-16 px-6">
                            <!-- Animated Icon -->
                            <div class="relative mb-8">
                                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-[#7C3AED]/20 to-[#6D28D9]/20 rounded-2xl flex items-center justify-center border-2 border-dashed border-[#7C3AED]/30">
                                    <svg class="w-12 h-12 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <!-- Floating particles effect -->
                                <div class="absolute -top-2 -left-2 w-3 h-3 bg-[#7C3AED] rounded-full opacity-60 animate-pulse"></div>
                                <div class="absolute -bottom-1 -right-1 w-2 h-2 bg-[#22C55E] rounded-full opacity-40 animate-pulse" style="animation-delay: 0.5s"></div>
                                <div class="absolute top-1/2 -right-3 w-1.5 h-1.5 bg-[#2563EB] rounded-full opacity-50 animate-pulse" style="animation-delay: 1s"></div>
                            </div>
                            
                            <div class="max-w-md mx-auto space-y-4">
                                <h3 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Start Your Collection</h3>
                                <p class="text-[#A1A1AA] font-['Inter'] text-lg leading-relaxed">
                                    Create your first game list to organize your favorites, track your progress, and share your recommendations with the community.
                                </p>
                                
                                <!-- Feature highlights -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 mb-8">
                                    <div class="text-center space-y-2">
                                        <div class="w-10 h-10 bg-[#2563EB]/20 rounded-lg mx-auto flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#2563EB]" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-[#A1A1AA] text-sm font-['Inter']">Organize Games</p>
                                    </div>
                                    <div class="text-center space-y-2">
                                        <div class="w-10 h-10 bg-[#22C55E]/20 rounded-lg mx-auto flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#22C55E]" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                            </svg>
                                        </div>
                                        <p class="text-[#A1A1AA] text-sm font-['Inter']">Share & Collaborate</p>
                                    </div>
                                    <div class="text-center space-y-2">
                                        <div class="w-10 h-10 bg-[#7C3AED]/20 rounded-lg mx-auto flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#7C3AED]" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <p class="text-[#A1A1AA] text-sm font-['Inter']">Get Feedback</p>
                                    </div>
                                </div>
                                
                                <button wire:click="$set('showCreate', true)" 
                                        class="bg-gradient-to-r from-[#7C3AED] to-[#6D28D9] hover:from-[#6D28D9] hover:to-[#5B21B6] text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-200 font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center mx-auto">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Your First List
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- List Detail View -->
        @php
            $currentList = $lists->find($viewingList);
        @endphp
        
        @if($currentList)
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <button wire:click="closeView" 
                                class="text-[#A1A1AA] hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $currentList->name }}</h2>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[#A1A1AA] text-sm font-['Inter']">{{ $currentList->items_count }} games</span>
                                @if($currentList->is_public)
                                    <span class="bg-[#22C55E]/20 text-[#22C55E] px-2 py-1 rounded text-xs font-semibold">Public</span>
                                    <button wire:click="copyPublicLink({{ $currentList->id }})" 
                                            class="text-[#2563EB] hover:text-[#1D4ED8] text-xs font-semibold transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Copy Public Link
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        @php
                            $isOwner = $currentList->user_id === auth()->id();
                            $userRole = $isOwner ? 'owner' : ($currentList->user_role ?? 'view');
                            $canEdit = $userRole === 'owner' || in_array($userRole, ['edit', 'admin']);
                            $collaboration = $currentList->collaborators->where('user_id', auth()->id())->first();
                            $canManageUsers = $userRole === 'owner' || ($collaboration && $collaboration->can_manage_users);
                            $canChangePrivacy = $userRole === 'owner' || ($collaboration && $collaboration->can_change_privacy);
                            $canChangeCategory = $userRole === 'owner' || ($collaboration && $collaboration->can_change_category);
                            $canRename = $userRole === 'owner' || ($collaboration && $collaboration->can_rename_list);
                        @endphp
                        

                    </div>
                </div>
                
                <!-- Management Panel -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">List Management</h3>
                            <p class="text-[#A1A1AA] font-['Inter'] text-sm">Manage settings and collaborations</p>
                        </div>
                        @if(!$isOwner)
                            <div class="bg-[#7C3AED]/20 text-[#7C3AED] px-3 py-1 rounded-lg text-xs font-semibold border border-[#7C3AED]/30">
                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                                {{ $collaboration ? $collaboration->getPermissionSummary() : 'Collaborator' }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Management Actions Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Rename List -->
                        @if($canRename)
                            <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#2563EB]/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold font-['Inter']">Rename List</h4>
                                        <p class="text-[#A1A1AA] text-xs">Change the list name</p>
                                    </div>
                                </div>
                                @if($editingList === $currentList->id)
                                    <form wire:submit.prevent="saveEdit" class="space-y-3">
                                        <input type="text" wire:model.defer="editingName" 
                                               class="w-full bg-[#27272A] border border-[#3F3F46] rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#2563EB]" />
                                        <div class="flex gap-2">
                                            <button type="submit" class="bg-[#22C55E] hover:bg-[#16A34A] text-white px-3 py-1.5 rounded text-xs font-semibold transition-colors">Save</button>
                                            <button type="button" wire:click="cancelEdit" class="text-[#A1A1AA] hover:text-white px-3 py-1.5 text-xs transition-colors">Cancel</button>
                                        </div>
                                    </form>
                                @else
                                    <button wire:click="startEditing({{ $currentList->id }})" 
                                            class="w-full bg-[#2563EB] hover:bg-[#1D4ED8] text-white py-2 px-3 rounded-lg text-sm font-semibold transition-colors">
                                        Rename
                                    </button>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Change Privacy -->
                        @if($canChangePrivacy)
                            <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#22C55E]/20 rounded-lg flex items-center justify-center">
                                        @if($currentList->is_public)
                                            <svg class="w-5 h-5 text-[#22C55E]" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-[#71717A]" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold font-['Inter']">Privacy</h4>
                                        <p class="text-[#A1A1AA] text-xs">Currently {{ $currentList->is_public ? 'Public' : 'Private' }}</p>
                                    </div>
                                </div>
                                <button wire:click="togglePublic({{ $currentList->id }})" 
                                        class="w-full {{ $currentList->is_public ? 'bg-[#71717A] hover:bg-[#52525B]' : 'bg-[#22C55E] hover:bg-[#16A34A]' }} text-white py-2 px-3 rounded-lg text-sm font-semibold transition-colors">
                                    Make {{ $currentList->is_public ? 'Private' : 'Public' }}
                                </button>
                            </div>
                        @endif
                        
                        <!-- Change Category -->
                        @if($canChangeCategory)
                            <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#F59E0B]/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold font-['Inter']">Category</h4>
                                        <p class="text-[#A1A1AA] text-xs">{{ $categories[$currentList->category] ?? 'General' }}</p>
                                    </div>
                                </div>
                                @if($editingCategoryListId === $currentList->id)
                                    <div class="space-y-3">
                                        <select wire:model="editingCategoryValue" 
                                                class="w-full bg-[#27272A] border border-[#3F3F46] rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#F59E0B]">
                                            @foreach($categories as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <div class="flex gap-2">
                                            <button wire:click="saveCategory" class="bg-[#22C55E] hover:bg-[#16A34A] text-white px-3 py-1.5 rounded text-xs font-semibold transition-colors">Save</button>
                                            <button wire:click="cancelCategoryEdit" class="text-[#A1A1AA] hover:text-white px-3 py-1.5 text-xs transition-colors">Cancel</button>
                                        </div>
                                    </div>
                                @else
                                    <button wire:click="startEditingCategory({{ $currentList->id }})" 
                                            class="w-full bg-[#F59E0B] hover:bg-[#D97706] text-white py-2 px-3 rounded-lg text-sm font-semibold transition-colors">
                                        Change Category
                                    </button>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Manage Collaborators -->
                        @if($canManageUsers && $currentList->allow_collaboration)
                            <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#7C3AED]/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold font-['Inter']">Collaborators</h4>
                                        <p class="text-[#A1A1AA] text-xs">{{ $currentList->collaborators->where('accepted_at', '!=', null)->count() }} active</p>
                                    </div>
                                </div>
                                <button wire:click="openCollaborationManager({{ $currentList->id }})" 
                                        class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white py-2 px-3 rounded-lg text-sm font-semibold transition-colors">
                                    Manage Users
                                </button>
                            </div>
                        @endif
                        
                        <!-- Duplicate (Owner Only) -->
                        @if($isOwner)
                            <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#A855F7]/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#A855F7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold font-['Inter']">Duplicate</h4>
                                        <p class="text-[#A1A1AA] text-xs">Create a copy of this list</p>
                                    </div>
                                </div>
                                <button wire:click="duplicateList({{ $currentList->id }})" 
                                        class="w-full bg-[#A855F7] hover:bg-[#9333EA] text-white py-2 px-3 rounded-lg text-sm font-semibold transition-colors">
                                    Duplicate List
                                </button>
                            </div>
                        @endif
                        
                        <!-- Delete (Owner Only) -->
                        @if($isOwner)
                            <div class="bg-[#18181B] border border-[#EF4444]/30 rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#EF4444]/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-[#EF4444] font-semibold font-['Inter']">Delete List</h4>
                                        <p class="text-[#A1A1AA] text-xs">Permanently remove this list</p>
                                    </div>
                                </div>
                                <button wire:click="deleteList({{ $currentList->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this list? This action cannot be undone.')"
                                        class="w-full bg-[#EF4444] hover:bg-[#DC2626] text-white py-2 px-3 rounded-lg text-sm font-semibold transition-colors">
                                    Delete List
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Enhanced List Info & Controls -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- List Info -->
                        <div class="space-y-3">
                            <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono']">List Details</h3>
                            <div class="space-y-2 text-sm">
                                @if($currentList->category && $currentList->category !== 'general')
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#A1A1AA]">Category:</span>
                                        <span class="bg-[#7C3AED]/20 text-[#7C3AED] px-2 py-1 rounded-full text-xs font-semibold">
                                            {{ $categories[$currentList->category] ?? ucfirst($currentList->category) }}
                                        </span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-2">
                                    <span class="text-[#A1A1AA]">Created:</span>
                                    <span class="text-white">{{ $currentList->created_at->format('M j, Y') }}</span>
                                </div>
                                
                                @if($currentList->cloned_from)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#A1A1AA]">Cloned from original list</span>
                                        <svg class="w-4 h-4 text-[#71717A]" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/>
                                            <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Sorting Controls -->
                        <div class="space-y-3">
                            <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono']">Sort Games</h3>
                            <div class="space-y-2">
                                <select wire:change="updateListSort({{ $currentList->id }}, $event.target.value)" 
                                        class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                                    @foreach($sortOptions as $key => $label)
                                        <option value="{{ $key }}" {{ $currentList->sort_by === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <div class="flex gap-2">
                                    <button wire:click="updateListSort({{ $currentList->id }}, '{{ $currentList->sort_by ?? 'date_added' }}', 'asc')" 
                                            class="flex-1 px-3 py-1.5 rounded text-xs transition-colors {{ ($currentList->sort_direction ?? 'desc') === 'asc' ? 'bg-[#2563EB] text-white' : 'bg-[#18181B] text-[#A1A1AA] hover:text-white' }}">
                                        ↑ Ascending
                                    </button>
                                    <button wire:click="updateListSort({{ $currentList->id }}, '{{ $currentList->sort_by ?? 'date_added' }}', 'desc')" 
                                            class="flex-1 px-3 py-1.5 rounded text-xs transition-colors {{ ($currentList->sort_direction ?? 'desc') === 'desc' ? 'bg-[#2563EB] text-white' : 'bg-[#18181B] text-[#A1A1AA] hover:text-white' }}">
                                        ↓ Descending
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Stats -->
                        <div class="space-y-3">
                            <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono']">Community</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-[#18181B] rounded-lg p-3 text-center">
                                    <div class="text-xl font-bold text-[#22C55E]">{{ $currentList->followers_count }}</div>
                                    <div class="text-xs text-[#A1A1AA]">Followers</div>
                                </div>
                                <div class="bg-[#18181B] rounded-lg p-3 text-center">
                                    <div class="text-xl font-bold text-[#2563EB]">{{ $currentList->comments_count }}</div>
                                    <div class="text-xs text-[#A1A1AA]">Comments</div>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="{{ route('lists.public', $currentList->slug) }}" 
                                   target="_blank"
                                   class="flex-1 bg-[#F59E0B] hover:bg-[#D97706] text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors text-center">
                                    View Public Page
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add Games Button (Right Side) -->
                @php
                    $isOwner = $currentList->user_id === auth()->id();
                    $userRole = $isOwner ? 'owner' : ($currentList->user_role ?? 'view');
                    $collaboration = $currentList->collaborators->where('user_id', auth()->id())->first();
                    $canAddGames = $userRole === 'owner' || ($collaboration && $collaboration->can_add_games);
                @endphp
                
                <div class="flex justify-end mb-6">
                    @if($canAddGames)
                        <button wire:click="$set('showSearch', true)" 
                                class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold text-sm transition-colors font-['Inter'] flex items-center shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Games
                        </button>
                    @else
                        <div class="bg-[#3F3F46] text-[#A1A1AA] px-6 py-3 rounded-lg text-sm font-['Inter'] flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Only Access
                        </div>
                    @endif
                </div>

                <!-- Search Section -->
                @if($showSearch)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono']">Add Games to List</h3>
                            <button wire:click="$set('showSearch', false)" 
                                    class="text-[#A1A1AA] hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="relative mb-4">
                            <input type="text" wire:model.live="searchTerm" placeholder="Search for games..." 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-3 pl-10 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']" />
                            <svg class="w-5 h-5 text-[#71717A] absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        


                        @if(count($searchResults) > 0)
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach($searchResults as $game)
                                    <div class="flex items-center justify-between bg-[#18181B] rounded-lg px-4 py-3 border border-[#3F3F46] hover:bg-[#27272A] transition-colors group">
                                        <div class="flex items-center flex-1">
                                            <!-- Game Image -->
                                            @if($game->image_url)
                                                <div class="w-16 h-9 rounded-lg overflow-hidden mr-3 flex-shrink-0">
                                                    <img src="{{ $game->image_url }}" 
                                                         alt="{{ $game->name }}" 
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1">
                                                <div class="text-white font-semibold font-['Share_Tech_Mono'] text-sm">{{ $game->name }}</div>
                                                <div class="text-[#A1A1AA] text-xs font-['Inter'] mt-1">{{ Str::limit($game->description, 40) }}</div>
                                                
                                                <!-- Game Info -->
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($game->genre)
                                                        <span class="bg-[#7C3AED]/20 text-[#7C3AED] px-1.5 py-0.5 rounded text-xs">
                                                            {{ $game->genre->name }}
                                                        </span>
                                                    @endif
                                                    @if($game->platform)
                                                        <span class="bg-[#2563EB]/20 text-[#2563EB] px-1.5 py-0.5 rounded text-xs">
                                                            {{ $game->platform->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 ml-3">
                                            <a href="{{ route('games.show', $game->slug) }}" 
                                               target="_blank"
                                               class="text-[#A1A1AA] hover:text-white transition-colors p-1.5 rounded hover:bg-[#52525B]"
                                               title="View Game">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button wire:click="addGameToList({{ $game->id }})" 
                                                    class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors font-['Inter']">
                                                Add
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(strlen($searchTerm) >= 2)
                            <div class="text-center py-4">
                                <p class="text-[#A1A1AA] font-['Inter']">No games found for "{{ $searchTerm }}"</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Games in List -->
                @if($currentList->items->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($currentList->items as $item)
                            <a href="{{ route('games.show', $item->product->slug) }}" target="_blank" class="block group">
                                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-4 hover:border-[#52525B] group-hover:border-[#7C3AED] transition-all duration-200 relative overflow-hidden">
                                    <!-- Game Image -->
                                    @if($item->product->image_url)
                                        <div class="mb-3 rounded-lg overflow-hidden aspect-video">
                                            <img src="{{ $item->product->image_url }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold font-['Share_Tech_Mono'] text-sm group-hover:text-[#7C3AED] transition-colors">{{ $item->product->name }}</h4>
                                            <p class="text-[#A1A1AA] text-xs font-['Inter'] mt-1 leading-relaxed">{{ Str::limit($item->product->description, 60) }}</p>
                                            
                                            <!-- Game Info -->
                                            <div class="flex items-center gap-2 mt-2">
                                                @if($item->product->overall_rating)
                                                    <div class="flex items-center">
                                                        <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                        <span class="text-[#A1A1AA] text-xs">{{ number_format($item->product->overall_rating, 1) }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if($item->product->genre)
                                                    <span class="bg-[#7C3AED]/20 text-[#7C3AED] px-2 py-1 rounded text-xs font-semibold">
                                                        {{ $item->product->genre->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Remove Button (only for editors and above) -->
                                        @php
                                            $isOwner = $currentList->user_id === auth()->id();
                                            $userRole = $isOwner ? 'owner' : ($currentList->user_role ?? 'view');
                                            $canEdit = $userRole === 'owner' || in_array($userRole, ['edit', 'admin']);
                                        @endphp
                                        
                                        @if($canEdit)
                                            <button wire:click.stop="removeGameFromList({{ $item->product->id }})" 
                                                    onclick="return confirm('Remove this game from the list?')"
                                                    class="ml-3 text-[#E53E3E] hover:text-[#DC2626] transition-colors p-1 rounded hover:bg-[#E53E3E]/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a1 1 0 011-1h6a1 1 0 011 1v2M7 7h10" />
                        </svg>
                        <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono'] mb-2">No games in this list</h3>
                        @php
                            $isOwner = $currentList->user_id === auth()->id();
                            $userRole = $isOwner ? 'owner' : ($currentList->user_role ?? 'view');
                            $canEdit = $userRole === 'owner' || in_array($userRole, ['edit', 'admin']);
                        @endphp
                        
                        @if($canEdit)
                            <p class="text-[#A1A1AA] font-['Inter'] mb-4">Start adding games to build your collection</p>
                            <button wire:click="$set('showSearch', true)" 
                                    class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold transition-colors font-['Inter']">
                                Add Your First Game
                            </button>
                        @else
                            <p class="text-[#A1A1AA] font-['Inter'] mb-4">This list is empty. You have {{ ucfirst($userRole) }} access to view this collaborative list.</p>
                            <div class="bg-[#3F3F46] text-[#A1A1AA] px-6 py-3 rounded-lg font-['Inter'] inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Only Access
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    @endif
    
    <!-- Collaboration Manager Modal -->
    @if($showCollaborationManager && $managingListId)
        @php
            $managingList = $lists->firstWhere('id', $managingListId);
        @endphp
        
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" wire:click="closeCollaborationManager">
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto mx-4" wire:click.stop>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Manage Collaborators</h3>
                        <p class="text-[#A1A1AA] font-['Inter']">{{ $managingList->name ?? 'List' }}</p>
                    </div>
                    <button wire:click="closeCollaborationManager" 
                            class="text-[#A1A1AA] hover:text-white transition-colors p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Invite New Collaborator -->
                <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-6 mb-6">
                    <h4 class="text-lg font-bold text-white font-['Share_Tech_Mono'] mb-4">Invite New Collaborator</h4>
                    
                    <form wire:submit.prevent="sendInvitation" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-white mb-2">Email Address</label>
                            <input type="email" wire:model="inviteEmail" placeholder="Enter user's email address"
                                   class="w-full bg-[#27272A] border border-[#3F3F46] rounded-lg px-4 py-3 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                            @error('inviteEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-white mb-3">Permissions</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model="invitePermissions.can_add_games" 
                                           class="w-4 h-4 text-[#7C3AED] bg-[#27272A] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <span class="text-sm text-white">Add Games</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model="invitePermissions.can_delete_games" 
                                           class="w-4 h-4 text-[#7C3AED] bg-[#27272A] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <span class="text-sm text-white">Remove Games</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model="invitePermissions.can_rename_list" 
                                           class="w-4 h-4 text-[#7C3AED] bg-[#27272A] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <span class="text-sm text-white">Rename List</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model="invitePermissions.can_manage_users" 
                                           class="w-4 h-4 text-[#7C3AED] bg-[#27272A] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <span class="text-sm text-white">Manage Users</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model="invitePermissions.can_change_privacy" 
                                           class="w-4 h-4 text-[#7C3AED] bg-[#27272A] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <span class="text-sm text-white">Change Privacy</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model="invitePermissions.can_change_category" 
                                           class="w-4 h-4 text-[#7C3AED] bg-[#27272A] border-[#3F3F46] rounded focus:ring-[#7C3AED] focus:ring-2">
                                    <span class="text-sm text-white">Change Category</span>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                            Send Invitation
                        </button>
                    </form>
                </div>
                
                <!-- Current Collaborators -->
                @if($managingList && $managingList->collaborators->count() > 0)
                    <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-6">
                        <h4 class="text-lg font-bold text-white font-['Share_Tech_Mono'] mb-4">Current Collaborators</h4>
                        
                        <div class="space-y-4">
                            @foreach($managingList->collaborators as $collaborator)
                                <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-[#7C3AED] rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($collaborator->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-white font-semibold">{{ $collaborator->user->name }}</div>
                                                <div class="text-[#A1A1AA] text-sm">{{ $collaborator->user->email }}</div>
                                                @if($collaborator->accepted_at)
                                                    <div class="text-[#22C55E] text-xs">Active since {{ $collaborator->accepted_at->format('M j, Y') }}</div>
                                                @else
                                                    <div class="text-[#F59E0B] text-xs">Invitation pending</div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <!-- Permission Summary -->
                                            <div class="text-right">
                                                <div class="text-sm text-[#7C3AED] font-semibold">
                                                    {{ $collaborator->getPermissionSummary() }}
                                                </div>
                                                <div class="text-xs text-[#A1A1AA]">
                                                    @php
                                                        $permissions = [];
                                                        if ($collaborator->can_add_games) $permissions[] = 'Add';
                                                        if ($collaborator->can_delete_games) $permissions[] = 'Remove';
                                                        if ($collaborator->can_rename_list) $permissions[] = 'Rename';
                                                        if ($collaborator->can_manage_users) $permissions[] = 'Manage';
                                                        if ($collaborator->can_change_privacy) $permissions[] = 'Privacy';
                                                        if ($collaborator->can_change_category) $permissions[] = 'Category';
                                                    @endphp
                                                    {{ implode(', ', $permissions) ?: 'View Only' }}
                                                </div>
                                            </div>
                                            
                                            <!-- Remove Button -->
                                            <button wire:click="removeCollaboratorFromManager({{ $collaborator->id }})"
                                                    onclick="return confirm('Remove this collaborator from the list?')"
                                                    class="text-[#EF4444] hover:text-[#DC2626] p-2 rounded hover:bg-[#EF4444]/20 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('copy-to-clipboard', (event) => {
            const text = event.text || event[0]?.text;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    console.log('Link copied successfully');
                }).catch((err) => {
                    console.error('Failed to copy: ', err);
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        });
    });

    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        textArea.style.opacity = "0";
        
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                console.log('Link copied using fallback method');
            } else {
                console.error('Fallback copy failed');
            }
        } catch (err) {
            console.error('Fallback copy error: ', err);
        }
        
        document.body.removeChild(textArea);
    }
</script> 