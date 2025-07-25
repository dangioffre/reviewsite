@props(['gameStatus'])

<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] overflow-hidden hover:border-[#2563EB] transition-all duration-300 group shadow-lg hover:shadow-xl">
    <!-- Game Header with Better Spacing -->
    <div class="flex items-start p-4 pb-3">
        <!-- Game Image with Better Sizing -->
        <div class="w-16 h-16 bg-gradient-to-br from-[#2563EB] to-[#1E40AF] rounded-lg overflow-hidden shrink-0 mr-4 group-hover:scale-105 transition-transform duration-300 shadow-md">
            @if($gameStatus->product->image)
                <img src="{{ $gameStatus->product->image }}" alt="{{ $gameStatus->product->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Game Info with Better Layout -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-white mb-2 group-hover:text-[#3B82F6] transition-colors font-['Share_Tech_Mono'] line-clamp-2 leading-tight">
                        <a href="{{ route('games.show', $gameStatus->product->slug) }}" class="hover:underline">{{ $gameStatus->product->name }}</a>
                    </h3>
                    
                    <!-- Genre and Platform with Better Spacing -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($gameStatus->product->genre)
                            <span class="text-xs px-2 py-1 rounded-md font-medium" style="background-color: {{ $gameStatus->product->genre->color ?? '#2563EB' }}20; color: {{ $gameStatus->product->genre->color ?? '#2563EB' }};">
                                {{ $gameStatus->product->genre->name }}
                            </span>
                        @endif
                        
                        @if($gameStatus->platform_played)
                            <span class="text-xs px-2 py-1 rounded-md bg-[#18181B] text-[#A1A1AA] font-medium">
                                {{ $gameStatus->platform_played }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Status Badges with Better Spacing -->
                <div class="flex gap-1 ml-3">
                    @if($gameStatus->have)
                        <span class="inline-flex items-center text-xs px-2 py-1 rounded-md bg-green-600/20 text-green-400 font-medium">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            Own
                        </span>
                    @endif
                    
                    @if($gameStatus->want)
                        <span class="inline-flex items-center text-xs px-2 py-1 rounded-md bg-blue-600/20 text-blue-400 font-medium">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"/>
                            </svg>
                            Want
                        </span>
                    @endif
                    
                    @if($gameStatus->is_favorite)
                        <span class="inline-flex items-center text-xs px-2 py-1 rounded-md bg-red-500/20 text-red-400 font-medium">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                            </svg>
                            Fav
                        </span>
                    @endif
                    
                    @if($gameStatus->rating)
                        <span class="inline-flex items-center text-xs px-2 py-1 rounded-md bg-yellow-500/20 text-yellow-400 font-medium">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ $gameStatus->rating }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Details with Better Spacing -->
    @if($gameStatus->completion_status)
        @php $statusDetails = $gameStatus->getStatusDetails(); @endphp
        <div class="px-4 py-3 border-t border-[#3F3F46] bg-[#18181B]/30">
            <!-- Status and Progress with Better Layout -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <div class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $statusDetails['color'] }};"></div>
                    <span class="text-sm font-semibold text-white">{{ $statusDetails['label'] }}</span>
                    @if($gameStatus->completion_percentage)
                        <span class="text-sm text-[#A1A1AA] ml-2">({{ $gameStatus->completion_percentage }}%)</span>
                    @endif
                </div>
                
                @if($gameStatus->hours_played)
                    <span class="text-sm text-[#A1A1AA] font-medium">{{ $gameStatus->formatted_play_time }}</span>
                @endif
            </div>

            <!-- Progress Bar with Better Styling -->
            @if($gameStatus->completion_percentage)
                <div class="mb-3">
                    <div class="w-full bg-[#18181B] rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-300 shadow-sm" 
                             style="width: {{ $gameStatus->completion_percentage }}%; background-color: {{ $gameStatus->getCompletionBadgeColor() }};">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Dates and Info with Better Spacing -->
            @if($gameStatus->started_date || $gameStatus->completed_date || $gameStatus->times_replayed > 0)
                <div class="flex flex-wrap gap-3 text-sm text-[#A1A1AA] mb-3">
                    @if($gameStatus->started_date)
                        <span class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Started: {{ $gameStatus->started_date->format('M j') }}
                        </span>
                    @endif
                    @if($gameStatus->completed_date)
                        <span class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Completed: {{ $gameStatus->completed_date->format('M j') }}
                        </span>
                    @endif
                    @if($gameStatus->times_replayed > 0)
                        <span class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ $gameStatus->times_replayed }}x replayed
                        </span>
                    @endif
                </div>
            @endif

            <!-- Notes with Better Styling -->
            @if($gameStatus->notes)
                <div class="p-3 bg-[#18181B] rounded-lg text-sm mb-3 border border-[#3F3F46]">
                    <span class="text-[#A1A1AA] font-medium">Notes:</span>
                    <span class="text-white ml-2">{{ Str::limit($gameStatus->notes, 80) }}</span>
                </div>
            @endif

            <!-- Dropped Status with Better Styling -->
            @if($gameStatus->dropped)
                <div class="p-3 bg-red-500/10 rounded-lg border border-red-500/30">
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                        </svg>
                        <span class="text-red-400 font-semibold">Dropped</span>
                        @if($gameStatus->dropped_date)
                            <span class="text-red-400/70 ml-2">{{ $gameStatus->dropped_date->format('M j') }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="px-4 py-4 border-t border-[#3F3F46] bg-[#18181B]/30">
            <p class="text-sm text-[#71717A] text-center font-medium">No status set</p>
        </div>
    @endif

    <!-- Action Footer with Better Spacing -->
    <div class="px-4 py-4 border-t border-[#3F3F46] bg-gradient-to-br from-[#18181B] to-[#27272A]">
        <!-- Enhanced Status Buttons with Better Spacing -->
        <div class="mb-1">
            <livewire:enhanced-game-status-buttons :product="$gameStatus->product" :timestamp="$gameStatus->updated_at->diffForHumans()" :key="'collection-'.$gameStatus->id" />
        </div>
</div>
