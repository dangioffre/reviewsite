@props(['streamerProfile'])

@if($streamerProfile->vods->count() > 0)
<div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-xl border border-zinc-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white">Recent VODs</h3>
        </div>
        @if($streamerProfile->vods->count() > 5)
            <button class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                View All ({{ $streamerProfile->vods->count() }})
            </button>
        @endif
    </div>
    
    <div class="space-y-4">
        @foreach($streamerProfile->vods->take(5) as $vod)
            @php
                // Extract video information for embed
                $embedType = null;
                $videoId = null;
                $clipId = null;
                $channel = null;
                $url = $vod->vod_url;
                
                // Detect video type and extract IDs
                if (preg_match('/(?:www\.|m\.|go\.)?twitch\.tv\/videos\/(\d+)/', $url, $matches)) {
                    $videoId = $matches[1];
                    $embedType = 'video';
                } elseif (preg_match('/(?:www\.|m\.|go\.)?twitch\.tv\/([^\/]+)\/clip\/([^\/\?]+)/', $url, $matches)) {
                    $channel = $matches[1];
                    $clipId = $matches[2];
                    $embedType = 'clip';
                } elseif (preg_match('/clips\.twitch\.tv\/([^\/\?]+)/', $url, $matches)) {
                    $clipId = $matches[1];
                    $embedType = 'clip';
                }
            @endphp
            
            <div class="bg-zinc-800/50 rounded-lg border border-zinc-600 overflow-hidden hover:border-zinc-500 transition-colors group">
                <div class="flex">
                    <!-- Video Thumbnail -->
                    <div class="w-48 h-28 flex-shrink-0 relative bg-zinc-900">
                        @if($vod->thumbnail_url)
                            <img src="{{ $vod->thumbnail_url }}" 
                                 class="w-full h-full object-cover" 
                                 alt="{{ $vod->title }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        
                        <!-- Fallback thumbnail -->
                        <div class="w-full h-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white {{ $vod->thumbnail_url ? 'hidden' : 'flex' }}" 
                             style="{{ $vod->thumbnail_url ? 'display: none;' : '' }}">
                            @if($embedType === 'clip')
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>
                        
                        <!-- Duration overlay -->
                        @if($vod->formatted_duration)
                            <div class="absolute bottom-1 right-1 bg-black/80 text-white px-2 py-0.5 rounded text-xs">
                                {{ $vod->formatted_duration }}
                            </div>
                        @endif
                        
                        <!-- Play button overlay -->
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-zinc-900 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Video Info -->
                    <div class="flex-1 p-4 min-w-0">
                        <h4 class="text-white font-semibold mb-2 line-clamp-2 group-hover:text-blue-400 transition-colors">
                            {{ Str::limit($vod->title, 80) }}
                        </h4>
                        
                        @if($vod->description)
                            <p class="text-zinc-400 text-sm mb-2 line-clamp-2">
                                {{ Str::limit($vod->description, 120) }}
                            </p>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <div class="text-zinc-500 text-xs">
                                @if($vod->published_at)
                                    {{ $vod->published_at->diffForHumans() }}
                                @endif
                            </div>
                            
                            @if($embedType)
                                <button type="button" 
                                        class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs font-medium transition-colors watch-embed-btn"
                                        data-embed-type="{{ $embedType }}"
                                        data-vod-id="{{ $videoId }}" 
                                        data-clip-id="{{ $clipId }}"
                                        data-channel="{{ $channel }}"
                                        data-vod-title="{{ $vod->title }}"
                                        data-original-url="{{ $vod->vod_url }}">
                                    Watch
                                </button>
                            @else
                                <a href="{{ $vod->vod_url }}" target="_blank" 
                                   class="px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600 text-white rounded text-xs font-medium transition-colors">
                                    Open
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    @if($streamerProfile->vods->count() > 5)
        <div class="text-center mt-6">
            <button class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors font-medium">
                Load More VODs
            </button>
        </div>
    @endif
</div>
@endif

@once
@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endonce 