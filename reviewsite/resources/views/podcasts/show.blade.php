@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Podcast Header -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-start gap-8">
                <!-- Podcast Logo -->
                <div class="flex-shrink-0">
                    @if($podcast->logo_url)
                        <img src="{{ $podcast->logo_url }}" 
                             alt="{{ $podcast->name }}"
                             class="w-48 h-48 rounded-xl object-cover shadow-lg">
                    @else
                        <div class="w-48 h-48 bg-[#3F3F46] rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-20 h-20 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Podcast Info -->
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-4xl font-bold text-white font-['Share_Tech_Mono']">
                            {{ $podcast->name }}
                        </h1>
                        
                        @auth
                            @if($podcast->userCanPostAsThisPodcast(auth()->user()))
                                <div class="flex gap-2">
                                    <a href="{{ route('podcasts.dashboard') }}" 
                                       class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-[#DC2626] transition-colors font-['Inter'] text-sm">
                                        Manage
                                    </a>
                                    <form action="{{ route('podcasts.sync-rss', $podcast) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-[#27272A] text-white px-4 py-2 rounded-lg border border-[#3F3F46] hover:bg-[#374151] transition-colors font-['Inter'] text-sm">
                                            Sync RSS
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>

                    @if($podcast->description)
                        <p class="text-[#A1A1AA] text-lg mb-6 font-['Inter']">
                            {{ $podcast->description }}
                        </p>
                    @endif

                    <!-- Podcast Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div class="flex items-center text-[#A1A1AA] font-['Inter']">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ $podcast->owner->name }}</span>
                        </div>

                        @if($podcast->hosts && count($podcast->hosts) > 0)
                            <div class="flex items-center text-[#A1A1AA] font-['Inter']">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                                <span>{{ implode(', ', $podcast->hosts) }}</span>
                            </div>
                        @endif

                        <div class="flex items-center text-[#A1A1AA] font-['Inter']">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                            <span>{{ $totalEpisodes }} episodes</span>
                        </div>

                        <div class="flex items-center text-[#A1A1AA] font-['Inter']">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            <span>{{ $totalReviews }} reviews</span>
                        </div>

                        @if($podcast->website_url)
                            <div class="flex items-center text-[#A1A1AA] font-['Inter']">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <a href="{{ $podcast->website_url }}" 
                                   target="_blank" 
                                   class="hover:text-[#E53E3E] transition-colors">
                                    Visit Website
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Social Links -->
                    @if(is_array($podcast->links) && count($podcast->links) > 0)
                        <div class="flex flex-wrap gap-3">
                            @foreach($podcast->links as $link)
                                @if(is_array($link) && isset($link['url']) && isset($link['platform']))
                                    <a href="{{ $link['url'] }}" 
                                       target="_blank" 
                                       class="bg-[#3F3F46] text-white px-4 py-2 rounded-lg hover:bg-[#E53E3E] transition-colors font-['Inter'] text-sm">
                                        {{ $link['platform'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Team Members -->
        @if($podcast->activeTeamMembers->count() > 0)
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Team Members</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($podcast->activeTeamMembers as $member)
                        <div class="flex items-center space-x-3 p-4 bg-[#1A1A1B] rounded-lg border border-[#3F3F46]">
                            <div class="w-10 h-10 bg-[#E53E3E] rounded-full flex items-center justify-center">
                                <span class="text-white font-bold font-['Inter']">
                                    {{ substr($member->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-white font-medium font-['Inter']">{{ $member->user->name }}</p>
                                <p class="text-[#A1A1AA] text-sm font-['Inter']">{{ ucfirst($member->role) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Content Tabs -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Episodes -->
            <div class="lg:w-2/3">
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">All Episodes</h2>
                    
                    @if($episodes->count() > 0)
                        <div class="space-y-4">
                            @foreach($episodes as $episode)
                                <div class="bg-[#1A1A1B] rounded-lg p-6 border border-[#3F3F46] hover:border-[#E53E3E] transition-all duration-300">
                                    <div class="flex items-start gap-4">
                                        @if($episode->artwork_url)
                                            <img src="{{ $episode->artwork_url }}" 
                                                 alt="{{ $episode->title }}"
                                                 class="w-16 h-16 rounded-lg object-cover">
                                        @else
                                            <div class="w-16 h-16 bg-[#3F3F46] rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-white mb-2 font-['Share_Tech_Mono']">
                                                <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" 
                                                   class="hover:text-[#E53E3E] transition-colors">
                                                    {{ $episode->title }}
                                                </a>
                                            </h3>
                                            
                                            @if($episode->description)
                                                <p class="text-[#A1A1AA] text-sm mb-3 font-['Inter']">
                                                    {{ Str::limit($episode->description, 150) }}
                                                </p>
                                            @endif

                                            <div class="flex items-center text-[#A1A1AA] text-sm space-x-4 font-['Inter']">
                                                @if($episode->episode_number)
                                                    <span>Episode {{ $episode->episode_number }}</span>
                                                @endif
                                                <span>{{ $episode->published_at->format('M j, Y') }}</span>
                                                @if($episode->duration)
                                                    <span>{{ $episode->formatted_duration }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $episodes->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-[#3F3F46] rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                </svg>
                            </div>
                            <p class="text-[#A1A1AA] font-['Inter']">No episodes available yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews -->
            <div class="lg:w-1/3">
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Recent Reviews</h2>
                    
                    @if($recentAttachedReviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentAttachedReviews as $review)
                                <div class="bg-[#1A1A1B] rounded-lg p-4 border border-[#3F3F46]">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            @include('components.star-rating-simple', ['product' => $review->product, 'rating' => $review->rating])
                                        </div>
                                        <span class="text-[#A1A1AA] text-sm font-['Inter']">
                                            {{ $review->created_at->format('M j') }}
                                        </span>
                                    </div>
                                    
                                    <h4 class="text-white font-semibold mb-2 font-['Share_Tech_Mono']">
                                        <a href="{{ route('games.reviews.show', [$review->product, $review]) }}" 
                                           class="hover:text-[#E53E3E] transition-colors">
                                            {{ $review->title }}
                                        </a>
                                    </h4>
                                    
                                    <p class="text-[#A1A1AA] text-sm mb-3 font-['Inter']">
                                        {{ Str::limit($review->content, 100) }}
                                    </p>
                                    
                                    <div class="text-[#A1A1AA] text-xs font-['Inter']">
                                        Review of <span class="text-white">{{ $review->product->name }}</span>
                                        • Attached to 
                                        <a href="{{ route('podcasts.episodes.show', [$podcast, $review->episode_id]) }}" 
                                           class="text-[#E53E3E] hover:underline">
                                            {{ Str::limit($review->episode_title, 30) }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-[#3F3F46] rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                            <p class="text-[#A1A1AA] font-['Inter']">No reviews attached to episodes yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 