@extends('layouts.app')

@push('styles')
<style>
    /* VOD Card Hover Effects */
    .vod-card {
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #27272A 0%, #1A1A1B 100%);
        min-height: 400px;
    }
    
    .vod-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        border-color: #2563EB !important;
    }
    
    .vod-thumbnail {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .vod-card:hover .vod-thumbnail img {
        transform: scale(1.05);
    }
    
    /* Action Button Styles */
    .action-btn {
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
    }
    
    /* Modal Styling */
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
        background: linear-gradient(135deg, #27272A 0%, #1A1A1B 100%);
        border: 1px solid #3F3F46;
        border-radius: 0.5rem;
        outline: 0;
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 1.5rem;
        border-bottom: none;
        border-top-left-radius: calc(0.5rem - 1px);
        border-top-right-radius: calc(0.5rem - 1px);
    }
    
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 1rem;
    }
    
    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-top: 1px solid #3F3F46;
        border-bottom-right-radius: calc(0.5rem - 1px);
        border-bottom-left-radius: calc(0.5rem - 1px);
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
    
    .modal-backdrop.fade {
        opacity: 0;
    }
    
    .modal-backdrop.show {
        opacity: 1;
    }
    
    body.modal-open {
        overflow: hidden;
    }
    
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
    }
    
    @media (min-width: 992px) {
        .modal-lg {
            max-width: 800px;
        }
        
        .modal-xl {
            max-width: 1200px;
        }
    }
    
    /* Aspect ratio for video container */
    .aspect-video {
        aspect-ratio: 16 / 9;
    }
    
    /* Custom Form Styling */
    .form-control {
        background-color: #1A1A1B;
        border: 1px solid #3F3F46;
        color: #FFFFFF;
    }
    
    .form-control:focus {
        background-color: #1A1A1B;
        border-color: #2563EB;
        color: #FFFFFF;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }
    
    .form-control::placeholder {
        color: #A1A1AA;
    }
    
    /* Line clamp utility for text truncation */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Modal backdrop styling */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.8);
    }
    
    /* Smooth transitions for all interactive elements */
    * {
        transition-property: color, background-color, border-color, transform, box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#151515] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono'] mb-2">
                        <svg class="w-8 h-8 inline mr-3 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Manage VODs
                    </h1>
                    <p class="text-[#A1A1AA] font-['Inter']">Manage your video content and import from {{ ucfirst($streamerProfile->platform) }}</p>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('streamer.profile.show', $streamerProfile) }}" 
                       class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter'] action-btn">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Profile
                    </a>
                    <button type="button" class="px-4 py-2 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors font-['Inter'] action-btn" 
                            data-toggle="modal" data-target="#addVodModal">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Manual VOD
                    </button>
                    <form method="POST" action="{{ route('streamer.profile.import-vods', $streamerProfile) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-['Inter'] action-btn">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Import from {{ ucfirst($streamerProfile->platform) }}
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/30 text-green-100 rounded-lg p-4 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-['Inter']">{{ session('success') }}</span>
                <button type="button" class="ml-auto text-current hover:opacity-75" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/30 text-red-100 rounded-lg p-4 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-['Inter']">{{ session('error') }}</span>
                <button type="button" class="ml-auto text-current hover:opacity-75" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-500/20 border border-blue-500/30 text-blue-100 rounded-lg p-4 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-['Inter']">{{ session('info') }}</span>
                <button type="button" class="ml-auto text-current hover:opacity-75" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

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
                                                    title="Watch embedded Twitch {{ $embedType === 'clip' ? 'Clip' : 'VOD' }}">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                                </svg>
                                                {{ $embedType === 'clip' ? 'Play Clip' : 'Watch' }}
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
    </div>
</div>

<!-- Add VOD Modal -->
<div class="modal fade" id="addVodModal" tabindex="-1" role="dialog" aria-labelledby="addVodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-2xl">
            <form method="POST" action="{{ route('streamer.profile.add-vod', $streamerProfile) }}">
                @csrf
                <!-- Modal Header -->
                <div class="modal-header bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] text-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-lg font-bold font-['Share_Tech_Mono'] mb-0" id="addVodModalLabel">
                                Add Manual VOD
                            </h5>
                            <p class="text-blue-100 text-sm font-['Inter'] mb-0">
                                Add a custom video to your collection
                            </p>
                        </div>
                    </div>
                    <button type="button" class="text-white hover:text-blue-200 transition-colors p-2 hover:bg-white/10 rounded-lg" data-dismiss="modal" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="modal-body p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Title Field -->
                        <div class="form-group">
                            <label for="title" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Title <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors @error('title') border-red-500 @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required maxlength="500"
                                       placeholder="e.g., Epic Gaming Session - Part 1">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('title')
                                <div class="flex items-center mt-2 text-red-400 text-sm font-['Inter']">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- VOD URL Field -->
                        <div class="form-group">
                            <label for="vod_url" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                VOD URL <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="url" 
                                       class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors @error('vod_url') border-red-500 @enderror" 
                                       id="vod_url" name="vod_url" value="{{ old('vod_url') }}" required maxlength="500"
                                       placeholder="https://www.twitch.tv/videos/1234567890">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div id="url-status-icon">
                                        <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 p-3 bg-purple-500/10 border border-purple-500/20 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-purple-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                    </svg>
                                    <div class="text-purple-300 text-xs font-['Inter']">
                                        <div class="font-semibold mb-1">Twitch VODs & Clips support embedded viewing!</div>
                                        <div class="space-y-1">
                                            <div>VODs: <code class="bg-purple-900/30 px-1 rounded">https://www.twitch.tv/videos/1234567890</code></div>
                                            <div>Clips: <code class="bg-purple-900/30 px-1 rounded">https://www.twitch.tv/username/clip/ClipName-abc123</code></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('vod_url')
                                <div class="flex items-center mt-2 text-red-400 text-sm font-['Inter']">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Description Field -->
                        <div class="form-group">
                            <label for="description" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                Description
                                <span class="text-[#A1A1AA] text-sm ml-2 font-normal">(Optional)</span>
                            </label>
                            <textarea class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors resize-none @error('description') border-red-500 @enderror" 
                                      id="description" name="description" rows="4" maxlength="1000"
                                      placeholder="Describe what happens in this VOD, key moments, games played, etc.">{{ old('description') }}</textarea>
                            <div class="flex justify-between items-center mt-2">
                                @error('description')
                                    <div class="flex items-center text-red-400 text-sm font-['Inter']">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @else
                                    <div></div>
                                @enderror
                                <span class="text-[#A1A1AA] text-xs font-['Inter']">Max 1000 characters</span>
                            </div>
                        </div>

                        <!-- Thumbnail URL Field -->
                        <div class="form-group">
                            <label for="thumbnail_url" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Thumbnail URL
                                <span class="text-[#A1A1AA] text-sm ml-2 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <input type="url" 
                                       class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors @error('thumbnail_url') border-red-500 @enderror" 
                                       id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url') }}" maxlength="500"
                                       placeholder="https://example.com/thumbnail.jpg">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('thumbnail_url')
                                <div class="flex items-center mt-2 text-red-400 text-sm font-['Inter']">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="modal-footer bg-[#1A1A1B] border-t border-[#3F3F46] px-6 py-4">
                    <div class="flex items-center justify-between w-full">
                        <div class="text-[#A1A1AA] text-sm font-['Inter']">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Fields marked with * are required
                        </div>
                        <div class="flex gap-3">
                            <button type="button" 
                                    class="px-6 py-2.5 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-all font-['Inter'] font-medium border border-[#52525B] hover:border-[#6B7280]" 
                                    data-dismiss="modal">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] text-white rounded-lg hover:from-[#1D4ED8] hover:to-[#1E40AF] transition-all font-['Inter'] font-medium shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add VOD
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
                        <h5 class="text-lg font-bold font-['Share_Tech_Mono'] mb-0" id="twitchEmbedModalLabel">
                            Watch VOD
                        </h5>
                        <p class="text-purple-100 text-sm font-['Inter'] mb-0" id="vodTitleDisplay">
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
            <div class="modal-footer bg-[#1A1A1B] border-t border-[#3F3F46] px-6 py-4">
                <div class="flex items-center justify-between w-full">
                    <div class="text-[#A1A1AA] text-sm font-['Inter']">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Embedded Twitch player - Full screen available
                    </div>
                    <div class="flex gap-3">
                        <button type="button" 
                                class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter']" 
                                data-dismiss="modal">
                            Close
                        </button>
                        <a id="openTwitchLink" 
                           href="#" 
                           target="_blank" 
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-['Inter']">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteVodModal" tabindex="-1" role="dialog" aria-labelledby="deleteVodModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-2xl">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-to-r from-red-600 to-red-700 text-white">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold font-['Share_Tech_Mono'] mb-0" id="deleteVodModalLabel">
                            Delete VOD
                        </h5>
                        <p class="text-red-100 text-sm font-['Inter'] mb-0">
                            This action cannot be undone
                        </p>
                    </div>
                </div>
                <button type="button" class="text-white hover:text-red-200 transition-colors p-2 hover:bg-white/10 rounded-lg" data-dismiss="modal" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2 font-['Inter']">
                            Are you sure you want to delete this VOD?
                        </h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mb-4" id="deleteVodTitle">
                            This VOD will be permanently removed from your collection.
                        </p>
                        <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-red-300 text-sm font-['Inter']">
                                    This action cannot be undone. The VOD will be permanently deleted.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer bg-[#1A1A1B] border-t border-[#3F3F46] px-6 py-4">
                <div class="flex justify-end gap-3 w-full">
                    <button type="button" 
                            class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter']" 
                            data-dismiss="modal">
                        Cancel
                    </button>
                    <form id="deleteVodForm" method="POST" action="" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-['Inter']">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete VOD
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modal = document.getElementById('addVodModal');
    const modalButtons = document.querySelectorAll('[data-target="#addVodModal"]');
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    
    // Open modal
    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modal-backdrop';
            document.body.appendChild(backdrop);
            
            // Close on backdrop click
            backdrop.addEventListener('click', closeModal);
        });
    });
    
    // Close modal
    function closeModal() {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        const backdrop = document.getElementById('modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });
    
    // Auto-show modal if there are validation errors
    @if($errors->any())
        modalButtons[0].click();
    @endif
    
    // Auto-fill functionality for Twitch URLs
    const vodUrlInput = document.getElementById('vod_url');
    const titleInput = document.getElementById('title');
    const thumbnailInput = document.getElementById('thumbnail_url');
    
    if (vodUrlInput && titleInput) {
        vodUrlInput.addEventListener('blur', function() {
            const url = this.value.trim();
            if (url && !titleInput.value.trim()) {
                extractTwitchInfo(url);
            }
        });
        
        vodUrlInput.addEventListener('paste', function() {
            // Small delay to let the paste complete
            setTimeout(() => {
                const url = this.value.trim();
                if (url && !titleInput.value.trim()) {
                    extractTwitchInfo(url);
                }
            }, 100);
        });
    }
    
    function extractTwitchInfo(url) {
        // Show loading indicator in form
        showFormLoadingState();
        
        // Extract clip information from URL
        let clipId = null;
        let videoId = null;
        let channelName = null;
        
        // Handle different Twitch URL formats
        if (url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/([^\/]+)\/clip\/([^\/\?]+)/)) {
            const matches = url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/([^\/]+)\/clip\/([^\/\?]+)/);
            channelName = matches[1];
            clipId = matches[2];
        } else if (url.match(/clips\.twitch\.tv\/([^\/\?]+)/)) {
            const matches = url.match(/clips\.twitch\.tv\/([^\/\?]+)/);
            clipId = matches[1];
        } else if (url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/videos\/(\d+)/)) {
            const matches = url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/videos\/(\d+)/);
            videoId = matches[1];
        }
        
        let success = false;
        
        if (clipId) {
            // Extract title from clip slug (clips often have descriptive names)
            const clipTitle = extractClipTitle(clipId);
            if (clipTitle) {
                titleInput.value = clipTitle;
                titleInput.dispatchEvent(new Event('input')); // Trigger any validation
                success = true;
                
                // Show success message
                showAutoFillMessage(`Auto-filled title from clip: "${clipTitle}"`);
            }
            
            // Show helpful guidance for getting thumbnails manually
            if (!thumbnailInput.value.trim()) {
                showThumbnailHint(clipId);
            }
        } else if (videoId) {
            // For VODs, create a basic title
            const vodTitle = channelName ? `${channelName} - VOD ${videoId}` : `Twitch VOD ${videoId}`;
            titleInput.value = vodTitle;
            success = true;
            showAutoFillMessage(`Auto-filled title: "${vodTitle}"`);
        }
        
        // Hide loading indicator and show result
        hideFormLoadingState(success);
    }
    
    function showFormLoadingState() {
        const statusIcon = document.getElementById('url-status-icon');
        if (statusIcon) {
            statusIcon.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        }
    }
    
    function hideFormLoadingState(success) {
        const statusIcon = document.getElementById('url-status-icon');
        if (statusIcon) {
            if (success) {
                statusIcon.innerHTML = `
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                `;
                // Reset to normal after 3 seconds
                setTimeout(() => {
                    statusIcon.innerHTML = `
                        <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    `;
                }, 3000);
            } else {
                statusIcon.innerHTML = `
                    <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                `;
            }
        }
    }
    
    function showAutoFillMessage(message) {
        // Create a small success message near the title field
        const titleField = titleInput.parentElement;
        const existingMessage = titleField.querySelector('.auto-fill-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageEl = document.createElement('div');
        messageEl.className = 'auto-fill-message mt-2 p-2 bg-green-500/10 border border-green-500/20 rounded text-green-300 text-xs font-[\'Inter\'] flex items-center';
        messageEl.innerHTML = `
            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            ${message}
        `;
        
        titleField.appendChild(messageEl);
        
        // Remove message after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.parentNode.removeChild(messageEl);
            }
        }, 5000);
    }
    
    function showThumbnailHint(clipId) {
        // Show a helpful hint about getting the thumbnail
        const thumbnailField = thumbnailInput.parentElement.parentElement;
        const existingHint = thumbnailField.querySelector('.thumbnail-hint');
        if (existingHint) {
            existingHint.remove();
        }
        
        const hintEl = document.createElement('div');
        hintEl.className = 'thumbnail-hint mt-2 p-3 bg-purple-500/10 border border-purple-500/20 rounded text-purple-300 text-xs font-[\'Inter\']';
        hintEl.innerHTML = `
            <div class="flex items-start">
                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                </svg>
                <div>
                    <div class="font-semibold mb-1">💡 Tip: Get the clip thumbnail</div>
                    <div class="mb-2">Visit the clip on Twitch, right-click the video thumbnail, and copy the image URL.</div>
                    <div class="mt-2">
                        <button type="button" class="text-purple-400 hover:text-purple-300 underline text-xs" onclick="window.open('https://www.twitch.tv/clip/${clipId}', '_blank')">
                            Open clip on Twitch →
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-purple-200">
                        <strong>How to get thumbnail:</strong><br>
                        1. Click the link above to open the clip<br>
                        2. Right-click on the video thumbnail<br>
                        3. Select "Copy image address" or "Copy image URL"<br>
                        4. Paste it in the thumbnail field above
                    </div>
                </div>
            </div>
        `;
        
        thumbnailField.appendChild(hintEl);
        
        // Remove hint after 10 seconds
        setTimeout(() => {
            if (hintEl.parentNode) {
                hintEl.parentNode.removeChild(hintEl);
            }
        }, 10000);
    }
    
    function extractClipTitle(clipSlug) {
        // Clip slugs often contain descriptive information
        // Format is usually: DescriptiveWords-RandomString
        const parts = clipSlug.split('-');
        if (parts.length > 1) {
            // Take everything except the last part (which is usually random)
            const titlePart = parts.slice(0, -1).join('-');
            // Convert camelCase/PascalCase to readable format
            return titlePart
                .replace(/([A-Z])/g, ' $1') // Add space before capital letters
                .replace(/^./, str => str.toUpperCase()) // Capitalize first letter
                .trim();
        }
        return clipSlug;
    }
    
    function showLoadingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'fixed top-4 right-4 z-50 bg-blue-500/20 border border-blue-500/30 text-blue-100 rounded-lg p-3 flex items-center';
        indicator.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-['Inter'] text-sm">Extracting clip info...</span>
        `;
        document.body.appendChild(indicator);
        return indicator;
    }
    
    function hideLoadingIndicator(indicator) {
        if (indicator && indicator.parentNode) {
            indicator.parentNode.removeChild(indicator);
        }
    }
    
    // Twitch Embed Modal functionality
    const twitchModal = document.getElementById('twitchEmbedModal');
    const twitchEmbed = document.getElementById('twitchEmbed');
    const vodTitleDisplay = document.getElementById('vodTitleDisplay');
    const openTwitchLink = document.getElementById('openTwitchLink');
    const watchEmbedButtons = document.querySelectorAll('.watch-embed-btn');
    
    // Open Twitch embed modal
    watchEmbedButtons.forEach(button => {
        button.addEventListener('click', function() {
            const embedType = this.dataset.embedType;
            const vodId = this.dataset.vodId;
            const clipId = this.dataset.clipId;
            const channel = this.dataset.channel;
            const vodTitle = this.dataset.vodTitle;
            const originalUrl = this.dataset.originalUrl;
            
            let embedUrl = '';
            let twitchUrl = originalUrl;
            
            // Set up the embed URL based on type
            if (embedType === 'video' && vodId) {
                embedUrl = `https://player.twitch.tv/?video=${vodId}&parent=${window.location.hostname}&autoplay=false`;
                twitchUrl = `https://www.twitch.tv/videos/${vodId}`;
            } else if (embedType === 'clip' && clipId) {
                embedUrl = `https://clips.twitch.tv/embed?clip=${clipId}&parent=${window.location.hostname}&autoplay=false`;
                twitchUrl = originalUrl;
            }
            
            // Set up the embed
            twitchEmbed.src = embedUrl;
            
            // Update modal content
            vodTitleDisplay.textContent = vodTitle;
            openTwitchLink.href = twitchUrl;
            
            // Update modal title based on type
            const modalTitle = document.querySelector('#twitchEmbedModalLabel');
            modalTitle.textContent = embedType === 'clip' ? 'Watch Clip' : 'Watch VOD';
            
            // Show modal
            twitchModal.style.display = 'block';
            twitchModal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'twitch-modal-backdrop';
            document.body.appendChild(backdrop);
            
            // Close on backdrop click
            backdrop.addEventListener('click', closeTwitchModal);
        });
    });
    
    // Close Twitch modal
    function closeTwitchModal() {
        twitchModal.style.display = 'none';
        twitchModal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Clear the iframe to stop playback
        twitchEmbed.src = '';
        
        const backdrop = document.getElementById('twitch-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    // Close buttons for Twitch modal
    const twitchCloseButtons = twitchModal.querySelectorAll('[data-dismiss="modal"]');
    twitchCloseButtons.forEach(button => {
        button.addEventListener('click', closeTwitchModal);
    });
    
    // Close Twitch modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && twitchModal.classList.contains('show')) {
            closeTwitchModal();
        }
        if (e.key === 'Escape' && deleteModal.classList.contains('show')) {
            closeDeleteModal();
        }
    });
    
    // Delete VOD Modal functionality
    const deleteModal = document.getElementById('deleteVodModal');
    const deleteVodForm = document.getElementById('deleteVodForm');
    const deleteVodTitle = document.getElementById('deleteVodTitle');
    const deleteVodButtons = document.querySelectorAll('.delete-vod-btn');
    
    // Open delete modal
    deleteVodButtons.forEach(button => {
        button.addEventListener('click', function() {
            const vodTitle = this.dataset.vodTitle;
            const deleteUrl = this.dataset.deleteUrl;
            
            // Update modal content
            deleteVodTitle.textContent = `"${vodTitle}" will be permanently removed from your collection.`;
            deleteVodForm.action = deleteUrl;
            
            // Show modal
            deleteModal.style.display = 'block';
            deleteModal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'delete-modal-backdrop';
            document.body.appendChild(backdrop);
            
            // Close on backdrop click
            backdrop.addEventListener('click', closeDeleteModal);
        });
    });
    
    // Close delete modal
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        deleteModal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        const backdrop = document.getElementById('delete-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    // Close buttons for delete modal
    const deleteCloseButtons = deleteModal.querySelectorAll('[data-dismiss="modal"]');
    deleteCloseButtons.forEach(button => {
        button.addEventListener('click', closeDeleteModal);
    });
});
</script>
@endpush