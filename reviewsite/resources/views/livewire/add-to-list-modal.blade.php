<div>
    <!-- Add to Lists Button -->
    <button wire:click="openModal" class="{{ $buttonClass }}">
        <svg class="{{ $iconSize }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ $buttonText }}
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] w-full max-w-lg p-6 relative">
                <!-- Close Button -->
                <button wire:click="closeModal" class="absolute top-4 right-4 text-[#A1A1AA] hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Add to List</h2>
                    <p class="text-[#A1A1AA] text-sm mt-1 font-['Inter']">Choose a list or create a new one</p>
                </div>

                <!-- Success Message -->
                @if($successMessage)
                    <div class="mb-4 p-3 bg-[#22C55E]/20 border border-[#22C55E]/30 rounded-lg">
                        <div class="text-[#22C55E] font-semibold text-sm font-['Inter']">{{ $successMessage }}</div>
                    </div>
                @endif

                <!-- Loading State -->
                <div wire:loading class="mb-4 p-3 bg-[#2563EB]/20 border border-[#2563EB]/30 rounded-lg">
                    <div class="text-[#2563EB] font-semibold text-sm font-['Inter'] flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-[#2563EB]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </div>
                </div>

                <!-- Lists -->
                <div class="space-y-3 mb-6 max-h-60 overflow-y-auto">
                    @forelse($lists as $list)
                        <div class="flex items-center justify-between bg-[#18181B] rounded-lg px-4 py-3 border border-[#3F3F46] hover:border-[#52525B] transition-colors">
                            <div class="flex-1">
                                <div class="text-white font-semibold font-['Share_Tech_Mono'] text-sm">{{ $list->name }}</div>
                                <div class="text-xs text-[#A1A1AA] font-['Inter'] mt-1">
                                    @if($list->items->count())
                                        <span class="text-[#22C55E]">✓ Already in this list</span>
                                    @else
                                        {{ $list->items()->count() }} games in list
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                @if($list->items->count())
                                    <button wire:click="removeFromList({{ $list->id }})" 
                                            class="px-3 py-1.5 bg-[#E53E3E] hover:bg-[#DC2626] text-white rounded-lg text-xs font-semibold transition-colors font-['Inter']">
                                        Remove
                                    </button>
                                @else
                                    <button wire:click="addToList({{ $list->id }})" 
                                            class="px-3 py-1.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white rounded-lg text-xs font-semibold transition-colors font-['Inter']">
                                        Add
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto mb-3 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <p class="text-[#A1A1AA] font-['Inter']">No lists yet</p>
                            <p class="text-[#71717A] text-sm font-['Inter']">Create your first list below</p>
                        </div>
                    @endforelse
                </div>

                <!-- Create New List -->
                <div class="border-t border-[#3F3F46] pt-4">
                    @if(!$showCreate)
                        <button wire:click="$set('showCreate', true)" class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white py-3 px-4 rounded-lg font-semibold text-sm transition-colors font-['Inter'] flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create New List
                        </button>
                    @else
                        <form wire:submit.prevent="createList" class="space-y-3">
                            <div>
                                <input type="text" wire:model.defer="newListName" placeholder="Enter list name..." class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']" />
                                @error('newListName')
                                    <div class="text-[#E53E3E] text-xs mt-1 font-['Inter']">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <textarea wire:model.defer="newListDescription" placeholder="Description (optional)..." rows="2" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter'] resize-none"></textarea>
                                @error('newListDescription')
                                    <div class="text-[#E53E3E] text-xs mt-1 font-['Inter']">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-[#7C3AED] hover:bg-[#6D28D9] text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors font-['Inter']">
                                    Create
                                </button>
                                <button type="button" wire:click="$set('showCreate', false)" class="px-4 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
