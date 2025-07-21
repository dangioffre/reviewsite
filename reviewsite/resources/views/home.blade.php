<x-layouts.app>
    <x-slot name="title">Dan & Brian Reviews - Gaming Reviews, Podcasts & Streams</x-slot>
    
    <!-- Hero Section -->
    <section class="relative bg-[#121212] py-20 lg:py-28">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <!-- Hero Header -->
            <div class="text-center mb-16">
                <h1 class="text-5xl lg:text-7xl font-bold text-white mb-6 font-['Poppins']">
                    <span class="bg-gradient-to-r from-white via-[#DC2626] to-[#FFC107] bg-clip-text text-transparent">
                        Dan & Brian Reviews
                    </span>
                </h1>
                
                <p class="text-xl lg:text-2xl text-[#A0A0A0] mb-10 max-w-3xl mx-auto leading-relaxed">
                    Your trusted source for honest game reviews, insightful podcasts, and discovering the best content creators in gaming.
                </p>
                
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('games.index') }}" class="px-8 py-4 bg-[#DC2626] hover:bg-[#B91C1C] text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Explore Games
                    </a>
                    <a href="{{ route('streamer.profiles.index') }}" class="px-8 py-4 bg-transparent border-2 border-[#DC2626] text-[#DC2626] hover:bg-[#DC2626] hover:text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Discover Creators
                    </a>
                </div>
            </div>

            <!-- Featured Reviews Grid -->
            @if($heroReviews->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                <div class="lg:col-span-2">
                    @php $featuredReview = $heroReviews->first(); @endphp
                    <div class="bg-[#1E1E1E] backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden border border-[#292929] group hover:border-[#DC2626] transition-all duration-500 transform hover:-translate-y-2">
                        <div class="aspect-[16/9] bg-gradient-to-br from-[#DC2626]/20 via-[#FFC107]/20 to-[#03A9F4]/20 flex items-center justify-center relative overflow-hidden">
                            @if($featuredReview->product->thumbnail_url)
                                <img src="{{ $featuredReview->product->thumbnail_url }}" alt="{{ $featuredReview->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-[#DC2626]/30 to-[#FFC107]/30"></div>
                                <span class="text-6xl font-bold text-white/60">{{ substr($featuredReview->product->name, 0, 1) }}</span>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute top-6 left-6 flex items-center gap-3">
                                <span class="bg-[#DC2626] text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                    Staff Pick
                                </span>
                                <div class="flex items-center bg-black/60 backdrop-blur text-white px-4 py-2 rounded-full text-sm font-semibold">
                                    <svg class="w-4 h-4 mr-2 text-[#FFC107]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    {{ $featuredReview->rating }}/10
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-sm text-[#DC2626] font-medium">{{ $featuredReview->product->genre->name ?? 'Gaming' }}</span>
                                <span class="text-[#A0A0A0]">•</span>
                                <span class="text-sm text-[#A0A0A0]">{{ $featuredReview->created_at->format('M j, Y') }}</span>
                            </div>
                            <h2 class="text-2xl font-bold text-white mb-4 group-hover:text-[#DC2626] transition-colors duration-300">
                                {{ $featuredReview->title }}
                            </h2>
                            <p class="text-[#A0A0A0] mb-6 leading-relaxed">{{ Str::limit($featuredReview->content, 150) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-[#DC2626] to-[#FFC107] rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold">{{ substr($featuredReview->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">{{ $featuredReview->user->name }}</p>
                                        <p class="text-[#DC2626] text-sm">Staff Reviewer</p>
                                    </div>
                                </div>
                                <a href="{{ route($featuredReview->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$featuredReview->product, $featuredReview]) }}" 
                                   class="px-6 py-3 bg-[#DC2626] hover:bg-[#B91C1C] text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                                    Read Review
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Reviews -->
                <div class="space-y-6">
                    @foreach($heroReviews->skip(1)->take(3) as $review)
                    <div class="bg-[#1E1E1E] backdrop-blur-sm rounded-xl p-6 border border-[#292929] group hover:border-[#DC2626] transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center bg-black/40 backdrop-blur text-white px-3 py-1 rounded-full text-sm font-medium">
                                <svg class="w-3 h-3 mr-1.5 text-[#FFC107]" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                {{ $review->rating }}/10
                            </div>
                            <span class="text-xs text-[#A0A0A0]">{{ $review->created_at->format('M j') }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-3 group-hover:text-[#DC2626] transition-colors">
                            {{ $review->title }}
                        </h3>
                        <p class="text-[#A0A0A0] text-sm mb-4 line-clamp-2">{{ Str::limit($review->content, 100) }}</p>
                        <a href="{{ route($review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$review->product, $review]) }}" 
                           class="text-[#DC2626] hover:text-[#B91C1C] text-sm font-medium transition-colors inline-flex items-center gap-1">
                            Read More
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Recently Released Games -->
    @if($recentGames->count() > 0)
    <section class="bg-[#1E1E1E] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-3 font-['Poppins']">Recently Released Games</h2>
                    <p class="text-[#A0A0A0]">The newest games in our collection, ordered by release date</p>
                </div>
                <a href="{{ route('games.index') }}" class="inline-flex items-center gap-2 text-[#03A9F4] hover:text-[#0288D1] font-medium transition-colors">
                    View All Games
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($recentGames as $game)
                <div class="bg-[#121212] rounded-xl shadow-lg border border-[#292929] overflow-hidden group hover:shadow-xl hover:border-[#03A9F4] transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full">
                    <div class="aspect-[4/3] bg-gradient-to-br from-[#03A9F4]/20 to-[#FFC107]/20 flex items-center justify-center relative overflow-hidden">
                        @if($game->thumbnail_url)
                            <img src="{{ $game->thumbnail_url }}" alt="{{ $game->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <span class="text-3xl font-bold text-[#A0A0A0]">{{ substr($game->name, 0, 1) }}</span>
                        @endif
                        @if($game->is_featured)
                            <div class="absolute top-3 left-3">
                                <span class="bg-[#03A9F4] text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg">Featured</span>
                            </div>
                        @elseif($game->release_date && $game->release_date->gt(now()->subDays(7)))
                            <div class="absolute top-3 left-3">
                                <span class="bg-[#4CAF50] text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg">New</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="text-white font-semibold mb-2 line-clamp-2 text-sm group-hover:text-[#03A9F4] transition-colors flex-grow">{{ $game->name }}</h3>
                        <div class="flex items-center justify-between text-xs mb-4">
                            <span class="text-[#03A9F4] font-medium">{{ $game->genre->name ?? 'Gaming' }}</span>
                            <span class="text-[#A0A0A0]">{{ $game->release_date ? $game->release_date->format('M j') : 'TBA' }}</span>
                        </div>
                        <a href="{{ route('games.show', $game) }}" class="w-full bg-[#03A9F4] hover:bg-[#0288D1] text-white py-2 px-3 rounded-lg text-sm font-medium transition-all duration-300 text-center transform hover:scale-105 mt-auto">
                            View Game
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Streamers -->
    @if($recentStreamers->count() > 0)
    <section class="bg-[#121212] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-3 font-['Poppins']">Featured Creators</h2>
                    <p class="text-[#A0A0A0]">Meet the talented creators joining our community</p>
                </div>
                <a href="{{ route('streamer.profiles.index') }}" class="inline-flex items-center gap-2 text-[#FFC107] hover:text-[#FFB300] font-medium transition-colors">
                    Discover All Creators
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                @foreach($recentStreamers as $streamer)
                <div class="bg-[#1E1E1E] rounded-xl p-6 border border-[#292929] group hover:shadow-lg hover:border-[#FFC107] transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <div class="flex items-center mb-4">
                        <div class="w-14 h-14 flex-shrink-0 mr-4 relative">
                            @if($streamer->profile_photo_url)
                                <img src="{{ $streamer->profile_photo_url }}" alt="{{ $streamer->channel_name }}" class="w-full h-full rounded-full object-cover border-3 border-[#FFC107]">
                            @else
                                                                 <div class="w-full h-full bg-gradient-to-br from-[#FFC107] to-[#DC2626] rounded-full flex items-center justify-center border-3 border-[#FFC107]">
                                     <span class="text-white font-semibold">{{ substr($streamer->channel_name, 0, 1) }}</span>
                                 </div>
                            @endif
                            @if($streamer->isLive())
                                                                 <div class="absolute -top-1 -right-1 bg-[#DC2626] text-white text-xs px-2 py-1 rounded-full font-medium shadow-lg">
                                     <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse inline-block mr-1"></span>
                                     Live
                                 </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold text-base mb-1 group-hover:text-[#FFC107] transition-colors line-clamp-1">{{ $streamer->channel_name }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $streamer->platform === 'twitch' ? 'bg-purple-900/50 text-purple-300 border border-purple-700' : ($streamer->platform === 'youtube' ? 'bg-red-900/50 text-red-300 border border-red-700' : 'bg-green-900/50 text-green-300 border border-green-700') }}">
                                    {{ ucfirst($streamer->platform) }}
                                </span>
                                @if($streamer->is_verified)
                                    <svg class="w-4 h-4 text-[#03A9F4]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($streamer->bio)
                        <p class="text-[#A0A0A0] text-sm mb-4 line-clamp-2 flex-grow">{{ Str::limit($streamer->bio, 100) }}</p>
                    @endif
                    <div class="flex items-center justify-between text-sm text-[#A0A0A0] mb-4">
                        <span>{{ number_format($streamer->followers_count) }} followers</span>
                        <span>{{ $streamer->created_at->format('M j') }}</span>
                    </div>
                    <a href="{{ route('streamer.profile.show', $streamer) }}" 
                       class="w-full bg-[#FFC107] hover:bg-[#FFB300] text-black py-2 px-3 rounded-lg text-sm font-medium transition-all duration-300 text-center transform hover:scale-105 mt-auto">
                        View Profile
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Gaming Podcasts -->
    @if($recentPodcasts->count() > 0)
    <section class="bg-[#1E1E1E] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-3 font-['Poppins']">Gaming Podcasts</h2>
                    <p class="text-[#A0A0A0]">Dive deep into gaming discussions and insights</p>
                </div>
                <a href="{{ route('podcasts.index') }}" class="inline-flex items-center gap-2 text-[#4CAF50] hover:text-[#388E3C] font-medium transition-colors">
                    All Podcasts
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                @foreach($recentPodcasts as $podcast)
                <div class="bg-[#121212] rounded-xl p-6 border border-[#292929] group hover:shadow-lg hover:border-[#4CAF50] transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#4CAF50] to-[#03A9F4] rounded-lg flex items-center justify-center flex-shrink-0">
                            @if($podcast->cover_image)
                                <img src="{{ $podcast->cover_image }}" alt="{{ $podcast->name }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold text-base mb-1 group-hover:text-[#4CAF50] transition-colors line-clamp-2 leading-tight">{{ $podcast->name }}</h3>
                            <p class="text-[#A0A0A0] text-sm">by {{ $podcast->owner->name }}</p>
                        </div>
                    </div>
                    @if($podcast->description)
                        <p class="text-[#A0A0A0] text-sm mb-4 line-clamp-2 flex-grow">{{ Str::limit($podcast->description, 100) }}</p>
                    @endif
                    <div class="flex items-center justify-between text-sm text-[#A0A0A0] mb-4">
                        <span>{{ $podcast->episodes_count }} episodes</span>
                        <span>{{ $podcast->reviews_count }} reviews</span>
                    </div>
                    <a href="{{ route('podcasts.show', $podcast) }}" 
                       class="w-full bg-[#4CAF50] hover:bg-[#388E3C] text-white py-2 px-3 rounded-lg text-sm font-medium transition-all duration-300 text-center transform hover:scale-105 mt-auto">
                        Listen Now
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Community Lists -->
    @if($recentLists->count() > 0)
    <section class="bg-[#121212] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-3 font-['Poppins']">Community Lists</h2>
                    <p class="text-[#A0A0A0]">Discover curated collections created by our community</p>
                </div>
                <a href="{{ route('lists.index') }}" class="inline-flex items-center gap-2 text-[#FFC107] hover:text-[#FFB300] font-medium transition-colors">
                    Browse All Lists
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                @foreach($recentLists as $list)
                <div class="bg-[#1E1E1E] rounded-xl p-6 border border-[#292929] group hover:shadow-lg hover:border-[#FFC107] transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <div class="flex items-start gap-3 mb-4">
                                                 <div class="w-12 h-12 bg-gradient-to-br from-[#FFC107] to-[#DC2626] rounded-lg flex items-center justify-center flex-shrink-0">
                             <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                             </svg>
                         </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold text-base mb-1 group-hover:text-[#FFC107] transition-colors line-clamp-2 leading-tight">{{ $list->name }}</h3>
                            <p class="text-[#A0A0A0] text-sm">by {{ $list->user->name }}</p>
                        </div>
                    </div>
                    @if($list->description)
                        <p class="text-[#A0A0A0] text-sm mb-4 line-clamp-2 flex-grow">{{ Str::limit($list->description, 100) }}</p>
                    @endif
                    <div class="flex items-center justify-between text-sm text-[#A0A0A0] mb-4">
                        <span>{{ $list->items_count }} games</span>
                        <span>{{ $list->followers_count }} followers</span>
                    </div>
                    <a href="{{ route('lists.public', $list->slug) }}" 
                       class="w-full bg-[#FFC107] hover:bg-[#FFB300] text-black py-2 px-3 rounded-lg text-sm font-medium transition-all duration-300 text-center transform hover:scale-105 mt-auto">
                        View List
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Call to Action -->
    <section class="bg-[#1E1E1E] border-t border-[#292929] py-12">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4 font-['Poppins']">Join Our Gaming Community</h2>
            <p class="text-lg text-[#A0A0A0] mb-8 max-w-2xl mx-auto leading-relaxed">
                Connect with fellow gamers, discover amazing content, and be part of our growing community of passionate players and creators.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="/register" class="px-6 py-3 bg-[#03A9F4] hover:bg-[#0288D1] text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Sign Up Free
                </a>
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-transparent border-2 border-[#03A9F4] text-[#03A9F4] hover:bg-[#03A9F4] hover:text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Explore Dashboard
                </a>
            </div>
        </div>
    </section>
</x-layouts.app> 
