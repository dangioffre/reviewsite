<div class="space-y-6">
    <!-- Success Message -->
    @if($successMessage)
        <div class="p-4 bg-[#22C55E]/20 border border-[#22C55E]/30 rounded-lg">
            <div class="text-[#22C55E] font-semibold text-sm font-['Inter']">{{ $successMessage }}</div>
        </div>
    @endif

    @if(!$viewingList)
        <!-- Main Lists View -->
        <div>
            <!-- Header with Create Button -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">My Lists</h2>
                    <p class="text-[#A1A1AA] text-sm mt-1 font-['Inter']">Create and manage your game lists</p>
                </div>
                @if(!$showCreate)
                    <button wire:click="$set('showCreate', true)" 
                            class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors font-['Inter'] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        New List
                    </button>
                @endif
            </div>

            <!-- Create New List Form -->
            @if($showCreate)
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 mb-6">
                    <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono'] mb-4">Create New List</h3>
                    <form wire:submit.prevent="createList" class="space-y-4">
                        <div>
                            <input type="text" wire:model.defer="newListName" placeholder="Enter list name..." 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']" />
                            @error('newListName')
                                <div class="text-[#E53E3E] text-xs mt-1 font-['Inter']">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-2 rounded-lg font-semibold text-sm transition-colors font-['Inter']">
                                Create List
                            </button>
                            <button type="button" wire:click="$set('showCreate', false)" 
                                    class="px-6 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Lists Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($lists as $list)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 hover:border-[#52525B] transition-all duration-200">
                        <!-- List Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                @if($editingList === $list->id)
                                    <form wire:submit.prevent="saveEdit" class="space-y-2">
                                        <input type="text" wire:model.defer="editingName" 
                                               class="w-full bg-[#18181B] border border-[#3F3F46] rounded px-2 py-1 text-white text-lg font-bold font-['Share_Tech_Mono'] focus:outline-none focus:ring-1 focus:ring-[#2563EB]" />
                                        <div class="flex gap-2">
                                            <button type="submit" class="text-[#22C55E] hover:text-[#16A34A] text-xs">Save</button>
                                            <button type="button" wire:click="cancelEdit" class="text-[#A1A1AA] hover:text-white text-xs">Cancel</button>
                                        </div>
                                    </form>
                                @else
                                    <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono']">{{ $list->name }}</h3>
                                @endif
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-[#A1A1AA] text-sm font-['Inter']">{{ $list->items_count }} games</span>
                                    @if($list->is_public)
                                        <span class="bg-[#22C55E]/20 text-[#22C55E] px-2 py-1 rounded text-xs font-semibold">Public</span>
                                    @else
                                        <span class="bg-[#71717A]/20 text-[#A1A1AA] px-2 py-1 rounded text-xs font-semibold">Private</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- List Actions -->
                        <div class="space-y-3">
                            <!-- Primary Actions -->
                            <div class="flex gap-2">
                                <button wire:click="viewList({{ $list->id }})" 
                                        class="flex-1 bg-[#2563EB] hover:bg-[#1D4ED8] text-white py-2 px-3 rounded-lg font-semibold text-sm transition-colors font-['Inter'] flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View
                                </button>
                                @if($editingList !== $list->id)
                                    <button wire:click="startEditing({{ $list->id }})" 
                                            class="bg-[#18181B] hover:bg-[#27272A] text-[#A1A1AA] hover:text-white py-2 px-3 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            <!-- Secondary Actions -->
                            <div class="flex gap-2 text-sm">
                                <button wire:click="togglePublic({{ $list->id }})" 
                                        class="flex-1 text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] text-center py-1">
                                    {{ $list->is_public ? 'Make Private' : 'Make Public' }}
                                </button>
                                @if($list->is_public)
                                    <button wire:click="copyPublicLink({{ $list->id }})" 
                                            class="flex-1 text-[#2563EB] hover:text-[#1D4ED8] transition-colors font-['Inter'] text-center py-1">
                                        Copy Link
                                    </button>
                                @endif
                                <button wire:click="deleteList({{ $list->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this list?')"
                                        class="flex-1 text-[#E53E3E] hover:text-[#DC2626] transition-colors font-['Inter'] text-center py-1">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono'] mb-2">No lists yet</h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mb-4">Create your first list to get started</p>
                        <button wire:click="$set('showCreate', true)" 
                                class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold transition-colors font-['Inter']">
                            Create Your First List
                        </button>
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
                    <button wire:click="$set('showSearch', true)" 
                            class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors font-['Inter'] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Games
                    </button>
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
                            <input type="text" wire:model="searchTerm" placeholder="Search for games..." 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-3 pl-10 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']" />
                            <svg class="w-5 h-5 text-[#71717A] absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        @if(count($searchResults) > 0)
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach($searchResults as $game)
                                    <div class="flex items-center justify-between bg-[#18181B] rounded-lg px-4 py-3 border border-[#3F3F46]">
                                        <div>
                                            <div class="text-white font-semibold font-['Share_Tech_Mono'] text-sm">{{ $game->name }}</div>
                                            <div class="text-[#A1A1AA] text-xs font-['Inter'] mt-1">{{ Str::limit($game->description, 60) }}</div>
                                        </div>
                                        <button wire:click="addGameToList({{ $game->id }})" 
                                                class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors font-['Inter']">
                                            Add
                                        </button>
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
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-4 hover:border-[#52525B] transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-white font-semibold font-['Share_Tech_Mono'] text-sm">{{ $item->product->name }}</h4>
                                        <p class="text-[#A1A1AA] text-xs font-['Inter'] mt-1">{{ Str::limit($item->product->description, 80) }}</p>
                                        <div class="flex items-center gap-2 mt-2">
                                            @if($item->product->overall_rating)
                                                <div class="flex items-center">
                                                    <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <span class="text-[#A1A1AA] text-xs">{{ number_format($item->product->overall_rating, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="removeGameFromList({{ $item->product->id }})" 
                                            onclick="return confirm('Remove this game from the list?')"
                                            class="ml-3 text-[#E53E3E] hover:text-[#DC2626] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a1 1 0 011-1h6a1 1 0 011 1v2M7 7h10" />
                        </svg>
                        <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono'] mb-2">No games in this list</h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mb-4">Start adding games to build your collection</p>
                        <button wire:click="$set('showSearch', true)" 
                                class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold transition-colors font-['Inter']">
                            Add Your First Game
                        </button>
                    </div>
                @endif
            </div>
        @endif
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