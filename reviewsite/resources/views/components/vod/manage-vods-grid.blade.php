<!-- VODs List -->
<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono'] flex items-center">
            <svg class="w-6 h-6 mr-3 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Your VODs
        </h2>
        <span class="px-3 py-1 bg-[#2563EB] text-white rounded-full text-sm font-bold font-['Inter']">
            {{ $streamerProfile->vods->count() }} Total
        </span>
    </div>
    
    @if($streamerProfile->vods->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($streamerProfile->vods as $vod)
                @php
                    // Extract Twitch video ID or clip ID from URL for embed
                    $twitchVideoId = null;
                    $twitchClipId = null;
                    $twitchChannel = null;
                    $embedType = null;
                    $url = $vod->vod_url;
                    
                    // Handle different Twitch VOD URL formats
                    if (preg_match('/(?:www\.|m\.|go\.)?twitch\.tv\/videos\/(\d+)/', $url, $matches)) {
                        $twitchVideoId = $matches[1];
                        $embedType = 'video';
                    }
                    // Handle Twitch clip URLs
                    elseif (preg_match('/(?:www\.|m\.|go\.)?twitch\.tv\/([^\/]+)\/clip\/([^\/\?]+)/', $url, $matches)) {
                        $twitchChannel = $matches[1];
                        $twitchClipId = $matches[2];
                        $embedType = 'clip';
                    }
                    // Alternative clip URL format
                    elseif (preg_match('/clips\.twitch\.tv\/([^\/\?]+)/', $url, $matches)) {
                        $twitchClipId = $matches[1];
                        $embedType = 'clip';
                    }
                    
                    // Kick URL parsing
                    $kickVideoId = null;
                    $kickClipId = null;
                    $kickUsername = null;
                    $kickEmbedType = null;
                    
                    // Handle Kick clip URLs
                    if (preg_match('/kick\.com\/([^\/]+)\/clips\/([^\/\?]+)/', $url, $matches)) {
                        $kickUsername = $matches[1];
                        $kickClipId = $matches[2];
                        $kickEmbedType = 'clip';
                    }
                    // Handle Kick video URLs
                    elseif (preg_match('/kick\.com\/([^\/]+)\/videos\/(\d+)/', $url, $matches)) {
                        $kickUsername = $matches[1];
                        $kickVideoId = $matches[2];
                        $kickEmbedType = 'video';
                    }
                @endphp
                
                <div class="vod-card rounded-xl border border-[#3F3F46] flex flex-col h-full">
                    <!-- Thumbnail -->
                    <div class="vod-thumbnail aspect-video relative overflow-hidden rounded-t-xl">
                        @if($vod->thumbnail_url)
                            <img src="{{ $vod->thumbnail_url }}" 
                                 class="w-full h-full object-cover transition-transform duration-300" 
                                 alt="{{ $vod->title }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        
                        <!-- Default thumbnail (shown when no thumbnail or image fails to load) -->
                        <div class="w-full h-full bg-gradient-to-br from-[#2563EB] to-[#1D4ED8] flex flex-col items-center justify-center text-white {{ $vod->thumbnail_url ? 'hidden' : 'flex' }}" 
                             style="{{ $vod->thumbnail_url ? 'display: none;' : '' }}">
                            <div class="text-center">
                                @if($embedType === 'clip')
                                    <!-- Clip icon -->
                                    <svg class="w-16 h-16 mb-3 mx-auto opacity-90" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                    </svg>
                                    <div class="text-sm font-bold font-['Share_Tech_Mono'] mb-1">TWITCH CLIP</div>
                                @elseif($embedType === 'video')
                                    <!-- VOD icon -->
                                    <svg class="w-16 h-16 mb-3 mx-auto opacity-90" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                    </svg>
                                    <div class="text-sm font-bold font-['Share_Tech_Mono'] mb-1">TWITCH VOD</div>
                                @else
                                    <!-- Generic video icon -->
                                    <svg class="w-16 h-16 mb-3 mx-auto opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    <div class="text-sm font-bold font-['Share_Tech_Mono'] mb-1">VIDEO</div>
                                @endif
                                <div class="text-xs opacity-75 font-['Inter'] px-2 text-center">
                                    {{ Str::limit($vod->title, 30) }}
                                </div>
                            </div>
                            
                            <!-- Decorative elements -->
                            <div class="absolute top-2 left-2 w-8 h-8 border-2 border-white/20 rounded-full"></div>
                            <div class="absolute bottom-2 right-2 w-6 h-6 border-2 border-white/20 rounded-full"></div>
                            <div class="absolute top-1/2 right-2 w-4 h-4 border border-white/20 rounded-full"></div>
                        </div>
                        
                        <!-- Duration Badge -->
                        @if($vod->formatted_duration)
                            <div class="absolute bottom-2 right-2 bg-black/80 text-white px-2 py-1 rounded text-xs font-['Inter']">
                                {{ $vod->formatted_duration }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="text-white font-bold text-sm mb-2 font-['Inter'] line-clamp-2">
                            {{ $vod->title }}
                        </h3>
                        
                        @if($vod->description)
                            <p class="text-[#A1A1AA] text-xs mb-3 font-['Inter'] line-clamp-2">
                                {{ Str::limit($vod->description, 80) }}
                            </p>
                        @endif
                        
                        <!-- Status Badges -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($vod->is_manual)
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-['Inter']">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Manual
                                </span>
                            @else
                                <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-['Inter']">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                    </svg>
                                    Imported
                                </span>
                            @endif
                        </div>
                        
                        @if($vod->published_at)
                            <div class="text-[#A1A1AA] text-xs mb-4 font-['Inter']">
                                Published: {{ $vod->published_at->format('M j, Y') }}
                            </div>
                        @endif
                        
                        <!-- Actions -->
                        <div class="mt-auto space-y-2">
                            <div class="flex gap-2">
                                @if($embedType)
                                    <button type="button" 
                                            class="flex-1 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-center text-xs font-['Inter'] watch-embed-btn"
                                            data-embed-type="{{ $embedType }}"
                                            data-vod-id="{{ $twitchVideoId }}" 
                                            data-clip-id="{{ $twitchClipId }}"
                                            data-channel="{{ $twitchChannel }}"
                                            data-vod-title="{{ $vod->title }}"
                                            data-original-url="{{ $vod->vod_url }}"
                                            data-platform="twitch"
                                            title="Watch embedded Twitch {{ $embedType === 'clip' ? 'Clip' : 'VOD' }}">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                        </svg>
                                        {{ $embedType === 'clip' ? 'Play Clip' : 'Watch' }}
                                    </button>
                                @elseif($kickEmbedType)
                                    <button type="button" 
                                            class="flex-1 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center text-xs font-['Inter'] watch-embed-btn"
                                            data-embed-type="{{ $kickEmbedType }}"
                                            data-vod-id="{{ $kickVideoId }}" 
                                            data-clip-id="{{ $kickClipId }}"
                                            data-username="{{ $kickUsername }}"
                                            data-vod-title="{{ $vod->title }}"
                                            data-original-url="{{ $vod->vod_url }}"
                                            data-platform="kick"
                                            title="Watch Kick {{ $kickEmbedType === 'clip' ? 'Clip' : 'VOD' }} (opens in new tab)">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        {{ $kickEmbedType === 'clip' ? 'Play Clip' : 'Watch' }}
                                    </button>
                                @else
                                    <!-- Debug: Show why embed isn't available -->
                                    <div class="flex-1 px-3 py-2 bg-gray-600 text-gray-300 rounded-lg text-center text-xs font-['Inter'] opacity-50" 
                                         title="Embed not available - URL: {{ $vod->vod_url }}">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-2.636-2.636M6 6l2.636 2.636"></path>
                                        </svg>
                                        No Embed
                                    </div>
                                @endif
                                
                                <a href="{{ $vod->vod_url }}" target="_blank" 
                                   class="flex-1 px-3 py-2 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors text-center text-xs font-['Inter']">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    External
                                </a>
                            </div>
                            
                            <button type="button" 
                                    class="w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs font-['Inter'] delete-vod-btn"
                                    data-vod-title="{{ $vod->title }}"
                                    data-delete-url="{{ route('streamer.profile.delete-vod', [$streamerProfile, $vod]) }}">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-[#A1A1AA] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">No VODs Found</h3>
            <p class="text-[#A1A1AA] font-['Inter'] mb-6">Add your first VOD manually or import from {{ ucfirst($streamerProfile->platform) }}.</p>
            <div class="flex justify-center gap-3">
                <button type="button" class="px-6 py-3 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors font-['Inter']" 
                        data-toggle="modal" data-target="#addVodModal">
                    Add Manual VOD
                </button>
                <form method="POST" action="{{ route('streamer.profile.import-vods', $streamerProfile) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-['Inter']">
                        Import from {{ ucfirst($streamerProfile->platform) }}
                    </button>
                </form>
            </div>
        </div>
    @endif
</div> 
