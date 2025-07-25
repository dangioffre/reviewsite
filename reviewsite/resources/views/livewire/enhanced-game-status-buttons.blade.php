<div class="w-full">
    <!-- Segmented Control Status Buttons -->
    <div class="flex justify-center mb-6">
        <div class="flex bg-[#232326] border border-[#292929] rounded-full p-1 gap-1 shadow-sm">
            <!-- Own -->
            <button wire:click="toggle('have')"
                class="flex items-center gap-2 px-5 py-2 rounded-full font-medium text-sm font-['Inter'] transition-all duration-200 focus:outline-none relative group/own
                {{ $have ? 'bg-[#DC2626] text-white shadow' : 'bg-transparent text-[#A0A0A0] hover:bg-[#292929] hover:text-white' }}"
                aria-label="Own">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Own
                <!-- Tooltip with arrow -->
                <span class="absolute left-1/2 -bottom-9 -translate-x-1/2 z-20 px-3 py-1.5 bg-[#232326] text-xs text-white rounded shadow-lg opacity-0 group-hover/own:opacity-100 group-focus/own:opacity-100 transition pointer-events-none whitespace-nowrap flex flex-col items-center">
                    You own this game
                    <span class="w-2 h-2 bg-[#232326] rotate-45 mt-[-3px] shadow-lg" style="margin-top:-3px;"></span>
                </span>
            </button>
            <!-- Want -->
            <button wire:click="toggle('want')"
                class="flex items-center gap-2 px-5 py-2 rounded-full font-medium text-sm font-['Inter'] transition-all duration-200 focus:outline-none relative group/want
                {{ $want ? 'bg-[#2563EB] text-white shadow' : 'bg-transparent text-[#A0A0A0] hover:bg-[#292929] hover:text-white' }}"
                aria-label="Want">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Want
                <span class="absolute left-1/2 -bottom-9 -translate-x-1/2 z-20 px-3 py-1.5 bg-[#232326] text-xs text-white rounded shadow-lg opacity-0 group-hover/want:opacity-100 group-focus/want:opacity-100 transition pointer-events-none whitespace-nowrap flex flex-col items-center">
                    Add to wishlist
                    <span class="w-2 h-2 bg-[#232326] rotate-45 mt-[-3px] shadow-lg" style="margin-top:-3px;"></span>
                </span>
            </button>
            <!-- Play -->
            <button wire:click="openPlayedModal"
                class="flex items-center gap-2 px-5 py-2 rounded-full font-medium text-sm font-['Inter'] transition-all duration-200 focus:outline-none relative group/play
                {{ $played ? 'bg-[#4CAF50] text-white shadow' : 'bg-transparent text-[#A0A0A0] hover:bg-[#292929] hover:text-white' }}"
                aria-label="Play">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="5,3 19,12 5,21 5,3" fill="currentColor" class="{{ $played ? 'text-white' : 'text-[#A0A0A0]' }}" />
                </svg>
                {{ $played ? 'Played' : 'Play' }}
                <span class="absolute left-1/2 -bottom-9 -translate-x-1/2 z-20 px-3 py-1.5 bg-[#232326] text-xs text-white rounded shadow-lg opacity-0 group-hover/play:opacity-100 group-focus/play:opacity-100 transition pointer-events-none whitespace-nowrap flex flex-col items-center">
                    Mark as played
                    <span class="w-2 h-2 bg-[#232326] rotate-45 mt-[-3px] shadow-lg" style="margin-top:-3px;"></span>
                </span>
            </button>
        </div>
    </div>

    <!-- Additional Actions (when user has status) -->
    @if($userStatus)
        <div class="flex flex-wrap justify-center gap-2 mb-4">
            @if($played)
                <button wire:click="unselectPlay" class="px-3 py-1.5 rounded-lg text-xs text-[#71717A] hover:text-[#A0A0A0] hover:bg-[#27272A] transition-all duration-200 focus:outline-none flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Unselect Play
                </button>
            @endif
            <button wire:click="confirmDelete" class="px-3 py-1.5 rounded-lg text-xs text-[#71717A] hover:text-red-400 hover:bg-[#27272A] transition-all duration-200 focus:outline-none flex items-center gap-1.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Remove
            </button>
        </div>
    @endif

    <!-- Edit Details Button (when user has status) -->
    @if($userStatus && $userStatus->completion_status)
        <div class="text-center mb-4">
            <button wire:click="openDetailModal" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                Edit Details
            </button>
        </div>
    @endif

    <!-- Quick Status Buttons (when played but no detailed status) -->
    @if($played && (!$userStatus || !$userStatus->completion_status))
        <div class="text-center mb-4">
            <p class="text-xs text-[#A0A0A0] mb-2">Quick Status:</p>
            <div class="flex flex-wrap justify-center gap-1">
                @foreach($this->getCompletionStatuses() as $key => $status)
                    <button wire:click="quickSetStatus('{{ $key }}')" 
                            class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-105"
                            style="background-color: {{ $status['color'] }}20; color: {{ $status['color'] }}; border: 1px solid {{ $status['color'] }}40;">
                        {{ $status['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Detailed Status Modal -->
    @if($showDetailModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4" wire:click="closeDetailModal">
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Game Progress Details</h3>
                    <button wire:click="closeDetailModal" class="text-[#A1A1AA] hover:text-white transition-colors p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveDetails" class="space-y-6">
                    <!-- Completion Status -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Current Status</label>
                        <select wire:model="completion_status" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                            @foreach($this->getCompletionStatuses() as $key => $status)
                                <option value="{{ $key }}">{{ $status['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Progress and Time -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Completion Percentage</label>
                            <input type="number" wire:model="completion_percentage" min="0" max="100" 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']"
                                   placeholder="0-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Hours Played</label>
                            <input type="number" wire:model="hours_played" min="0" step="0.5" 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']"
                                   placeholder="0">
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Started Date</label>
                            <input type="date" wire:model="started_date" 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Completed Date</label>
                            <input type="date" wire:model="completed_date" 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                        </div>
                    </div>

                    <!-- Rating and Platform -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Personal Rating (1-10)</label>
                            <input type="number" wire:model="rating" min="1" max="10" 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']"
                                   placeholder="1-10">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Platform Played</label>
                            <select wire:model="platform_played" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                                <option value="">Select Platform</option>
                                <option value="PC">PC</option>
                                <option value="PlayStation 5">PlayStation 5</option>
                                <option value="PlayStation 4">PlayStation 4</option>
                                <option value="Xbox Series X">Xbox Series X</option>
                                <option value="Xbox One">Xbox One</option>
                                <option value="Nintendo Switch">Nintendo Switch</option>
                                <option value="Mobile">Mobile</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Times Replayed</label>
                            <input type="number" wire:model="times_replayed" min="0" 
                                   class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']"
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Difficulty</label>
                            <select wire:model="difficulty_played" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                                <option value="">Select Difficulty</option>
                                <option value="Easy">Easy</option>
                                <option value="Normal">Normal</option>
                                <option value="Hard">Hard</option>
                                <option value="Extreme">Extreme</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Personal Notes</label>
                        <textarea wire:model="notes" rows="4" 
                                  class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter'] resize-none"
                                  placeholder="Share your thoughts, memories, or experiences with this game..."></textarea>
                    </div>

                    <!-- Drop Status -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="dropped" id="dropped" class="w-4 h-4 text-[#2563EB] bg-[#18181B] border-[#3F3F46] rounded focus:ring-[#2563EB] focus:ring-2">
                            <label for="dropped" class="ml-2 text-sm text-white font-['Inter']">I dropped/abandoned this game</label>
                        </div>
                        
                        @if($dropped)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Drop Date</label>
                                    <input type="date" wire:model="dropped_date" 
                                           class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-3 font-['Inter']">Reason for Dropping</label>
                                    <input type="text" wire:model="drop_reason" 
                                           class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter']"
                                           placeholder="e.g., Too difficult, lost interest, etc.">
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Favorite Toggle -->
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_favorite" id="is_favorite" class="w-4 h-4 text-[#2563EB] bg-[#18181B] border-[#3F3F46] rounded focus:ring-[#2563EB] focus:ring-2">
                        <label for="is_favorite" class="ml-2 text-sm text-white font-['Inter']">Mark as favorite</label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                            Save Details
                        </button>
                        <button type="button" wire:click="closeDetailModal" class="flex-1 border border-[#3F3F46] hover:border-[#71717A] text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Simple Played Modal -->
    @if($showPlayedModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4" wire:click="closePlayedModal">
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 w-full max-w-md" wire:click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">Set Game Status</h3>
                    <button wire:click="closePlayedModal" class="text-[#A1A1AA] hover:text-white transition-colors p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <p class="text-[#A1A1AA] text-sm">Choose your current status with this game:</p>
                    
                    <div class="grid grid-cols-1 gap-2">
                        @foreach($this->getCompletionStatuses() as $key => $status)
                            @if($key !== 'not_started')
                                <button wire:click="quickSetStatus('{{ $key }}')" 
                                        class="flex items-center p-3 rounded-xl transition-all duration-200 hover:scale-105"
                                        style="background-color: {{ $status['color'] }}15; border: 1px solid {{ $status['color'] }}40; color: {{ $status['color'] }};">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="{{ $status['icon'] }}" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="text-left">
                                        <div class="font-semibold">{{ $status['label'] }}</div>
                                        <div class="text-xs opacity-80">{{ $status['description'] }}</div>
                                    </div>
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-[#3F3F46]">
                        <button wire:click="openDetailModal" class="w-full bg-[#18181B] hover:bg-[#27272A] text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200 border border-[#3F3F46]">
                            Add Detailed Information
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[60] p-4" wire:click="closeDeleteModal">
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 w-full max-w-md" wire:click.stop>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-500/20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Remove Game</h3>
                    <p class="text-[#A1A1AA] mb-6">Are you sure you want to remove this game from your collection? This action cannot be undone.</p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="deleteFromCollection" class="flex-1 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                        Yes, Remove Game
                    </button>
                    <button wire:click="closeDeleteModal" class="flex-1 border border-[#3F3F46] hover:border-[#71717A] text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Toast Notification -->
    @if (session()->has('message'))
        <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-[#22C55E] to-[#16A34A] text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm font-semibold animate-fade-in-out flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <style>
    @keyframes fade-in-out {
        0% { opacity: 0; transform: translateY(10px); }
        10% { opacity: 1; transform: translateY(0); }
        90% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-10px); }
    }
    .animate-fade-in-out {
        animation: fade-in-out 3s both;
    }
    </style>
</div>
