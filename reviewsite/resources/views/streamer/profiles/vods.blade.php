@extends('layouts.app')

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Modal Styling for Twitch Embed */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }
    
    .modal.show {
        display: block !important;
    }
    
    .modal-dialog {
        position: relative;
        width: auto;
        margin: 1.75rem;
        pointer-events: none;
    }
    
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background: linear-gradient(135deg, #3f3f46 0%, #27272a 100%);
        border: 1px solid #52525b;
        border-radius: 0.75rem;
        outline: 0;
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 1.5rem;
        border-bottom: none;
        border-top-left-radius: calc(0.75rem - 1px);
        border-top-right-radius: calc(0.75rem - 1px);
    }
    
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 0;
    }
    
    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-top: 1px solid #52525b;
        border-bottom-right-radius: calc(0.75rem - 1px);
        border-bottom-left-radius: calc(0.75rem - 1px);
    }
    
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.8);
    }
    
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
    }
    
    @media (min-width: 992px) {
        .modal-xl {
            max-width: 1200px;
        }
    }
    
    .aspect-video {
        aspect-ratio: 16 / 9;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-zinc-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Profile Info -->
        <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-xl border border-zinc-700 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <!-- Profile Photo -->
                    <div class="w-16 h-16 flex-shrink-0 mr-4">
                        @if($streamerProfile->profile_photo_url)
                            <img src="{{ $streamerProfile->profile_photo_url }}" 
                                 alt="{{ $streamerProfile->channel_name }}" 
                                 class="w-full h-full rounded-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xl">{{ substr($streamerProfile->channel_name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">
                            {{ $streamerProfile->channel_name }}'s VODs
                        </h1>
                        <div class="flex items-center text-zinc-400">
                            <span class="capitalize px-2 py-0.5 bg-{{ $streamerProfile->platform === 'twitch' ? 'purple' : ($streamerProfile->platform === 'youtube' ? 'red' : 'green') }}-600 text-white rounded text-sm mr-3">
                                {{ $streamerProfile->platform }}
                            </span>
                            <span>{{ $streamerProfile->vods->count() }} VODs</span>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('streamer.profile.show', $streamerProfile) }}" 
                   class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors">
                    Back to Profile
                </a>
            </div>
        </div>
        
        <!-- VODs Grid -->
        @if($streamerProfile->vods->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                @foreach($streamerProfile->vods as $vod)
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
                    
                    <div class="bg-zinc-800/50 rounded-lg border border-zinc-600 overflow-hidden hover:border-blue-500 hover:bg-zinc-800/70 transition-all group">
                        <!-- Video Thumbnail -->
                        <div class="aspect-video relative bg-zinc-900">
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
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 002 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                @endif
                            </div>
                            
                            <!-- Duration overlay -->
                            @if($vod->formatted_duration)
                                <div class="absolute bottom-2 right-2 bg-black/80 text-white px-2 py-0.5 rounded text-xs">
                                    {{ $vod->formatted_duration }}
                                </div>
                            @endif
                            
                            <!-- Play button overlay -->
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-zinc-900 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Video Info -->
                        <div class="p-4">
                            <h4 class="text-white font-semibold text-sm line-clamp-2 group-hover:text-blue-400 transition-colors mb-2 min-h-[2.5rem]">
                                {{ $vod->title }}
                            </h4>
                            
                            @if($vod->description)
                                <p class="text-zinc-400 text-xs mb-3 line-clamp-2">
                                    {{ Str::limit($vod->description, 80) }}
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
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 002 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No VODs Available</h3>
                <p class="text-zinc-400">This streamer hasn't added any VODs yet.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/streamer-profile.js') }}"></script>
@endpush

<!-- Twitch Embed Modal -->
<div class="modal fade" id="twitchEmbedModal" tabindex="-1" role="dialog" aria-labelledby="twitchEmbedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-2xl">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold mb-0" id="twitchEmbedModalLabel">
                            Watch VOD
                        </h5>
                        <p class="text-purple-100 text-sm mb-0" id="vodTitleDisplay">
                            Loading...
                        </p>
                    </div>
                </div>
                <button type="button" class="text-white hover:text-purple-200 transition-colors p-2 hover:bg-white/10 rounded-lg" data-dismiss="modal" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="aspect-video bg-black">
                    <iframe id="twitchEmbed" 
                            src="" 
                            height="100%" 
                            width="100%" 
                            allowfullscreen="true" 
                            scrolling="no" 
                            frameborder="0">
                    </iframe>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer bg-zinc-800 border-t border-zinc-600 px-6 py-4">
                <div class="flex items-center justify-between w-full">
                    <div class="text-zinc-300 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Embedded Twitch player - Full screen available
                    </div>
                    <div class="flex gap-3">
                        <button type="button" 
                                class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors" 
                                data-dismiss="modal">
                            Close
                        </button>
                        <a id="openTwitchLink" 
                           href="#" 
                           target="_blank" 
                           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Open on Twitch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 