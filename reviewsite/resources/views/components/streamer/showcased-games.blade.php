@props(['streamerProfile'])

@php
    $showcasedGames = $streamerProfile->showcasedGames()
        ->with(['gameUserStatus.product.genre', 'gameUserStatus.product.platform'])
        ->get();
@endphp

@if($showcasedGames->isNotEmpty())
    <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-2xl border border-zinc-700 p-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Currently Playing</h2>
                    <p class="text-zinc-400 font-['Inter']">Games {{ $streamerProfile->channel_name }} is showcasing</p>
                </div>
            </div>
            <div class="text-sm text-zinc-400 font-['Inter']">
                {{ $showcasedGames->count() }} {{ $showcasedGames->count() === 1 ? 'game' : 'games' }}
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @foreach($showcasedGames as $showcasedGame)
                @php
                    $product = $showcasedGame->gameUserStatus->product;
                    $status = $showcasedGame->gameUserStatus;
                @endphp
                <div class="bg-gradient-to-br from-zinc-700 to-zinc-800 rounded-lg border border-zinc-600 overflow-hidden hover:border-[#2563EB] transition-all duration-300 shadow-md hover:shadow-lg group">
                    <!-- Compact Game Image -->
                    <div class="aspect-square relative">
                        @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#3B82F6] to-[#1D4ED8] flex items-center justify-center">
                                <svg class="w-6 h-6 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Compact overlay -->
                        <a href="{{ route('games.show', $product->slug) }}" 
                           class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                            <div class="bg-white/20 backdrop-blur-sm rounded-full p-2">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                        </a>
                    </div>

                    <!-- Compact Game Info -->
                    <div class="p-3">
                        <h3 class="font-bold text-white text-sm mb-2 font-['Inter'] line-clamp-2 leading-tight">{{ $product->name }}</h3>
                        
                        <!-- Compact Tags -->
                        <div class="flex items-center gap-1 mb-2 flex-wrap">
                            @if($product->genre)
                                <span class="text-xs bg-[#3B82F6]/20 text-[#3B82F6] px-1.5 py-0.5 rounded font-medium">
                                    {{ $product->genre->name }}
                                </span>
                            @endif
                            @if($status->completion_status)
                                <span class="text-xs bg-green-500/20 text-green-400 px-1.5 py-0.5 rounded font-medium">
                                    {{ ucwords(str_replace('_', ' ', $status->completion_status)) }}
                                </span>
                            @endif
                        </div>

                        <!-- Compact Showcase Note -->
                        @if($showcasedGame->showcase_note)
                            <p class="text-zinc-300 text-xs font-['Inter'] italic mb-2 line-clamp-2">
                                "{{ $showcasedGame->showcase_note }}"
                            </p>
                        @endif

                        <!-- Clean One-Line Stats -->
                        @if($status->hours_played || $status->rating || $status->completion_percentage)
                            <div class="flex items-center gap-2 text-xs flex-wrap">
                                @if($status->hours_played)
                                    <span class="flex items-center gap-1 text-blue-400">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-semibold">{{ $status->hours_played }}h</span>
                                    </span>
                                @endif
                                
                                @if($status->rating)
                                    @if($status->hours_played)<span class="text-zinc-500">•</span>@endif
                                    <span class="flex items-center gap-1 text-yellow-400">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="font-semibold">{{ $status->rating }}</span>
                                    </span>
                                @endif
                                
                                @if($status->completion_percentage)
                                    @if($status->hours_played || $status->rating)<span class="text-zinc-500">•</span>@endif
                                    <span class="flex items-center gap-1 text-green-400">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-semibold">{{ $status->completion_percentage }}%</span>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif 