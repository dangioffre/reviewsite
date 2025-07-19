@extends('layouts.app')

@push('styles')
<style>
    /* Enhanced hover effects for schedule cards */
    .schedule-card {
        transition: all 0.3s ease;
    }
    
    .schedule-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
    }
    
    /* Review card hover effects */
    .review-card {
        transition: all 0.3s ease;
    }
    
    .review-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    
    /* Smooth animations for timezone converter */
    .timezone-converter {
        transition: all 0.2s ease;
    }
    
    .timezone-converter:hover {
        background-color: rgba(26, 26, 27, 0.8);
    }
    
    /* Custom scrollbar for better aesthetics */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #27272A;
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #3F3F46;
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #52525B;
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
        padding: 0;
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
        .modal-xl {
            max-width: 1200px;
        }
    }
    
    /* Aspect ratio for video container */
    .aspect-video {
        aspect-ratio: 16 / 9;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#151515] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Header -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
                    <!-- Header Actions -->
                    @if(auth()->check() && auth()->user()->id === $streamerProfile->user_id)
                        <div class="bg-[#1A1A1B] px-6 py-4 border-b border-[#3F3F46]">
                            <div class="flex gap-3">
                                <a href="{{ route('streamer.profile.edit', $streamerProfile) }}" 
                                   class="px-4 py-2 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors font-['Inter']">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Profile
                                </a>

                            </div>
                        </div>
                    @endif

                    <div class="p-8">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Profile Image -->
                            <div class="flex-shrink-0">
                                @if($streamerProfile->profile_photo_url)
                                    <img src="{{ $streamerProfile->profile_photo_url }}" 
                                         class="w-32 h-32 rounded-full border-4 border-[#3F3F46]" 
                                         alt="{{ $streamerProfile->channel_name }}">
                                @else
                                    <div class="w-32 h-32 bg-[#3F3F46] rounded-full flex items-center justify-center border-4 border-[#52525B]">
                                        <svg class="w-16 h-16 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Profile Info -->
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                    <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono']">
                                        {{ $streamerProfile->channel_name }}
                                    </h1>
                                    
                                    <!-- Platform Badge -->
                                    <span class="px-3 py-1 rounded-full text-sm font-bold font-['Inter'] 
                                        {{ $streamerProfile->platform === 'twitch' ? 'bg-[#9146FF] text-white' : 
                                           ($streamerProfile->platform === 'youtube' ? 'bg-[#FF0000] text-white' : 'bg-[#53FC18] text-black') }}">
                                        {{ ucfirst($streamerProfile->platform) }}
                                    </span>
                                    
                                    <!-- All OAuth-connected streamers are verified -->
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-bold font-['Inter']">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Verified
                                    </span>
                                </div>

                                @if($streamerProfile->bio)
                                    <p class="text-[#A1A1AA] text-lg mb-6 font-['Inter']">{{ $streamerProfile->bio }}</p>
                                @endif
                                
                                <!-- Live Status & Actions -->
                                <div class="space-y-4">
                                    @if($streamerProfile->isLive())
                                        <div class="flex items-center gap-3">
                                            <span class="px-4 py-2 bg-red-500 text-white rounded-lg font-bold font-['Inter'] animate-pulse">
                                                <div class="w-2 h-2 bg-white rounded-full inline-block mr-2 animate-ping"></div>
                                                LIVE NOW
                                            </span>
                                            @if($streamerProfile->manual_live_override !== null)
                                                <span class="text-[#A1A1AA] text-sm font-['Inter']">(Manual Override)</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-3">
                                            <a href="{{ $streamerProfile->channel_url }}" target="_blank" 
                                               class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-bold font-['Inter']">
                                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Watch Live
                                            </a>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3">
                                            <span class="px-4 py-2 bg-[#3F3F46] text-[#A1A1AA] rounded-lg font-bold font-['Inter']">
                                                <div class="w-2 h-2 bg-[#A1A1AA] rounded-full inline-block mr-2"></div>
                                                OFFLINE
                                            </span>
                                            @if($streamerProfile->live_status_checked_at)
                                                <span class="text-[#A1A1AA] text-sm font-['Inter']">
                                                    Last checked {{ $streamerProfile->live_status_checked_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-3">
                                            <a href="{{ $streamerProfile->channel_url }}" target="_blank" 
                                               class="px-6 py-3 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors font-bold font-['Inter']">
                                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Visit Channel
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @auth
                                        @if(auth()->id() !== $streamerProfile->user_id)
                                            <button class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all font-bold font-['Inter']" 
                                                    id="followBtn" data-profile-id="{{ $streamerProfile->id }}">
                                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                                Follow
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Streaming Schedule -->
                @if($streamerProfile->schedules->count() > 0)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                        <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono'] flex items-center">
                            <svg class="w-6 h-6 mr-3 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Streaming Schedule
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($streamerProfile->schedules->where('is_active', true) as $schedule)
                                <div class="bg-[#1A1A1B] rounded-lg border border-[#3F3F46] p-4 hover:border-[#2563EB] schedule-card">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-[#2563EB] font-bold text-lg font-['Inter']">
                                            {{ \Carbon\Carbon::create()->dayOfWeek($schedule->day_of_week)->format('D') }}
                                        </span>
                                        <div class="text-right">
                                            <div class="text-white font-bold font-['Inter'] text-sm">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                                            </div>
                                            <div class="text-[#A1A1AA] text-xs font-['Inter']">
                                                {{ $schedule->timezone }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center text-[#A1A1AA] text-sm mb-2">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="schedule-time" 
                                              data-start="{{ $schedule->start_time }}" 
                                              data-end="{{ $schedule->end_time }}" 
                                              data-timezone="{{ $schedule->timezone }}">
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }} end
                                        </span>
                                    </div>
                                    
                                    @if($schedule->notes)
                                        <div class="text-[#A1A1AA] text-xs font-['Inter'] mt-2 p-2 bg-[#27272A] rounded">
                                            {{ Str::limit($schedule->notes, 50) }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Timezone Converter -->
                        <div class="mt-6 p-4 bg-[#1A1A1B] rounded-lg border border-[#3F3F46] timezone-converter">
                            <div class="flex items-center justify-between">
                                <span class="text-[#A1A1AA] text-sm font-['Inter']">
                                    Times shown in streamer's timezone. 
                                    <span class="text-[#2563EB] cursor-pointer hover:underline" onclick="convertToLocalTime()">
                                        Convert to your timezone
                                    </span>
                                </span>
                                <div id="user-timezone" class="text-xs text-[#A1A1AA] font-['Inter']"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Recent Reviews -->
                @if($streamerProfile->reviews->count() > 0)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono'] flex items-center">
                                <svg class="w-6 h-6 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                Recent Reviews
                            </h2>
                            @if($streamerProfile->reviews->count() >= 6)
                                <a href="#" class="text-[#2563EB] hover:text-blue-400 text-sm font-['Inter'] transition-colors">
                                    View All ({{ $streamerProfile->reviews->count() }})
                                </a>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($streamerProfile->reviews->take(6) as $review)
                                <div class="bg-[#1A1A1B] rounded-lg border border-[#3F3F46] p-4 hover:border-[#52525B] review-card group">
                                    <div class="space-y-3">
                                        <!-- Product & Rating -->
                                        <div>
                                            <h3 class="text-white font-bold text-sm mb-1 font-['Inter'] group-hover:text-[#2563EB] transition-colors">
                                                <a href="{{ route($review->product->type === 'game' ? 'games.show' : 'tech.show', $review->product) }}">
                                                    {{ Str::limit($review->product->name, 25) }}
                                                </a>
                                            </h3>
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i <= ($review->rating / 2) ? 'text-yellow-400' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-yellow-400 font-bold text-sm font-['Inter']">{{ $review->rating }}/10</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Review Title -->
                                        <h4 class="text-[#2563EB] font-bold text-sm font-['Inter'] leading-tight">
                                            {{ Str::limit($review->title, 40) }}
                                        </h4>
                                        
                                        <!-- Review Content -->
                                        <p class="text-[#A1A1AA] text-xs font-['Inter'] leading-relaxed">
                                            {{ Str::limit($review->content, 80) }}
                                        </p>
                                        
                                        <!-- Meta Info -->
                                        <div class="flex items-center justify-between text-xs text-[#A1A1AA] font-['Inter'] pt-2 border-t border-[#3F3F46]">
                                            <span>{{ $review->created_at->diffForHumans() }}</span>
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                Review
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($streamerProfile->reviews->count() > 6)
                            <div class="text-center mt-6">
                                <button class="px-6 py-3 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter'] text-sm">
                                    Load More Reviews
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Profile Stats -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                    <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono'] flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Profile Stats
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-[#1A1A1B] rounded-lg border border-[#3F3F46]">
                            <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $streamerProfile->reviews->count() }}</div>
                            <div class="text-[#A1A1AA] text-sm font-['Inter']">Reviews</div>
                        </div>
                        <div class="text-center p-4 bg-[#1A1A1B] rounded-lg border border-[#3F3F46]">
                            <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $streamerProfile->followers->count() }}</div>
                            <div class="text-[#A1A1AA] text-sm font-['Inter']">Followers</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <div class="text-[#A1A1AA] text-sm font-['Inter']">
                            Member since {{ $streamerProfile->created_at->format('M Y') }}
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                @if($streamerProfile->socialLinks->count() > 0)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                        <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono'] flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Social Links
                        </h3>
                        
                        <div class="space-y-3">
                            @foreach($streamerProfile->socialLinks as $link)
                                <a href="{{ $link->url }}" target="_blank" 
                                   class="flex items-center p-3 bg-[#1A1A1B] rounded-lg border border-[#3F3F46] hover:border-[#52525B] transition-colors group">
                                    <div class="w-8 h-8 bg-[#3F3F46] rounded-full flex items-center justify-center mr-3 group-hover:bg-[#52525B] transition-colors">
                                        @if($link->platform === 'twitter')
                                            <svg class="w-4 h-4 text-[#1DA1F2]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                            </svg>
                                        @elseif($link->platform === 'instagram')
                                            <svg class="w-4 h-4 text-[#E4405F]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                            </svg>
                                        @elseif($link->platform === 'discord')
                                            <svg class="w-4 h-4 text-[#5865F2]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0002 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-white font-['Inter'] group-hover:text-[#2563EB] transition-colors">
                                        {{ $link->display_name ?: ucfirst($link->platform) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Recent VODs -->
                @if($streamerProfile->vods->count() > 0)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                        <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono'] flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Recent VODs
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($streamerProfile->vods->take(5) as $vod)
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
                                
                                <div class="bg-[#1A1A1B] rounded-lg border border-[#3F3F46] overflow-hidden hover:border-[#52525B] transition-colors">
                                    <div class="aspect-video relative">
                                        @if($vod->thumbnail_url)
                                            <img src="{{ $vod->thumbnail_url }}" 
                                                 class="w-full h-full object-cover" 
                                                 alt="{{ $vod->title }}"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @endif
                                        
                                        <!-- Default thumbnail for Recent VODs -->
                                        <div class="w-full h-full bg-gradient-to-br from-[#2563EB] to-[#1D4ED8] flex flex-col items-center justify-center text-white {{ $vod->thumbnail_url ? 'hidden' : 'flex' }}" 
                                             style="{{ $vod->thumbnail_url ? 'display: none;' : '' }}">
                                            <div class="text-center">
                                                @if($embedType === 'clip')
                                                    <!-- Clip icon -->
                                                    <svg class="w-12 h-12 mb-2 mx-auto opacity-90" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                                    </svg>
                                                    <div class="text-xs font-bold font-['Share_Tech_Mono'] mb-1">CLIP</div>
                                                @elseif($embedType === 'video')
                                                    <!-- VOD icon -->
                                                    <svg class="w-12 h-12 mb-2 mx-auto opacity-90" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                                    </svg>
                                                    <div class="text-xs font-bold font-['Share_Tech_Mono'] mb-1">VOD</div>
                                                @else
                                                    <!-- Generic video icon -->
                                                    <svg class="w-12 h-12 mb-2 mx-auto opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div class="text-xs font-bold font-['Share_Tech_Mono'] mb-1">VIDEO</div>
                                                @endif
                                                <div class="text-xs opacity-75 font-['Inter'] px-1 text-center">
                                                    {{ Str::limit($vod->title, 20) }}
                                                </div>
                                            </div>
                                            
                                            <!-- Decorative elements (smaller for sidebar) -->
                                            <div class="absolute top-1 left-1 w-4 h-4 border border-white/20 rounded-full"></div>
                                            <div class="absolute bottom-1 right-1 w-3 h-3 border border-white/20 rounded-full"></div>
                                        </div>
                                        
                                        @if($vod->formatted_duration)
                                            <div class="absolute bottom-2 right-2 bg-black/80 text-white px-2 py-1 rounded text-xs font-['Inter']">
                                                {{ $vod->formatted_duration }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h4 class="text-white font-bold mb-2 font-['Inter']">{{ Str::limit($vod->title, 50) }}</h4>
                                        @if($vod->description)
                                            <p class="text-[#A1A1AA] text-sm mb-3 font-['Inter']">{{ Str::limit($vod->description, 60) }}</p>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <div class="text-[#A1A1AA] text-xs font-['Inter']">
                                                @if($vod->published_at)
                                                    {{ $vod->published_at->diffForHumans() }}
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($embedType)
                                            <button type="button" 
                                                    class="w-full px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-center text-xs font-['Inter'] watch-embed-btn"
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
                                                Watch
                                            </button>
                                        @else
                                            <!-- For non-Twitch content, show a simple message or hide -->
                                            <div class="w-full px-3 py-2 bg-[#3F3F46] text-[#A1A1AA] rounded-lg text-center text-xs font-['Inter']">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                Video Content
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($streamerProfile->vods->count() >= 10)
                                <div class="text-center">
                                    <button class="px-6 py-3 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter']">
                                        View All VODs
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize timezone display
    initializeTimezoneDisplay();
    
    function initializeTimezoneDisplay() {
        const userTimezoneEl = document.getElementById('user-timezone');
        if (userTimezoneEl) {
            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            userTimezoneEl.textContent = `Your timezone: ${userTimezone}`;
        }
    }
    
    // Global function for timezone conversion
    window.convertToLocalTime = function() {
        const scheduleElements = document.querySelectorAll('.schedule-time');
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        
        scheduleElements.forEach(element => {
            const startTime = element.dataset.start;
            const endTime = element.dataset.end;
            const streamerTimezone = element.dataset.timezone;
            
            try {
                // Create date objects for today with the schedule times
                const today = new Date();
                const startDateTime = new Date(`${today.toDateString()} ${startTime}`);
                const endDateTime = new Date(`${today.toDateString()} ${endTime}`);
                
                // Format times in user's timezone
                const startLocal = startDateTime.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                    timeZone: userTimezone
                });
                
                const endLocal = endDateTime.toLocaleTimeString('en-US', {
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true,
                    timeZone: userTimezone
                });
                
                // Update the display
                const parentCard = element.closest('.bg-\\[\\#1A1A1B\\]');
                const startTimeEl = parentCard.querySelector('.text-white.font-bold');
                const timezoneEl = parentCard.querySelector('.text-\\[\\#A1A1AA\\].text-xs');
                
                if (startTimeEl && timezoneEl) {
                    startTimeEl.textContent = startLocal;
                    timezoneEl.textContent = userTimezone;
                    element.textContent = `${endLocal} end`;
                }
            } catch (error) {
                console.error('Error converting timezone:', error);
            }
        });
        
        // Update the converter text
        const converterText = document.querySelector('.text-\\[\\#2563EB\\].cursor-pointer');
        if (converterText) {
            converterText.textContent = 'Show original times';
            converterText.onclick = function() { location.reload(); };
        }
    };
    
    // Follow button functionality
    const followBtn = document.getElementById('followBtn');
    if (followBtn) {
        // Load initial follow status
        loadFollowStatus();
        
        followBtn.addEventListener('click', function() {
            const profileId = this.dataset.profileId;
            const isFollowing = this.classList.contains('bg-red-500');
            
            if (isFollowing) {
                unfollowStreamer(profileId);
            } else {
                followStreamer(profileId);
            }
        });
    }
    
    function loadFollowStatus() {
        const profileId = followBtn.dataset.profileId;
        
        fetch(`/streamer/follow/${profileId}/status`)
            .then(response => response.json())
            .then(data => {
                updateFollowButton(data.following, data.follower_count);
            })
            .catch(error => {
                console.error('Error loading follow status:', error);
            });
    }
    
    function followStreamer(profileId) {
        followBtn.disabled = true;
        
        fetch(`/streamer/follow/${profileId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showNotification(data.error, 'error');
            } else {
                updateFollowButton(true, data.follower_count);
                showNotification(data.message || 'Successfully followed!', 'success');
            }
        })
        .catch(error => {
            console.error('Error following streamer:', error);
            showNotification('An error occurred while following the streamer.', 'error');
        })
        .finally(() => {
            followBtn.disabled = false;
        });
    }
    
    function unfollowStreamer(profileId) {
        if (!confirm('Are you sure you want to unfollow this streamer?')) {
            return;
        }
        
        followBtn.disabled = true;
        
        fetch(`/streamer/follow/${profileId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showNotification(data.error, 'error');
            } else {
                updateFollowButton(false, data.follower_count);
                showNotification(data.message || 'Successfully unfollowed!', 'success');
            }
        })
        .catch(error => {
            console.error('Error unfollowing streamer:', error);
            showNotification('An error occurred while unfollowing the streamer.', 'error');
        })
        .finally(() => {
            followBtn.disabled = false;
        });
    }
    
    function updateFollowButton(isFollowing, followerCount) {
        if (isFollowing) {
            followBtn.className = 'px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all font-bold font-[\'Inter\']';
            followBtn.innerHTML = `
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Unfollow
            `;
            followBtn.title = `${followerCount} followers`;
        } else {
            followBtn.className = 'px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all font-bold font-[\'Inter\']';
            followBtn.innerHTML = `
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Follow
            `;
            followBtn.title = `${followerCount} followers`;
        }
    }
    
    function showNotification(message, type) {
        // Create a modern notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-2xl border max-w-sm transform transition-all duration-300 translate-x-full opacity-0 ${
            type === 'success' 
                ? 'bg-green-500/20 border-green-500/30 text-green-100' 
                : 'bg-red-500/20 border-red-500/30 text-red-100'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span class="font-['Inter']">${message}</span>
                <button type="button" class="ml-4 text-current hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
        }, 100);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
    
    // Twitch Embed Modal functionality (same as in manage-vods page)
    const twitchModal = document.getElementById('twitchEmbedModal');
    if (twitchModal) {
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
        });
    }
});
</script>
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

@endsection