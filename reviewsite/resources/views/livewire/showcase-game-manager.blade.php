<div class="max-w-6xl mx-auto p-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-gradient-to-r from-green-500/20 to-green-600/20 border border-green-500/30 rounded-xl p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-green-100 font-['Inter']">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-gradient-to-r from-red-500/20 to-red-600/20 border border-red-500/30 rounded-xl p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-red-100 font-['Inter']">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono'] mb-2">Game Showcase</h1>
                <p class="text-[#A1A1AA] text-lg font-['Inter']">Manage games displayed on your streamer profile</p>
            </div>
            <button wire:click="openAddModal" 
                    class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Game
            </button>
        </div>
    </div>

    <!-- Currently Showcased Games -->
    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Currently Showcased</h2>
            <div class="text-sm text-[#A1A1AA] font-['Inter']">
                {{ $showcasedGames->count() }}/{{ $maxShowcasedGames }} games
            </div>
        </div>

        @if($showcasedGames->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($showcasedGames as $showcasedGame)
                    @php
                        $product = $showcasedGame->gameUserStatus->product;
                        $status = $showcasedGame->gameUserStatus;
                    @endphp
                    <div class="bg-gradient-to-br from-[#3F3F46] to-[#27272A] rounded-xl border border-[#52525B] overflow-hidden">
                        <!-- Game Image -->
                        <div class="aspect-video relative">
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#3B82F6] to-[#1D4ED8] flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                <span class="bg-[#2563EB] text-white text-xs px-2 py-1 rounded font-semibold">
                                    #{{ $showcasedGame->display_order }}
                                </span>
                            </div>
                        </div>

                        <!-- Game Info -->
                        <div class="p-4">
                            <h3 class="font-bold text-white text-lg mb-1 font-['Inter']">{{ $product->name }}</h3>
                            <div class="flex items-center gap-2 mb-2">
                                @if($product->genre)
                                    <span class="text-xs bg-[#3B82F6]/20 text-[#3B82F6] px-2 py-1 rounded">
                                        {{ $product->genre->name }}
                                    </span>
                                @endif
                                @if($status->completion_status)
                                    <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded">
                                        {{ ucwords(str_replace('_', ' ', $status->completion_status)) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Showcase Note -->
                            @if($showcasedGame->showcase_note)
                                <p class="text-[#A1A1AA] text-sm mb-3 font-['Inter']">{{ $showcasedGame->showcase_note }}</p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-3 border-t border-[#52525B]">
                                <div class="flex items-center gap-1">
                                    <button wire:click="moveUp({{ $showcasedGame->id }})" 
                                            class="p-1 text-[#A1A1AA] hover:text-[#2563EB] transition-colors" 
                                            title="Move Up">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l5-5 5 5"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="moveDown({{ $showcasedGame->id }})" 
                                            class="p-1 text-[#A1A1AA] hover:text-[#2563EB] transition-colors" 
                                            title="Move Down">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 10l-5 5-5-5"></path>
                                        </svg>
                                    </button>
                                </div>
                                <button wire:click="removeFromShowcase({{ $showcasedGame->id }})" 
                                        wire:confirm="Are you sure you want to remove this game from your showcase?"
                                        class="text-red-400 hover:text-red-300 transition-colors p-1" 
                                        title="Remove from Showcase">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-gradient-to-br from-[#3F3F46] to-[#27272A] rounded-2xl p-8 max-w-md mx-auto">
                    <svg class="w-16 h-16 text-[#3F3F46] mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">No Games Showcased</h3>
                    <p class="text-[#A1A1AA] font-['Inter']">
                        Add games from your collection to showcase what you're currently playing or your favorites.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Add Game Modal -->
    @if($showAddModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] max-w-2xl w-full max-h-[80vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-[#3F3F46]">
                    <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">Add Game to Showcase</h3>
                    <button wire:click="closeAddModal" class="text-[#A1A1AA] hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="overflow-y-auto max-h-[60vh]">
                    <!-- Search Bar -->
                    <div class="p-6 border-b border-[#3F3F46]">
                        <div class="relative">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Search your games..."
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 pl-12 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] font-['Inter']">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-[#71717A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Available Games -->
                    <div class="p-6">
                        @if($availableGames->isNotEmpty())
                            <div class="grid grid-cols-1 gap-3 mb-4">
                                @foreach($availableGames as $gameStatus)
                                    @php $product = $gameStatus->product; @endphp
                                    <div class="border border-[#3F3F46] rounded-lg p-4 hover:border-[#2563EB] transition-colors cursor-pointer {{ $selectedGame && $selectedGame->id === $gameStatus->id ? 'border-[#2563EB] bg-[#2563EB]/10' : 'hover:bg-[#3F3F46]/20' }}"
                                         wire:click="selectGame({{ $gameStatus->id }})">
                                        <div class="flex items-center gap-4">
                                            @if($product->image)
                                                <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                                     class="w-16 h-16 object-cover rounded">
                                            @else
                                                <div class="w-16 h-16 bg-gradient-to-br from-[#3B82F6] to-[#1D4ED8] rounded flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-white font-['Inter']">{{ $product->name }}</h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($product->genre)
                                                        <span class="text-xs bg-[#3B82F6]/20 text-[#3B82F6] px-2 py-1 rounded">
                                                            {{ $product->genre->name }}
                                                        </span>
                                                    @endif
                                                    @if($gameStatus->completion_status)
                                                        <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded">
                                                            {{ ucwords(str_replace('_', ' ', $gameStatus->completion_status)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($selectedGame && $selectedGame->id === $gameStatus->id)
                                                <div class="text-[#2563EB]">
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if($availableGames->hasPages())
                                <div class="flex justify-center">
                                    {{ $availableGames->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <p class="text-[#A1A1AA] font-['Inter']">
                                    @if($search)
                                        No games found matching "{{ $search }}".
                                    @else
                                        You have no games in your collection to showcase.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Selected Game Details & Actions -->
                @if($selectedGame)
                    <div class="border-t border-[#3F3F46] p-6">
                        <div class="mb-4">
                            <label for="showcaseNote" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                                Showcase Note (Optional)
                            </label>
                            <textarea wire:model="showcaseNote" 
                                      id="showcaseNote"
                                      placeholder="Add a note about why you're showcasing this game..."
                                      class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] font-['Inter'] resize-none h-20"></textarea>
                            @error('showcaseNote') 
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center justify-end gap-3">
                            <button wire:click="closeAddModal" 
                                    class="px-4 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter']">
                                Cancel
                            </button>
                            <button wire:click="addToShowcase" 
                                    class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-6 py-2 rounded-xl font-semibold transition-all duration-200 font-['Inter']">
                                Add to Showcase
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
