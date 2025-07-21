@props(['streamerProfile'])

<div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-xl border border-zinc-700 overflow-hidden">
    <!-- Header Actions -->
    @if(auth()->check() && auth()->user()->id === $streamerProfile->user_id)
        <div class="bg-black/20 backdrop-blur-sm px-6 py-3 border-b border-zinc-600/50">
            <div class="flex justify-end">
                <a href="{{ route('streamer.profile.edit', $streamerProfile) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>
    @endif

    <div class="p-8">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            <!-- Profile Image -->
            <div class="flex-shrink-0">
                <div class="relative">
                    @if($streamerProfile->profile_photo_url)
                        <img src="{{ $streamerProfile->profile_photo_url }}" 
                             class="w-32 h-32 rounded-2xl border-2 border-zinc-600 shadow-lg object-cover" 
                             alt="{{ $streamerProfile->channel_name }}">
                    @else
                        <div class="w-32 h-32 bg-zinc-700 rounded-2xl flex items-center justify-center border-2 border-zinc-600 shadow-lg">
                            <svg class="w-16 h-16 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    

                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex-1 min-w-0">
                <!-- Name and Title -->
                <div class="mb-6">
                    <h1 class="text-4xl font-bold text-white mb-2 leading-tight">
                        {{ $streamerProfile->channel_name }}
                    </h1>
                    
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <!-- Platform Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium
                            {{ $streamerProfile->platform === 'twitch' ? 'bg-purple-600 text-white' : 
                               ($streamerProfile->platform === 'youtube' ? 'bg-red-600 text-white' : 'bg-green-600 text-white') }}">
                            @if($streamerProfile->platform === 'twitch')
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                </svg>
                            @elseif($streamerProfile->platform === 'youtube')
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            @endif
                            {{ ucfirst($streamerProfile->platform) }}
                        </span>
                        

                        
                        <!-- Member Since -->
                        <span class="inline-flex items-center px-3 py-1 bg-zinc-700/50 text-zinc-400 rounded-lg text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Member since {{ $streamerProfile->created_at->format('M Y') }}
                        </span>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex flex-wrap items-center gap-6 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">{{ number_format($streamerProfile->reviews->count()) }}</div>
                            <div class="text-zinc-400 text-sm">Reviews</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">{{ number_format($streamerProfile->followers->count()) }}</div>
                            <div class="text-zinc-400 text-sm">Followers</div>
                        </div>
                    </div>
                </div>

                <!-- Bio -->
                @if($streamerProfile->bio)
                    <div class="mb-6">
                        <p class="text-zinc-300 leading-relaxed">{{ $streamerProfile->bio }}</p>
                    </div>
                @endif
                
                <!-- Live Status -->
                <div class="mb-6">
                    @if($streamerProfile->isLive())
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-red-600/20 border border-red-500/30 rounded-lg">
                            <div class="flex items-center">
                                <div class="relative">
                                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                    <div class="absolute inset-0 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                                </div>
                                <span class="ml-2 text-red-400 font-semibold">LIVE NOW</span>
                            </div>

                        </div>
                    @else
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-zinc-700/30 border border-zinc-600 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-zinc-500 rounded-full"></div>
                                <span class="ml-2 text-zinc-400 font-semibold">OFFLINE</span>
                            </div>
                            @if($streamerProfile->live_status_checked_at)
                                <span class="text-zinc-500 text-sm">
                                    Last checked {{ $streamerProfile->live_status_checked_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    @if($streamerProfile->isLive())
                        <a href="{{ $streamerProfile->channel_url }}" target="_blank" 
                           class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Watch Live
                        </a>
                    @else
                        <a href="{{ $streamerProfile->channel_url }}" target="_blank" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Visit Channel
                        </a>
                    @endif
                    
                    <!-- Social Media Buttons -->
                    @foreach($streamerProfile->socialLinks as $link)
                        <a href="{{ $link->url }}" target="_blank" 
                           class="inline-flex items-center px-4 py-3 
                           {{ $link->platform === 'twitter' ? 'bg-blue-500 hover:bg-blue-600' : '' }}
                           {{ $link->platform === 'instagram' ? 'bg-pink-500 hover:bg-pink-600' : '' }}
                           {{ $link->platform === 'discord' ? 'bg-indigo-500 hover:bg-indigo-600' : '' }}
                           {{ $link->platform === 'youtube' ? 'bg-red-500 hover:bg-red-600' : '' }}
                           {{ !in_array($link->platform, ['twitter', 'instagram', 'discord', 'youtube']) ? 'bg-zinc-600 hover:bg-zinc-700' : '' }}
                           text-white rounded-lg transition-colors font-medium shadow-lg">
                            @if($link->platform === 'twitter')
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            @elseif($link->platform === 'instagram')
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            @elseif($link->platform === 'discord')
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0002 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z"/>
                                </svg>
                            @elseif($link->platform === 'youtube')
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            @endif
                            {{ $link->display_name ?: ucfirst($link->platform) }}
                        </a>
                    @endforeach
                    
                    @auth
                        @if(auth()->id() !== $streamerProfile->user_id)
                            @php
                                $isFollowing = auth()->user()->followedStreamers()->where('streamer_profiles.id', $streamerProfile->id)->exists();
                            @endphp
                            
                            @if($isFollowing)
                                <form action="{{ route('streamer.unfollow', $streamerProfile) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold shadow-lg"
                                            onclick="return confirm('Are you sure you want to unfollow {{ $streamerProfile->channel_name }}?')">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Unfollow
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('streamer.follow', $streamerProfile) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-semibold shadow-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        Follow
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div> 
