@props(['gameStatus'])

<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-lg border border-[#3F3F46] overflow-hidden hover:border-[#2563EB] transition-all duration-300 group">
    <!-- Ultra Compact Game Header -->
    <div class="flex items-start p-3 pb-2">
        <!-- Smaller Game Image -->
        <div class="w-12 h-12 bg-gradient-to-br from-[#2563EB] to-[#1E40AF] rounded overflow-hidden shrink-0 mr-2 group-hover:scale-105 transition-transform duration-300">
            @if($gameStatus->product->image)
                <img src="{{ $gameStatus->product->image }}" alt="{{ $gameStatus->product->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Ultra Compact Game Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-white mb-1 group-hover:text-[#3B82F6] transition-colors font-['Share_Tech_Mono'] line-clamp-1">
                        <a href="{{ route('games.show', $gameStatus->product->slug) }}">{{ $gameStatus->product->name }}</a>
                    </h3>
                    
                    <!-- Inline Genre and Platform -->
                    <div class="flex flex-wrap gap-1 mb-1">
                        @if($gameStatus->product->genre)
                            <span class="text-xs px-1 py-0.5 rounded text-xs" style="background-color: {{ $gameStatus->product->genre->color ?? '#2563EB' }}20; color: {{ $gameStatus->product->genre->color ?? '#2563EB' }};">
                                {{ $gameStatus->product->genre->name }}
                            </span>
                        @endif
                        
                        @if($gameStatus->platform_played)
                            <span class="text-xs px-1 py-0.5 rounded bg-[#18181B] text-[#A1A1AA]">
                                {{ $gameStatus->platform_played }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Ultra Compact Status Badges -->
                <div class="flex gap-0.5 ml-1">
                    @if($gameStatus->have)
                        <span class="inline-flex items-center text-xs px-1 py-0.5 rounded bg-green-600/20 text-green-400">
                            <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                        </span>
                    @endif
                    
                    @if($gameStatus->want)
                        <span class="inline-flex items-center text-xs px-1 py-0.5 rounded bg-blue-600/20 text-blue-400">
                            <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"/>
                            </svg>
                        </span>
                    @endif
                    
                    @if($gameStatus->is_favorite)
                        <span class="inline-flex items-center text-xs px-1 py-0.5 rounded bg-red-500/20 text-red-400">
                            <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                            </svg>
                        </span>
                    @endif
                    
                    @if($gameStatus->rating)
                        <span class="inline-flex items-center text-xs px-1 py-0.5 rounded bg-yellow-500/20 text-yellow-400">
                            <svg class="w-2 h-2 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ $gameStatus->rating }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ultra Compact Status Details -->
    @if($gameStatus->completion_status)
        @php $statusDetails = $gameStatus->getStatusDetails(); @endphp
        <div class="px-3 py-1.5 border-t border-[#3F3F46]">
            <!-- Status and Progress in one line -->
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center">
                    <div class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $statusDetails['color'] }};"></div>
                    <span class="text-xs font-semibold text-white">{{ $statusDetails['label'] }}</span>
                    @if($gameStatus->completion_percentage)
                        <span class="text-xs text-[#A1A1AA] ml-1">({{ $gameStatus->completion_percentage }}%)</span>
                    @endif
                </div>
                
                @if($gameStatus->hours_played)
                    <span class="text-xs text-[#A1A1AA]">{{ $gameStatus->formatted_play_time }}</span>
                @endif
            </div>

            <!-- Ultra Compact Progress Bar -->
            @if($gameStatus->completion_percentage)
                <div class="mb-1.5">
                    <div class="w-full bg-[#18181B] rounded-full h-1">
                        <div class="h-1 rounded-full transition-all duration-300" 
                             style="width: {{ $gameStatus->completion_percentage }}%; background-color: {{ $gameStatus->getCompletionBadgeColor() }};">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ultra Compact Dates and Info -->
            @if($gameStatus->started_date || $gameStatus->completed_date || $gameStatus->times_replayed > 0)
                <div class="flex flex-wrap gap-2 text-xs text-[#A1A1AA] mb-1">
                    @if($gameStatus->started_date)
                        <span>Started: {{ $gameStatus->started_date->format('M j') }}</span>
                    @endif
                    @if($gameStatus->completed_date)
                        <span>Completed: {{ $gameStatus->completed_date->format('M j') }}</span>
                    @endif
                    @if($gameStatus->times_replayed > 0)
                        <span>{{ $gameStatus->times_replayed }}x replayed</span>
                    @endif
                </div>
            @endif

            <!-- Ultra Compact Notes -->
            @if($gameStatus->notes)
                <div class="p-1.5 bg-[#18181B] rounded text-xs mb-1">
                    <span class="text-[#A1A1AA]">Notes:</span>
                    <span class="text-white ml-1">{{ Str::limit($gameStatus->notes, 60) }}</span>
                </div>
            @endif

            <!-- Ultra Compact Dropped Status -->
            @if($gameStatus->dropped)
                <div class="mt-1 p-1.5 bg-red-500/10 rounded border border-red-500/30">
                    <div class="flex items-center text-xs">
                        <svg class="w-2.5 h-2.5 text-red-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                        </svg>
                        <span class="text-red-400 font-semibold">Dropped</span>
                        @if($gameStatus->dropped_date)
                            <span class="text-red-400/70 ml-1">{{ $gameStatus->dropped_date->format('M j') }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="px-3 py-1.5 border-t border-[#3F3F46]">
            <p class="text-xs text-[#71717A] text-center">No status set</p>
        </div>
    @endif

    <!-- Ultra Compact Action Footer -->
    <div class="px-3 py-1.5 border-t border-[#3F3F46] bg-[#18181B]/50">
        <div class="flex items-center justify-between text-xs mb-1">
            <span class="text-[#71717A]">{{ $gameStatus->updated_at->diffForHumans() }}</span>
            <a href="{{ route('games.show', $gameStatus->product->slug) }}" 
               class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white px-1.5 py-0.5 rounded text-xs transition-colors">
                View
            </a>
        </div>
        
        <!-- Ultra Compact Enhanced Status Buttons -->
        <div class="compact-status-buttons">
            <livewire:enhanced-game-status-buttons :product="$gameStatus->product" :key="'collection-'.$gameStatus->id" />
        </div>
    </div>
</div>
