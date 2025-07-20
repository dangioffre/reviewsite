<x-layouts.app>
    <x-slot name="title">Dan & Brian Reviews - Gaming Reviews, Podcasts & Streams</x-slot>
    
    <!-- Hero Section - Recent Staff Reviews -->
    <section class="bg-gradient-to-br from-[#151515] via-[#1A1A1A] to-[#0F0F0F] py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Header -->
            <div class="text-center mb-16">
                <h1 class="text-5xl lg:text-7xl font-bold text-white mb-6 font-['Share_Tech_Mono'] bg-gradient-to-r from-white via-purple-200 to-red-200 bg-clip-text text-transparent">
                    Dan & Brian Reviews
                </h1>
                <p class="text-xl lg:text-2xl text-zinc-400 mb-8 max-w-3xl mx-auto font-['Inter'] leading-relaxed">
                    Your ultimate destination for honest game reviews, epic podcasts, and the best streamers in gaming.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('games.index') }}" class="px-8 py-4 bg-[#E53E3E] hover:bg-red-700 text-white font-bold rounded-xl transition-all duration-300 font-['Inter'] shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        Explore Games
                    </a>
                    <a href="{{ route('streamer.profiles.index') }}" class="px-8 py-4 bg-transparent border-2 border-purple-500 text-purple-400 hover:bg-purple-500 hover:text-white font-bold rounded-xl transition-all duration-300 font-['Inter'] shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        Discover Streamers
                    </a>
                </div>
            </div>

            <!-- Staff Reviews Hero Grid -->
            @if($heroReviews->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
                <div class="lg:col-span-2">
                    @php $featuredReview = $heroReviews->first(); @endphp
                    <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-2xl overflow-hidden border border-zinc-700 group hover:border-[#E53E3E] transition-all duration-500 transform hover:-translate-y-2">
                        <div class="aspect-[16/9] bg-gradient-to-br from-purple-600/20 via-blue-600/20 to-red-600/20 flex items-center justify-center relative overflow-hidden">
                            @if($featuredReview->product->thumbnail_url)
                                <img src="{{ $featuredReview->product->thumbnail_url }}" alt="{{ $featuredReview->product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-red-500 to-purple-600 opacity-20"></div>
                                <span class="text-6xl font-bold text-white opacity-50">{{ substr($featuredReview->product->name, 0, 1) }}</span>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute top-4 left-4 flex items-center gap-3">
                                <span class="bg-[#E53E3E] text-white px-3 py-1.5 rounded-full text-sm font-bold">STAFF PICK</span>
                                <div class="flex items-center bg-black/50 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-sm font-bold">
                                    <svg class="w-4 h-4 mr-1.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    {{ $featuredReview->rating }}/10
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-sm text-zinc-400 font-['Inter']">{{ $featuredReview->product->genre->name ?? 'Gaming' }}</span>
                                <span class="text-zinc-600">•</span>
                                <span class="text-sm text-zinc-400 font-['Inter']">{{ $featuredReview->created_at->format('M j, Y') }}</span>
                            </div>
                            <h2 class="text-2xl font-bold text-white mb-3 font-['Share_Tech_Mono'] group-hover:text-[#E53E3E] transition-colors duration-300">
                                {{ $featuredReview->title }}
                            </h2>
                            <p class="text-zinc-400 mb-6 leading-relaxed font-['Inter']">{{ Str::limit($featuredReview->content, 150) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($featuredReview->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">{{ $featuredReview->user->name }}</p>
                                        <p class="text-zinc-500 text-xs">Staff Reviewer</p>
                                    </div>
                                </div>
                                <a href="{{ route($featuredReview->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$featuredReview->product, $featuredReview]) }}" 
                                   class="bg-[#E53E3E] hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-300 font-['Inter'] transform hover:scale-105">
                                    Read Review
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Reviews -->
                <div class="space-y-4">
                    @foreach($heroReviews->skip(1)->take(3) as $review)
                    <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-xl shadow-xl p-4 border border-zinc-700 group hover:border-purple-500 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex items-center bg-black/50 text-white px-2 py-1 rounded text-xs font-bold">
                                <svg class="w-3 h-3 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                {{ $review->rating }}/10
                            </div>
                            <span class="text-xs text-zinc-500">{{ $review->created_at->format('M j') }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2 font-['Share_Tech_Mono'] group-hover:text-purple-400 transition-colors">
                            {{ $review->title }}
                        </h3>
                        <p class="text-zinc-400 text-sm mb-4 line-clamp-2">{{ Str::limit($review->content, 100) }}</p>
                        <a href="{{ route($review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$review->product, $review]) }}" 
                           class="text-purple-400 hover:text-purple-300 text-sm font-semibold transition-colors">
                            Read More →
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
    <section class="bg-[#1A1A1A] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Recently Released Games</h2>
                    <p class="text-zinc-400 font-['Inter']">The newest games in our collection, ordered by release date</p>
                </div>
                <a href="{{ route('games.index') }}" class="text-[#E53E3E] hover:text-red-400 font-semibold transition-colors font-['Inter'] flex items-center gap-2">
                    View All Games
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($recentGames as $game)
                <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-lg shadow-lg border border-zinc-700 overflow-hidden group hover:border-[#E53E3E] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl flex flex-col h-full">
                    <div class="aspect-[4/3] bg-gradient-to-br from-blue-600/20 to-purple-600/20 flex items-center justify-center relative overflow-hidden">
                        @if($game->thumbnail_url)
                            <img src="{{ $game->thumbnail_url }}" alt="{{ $game->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-blue-600 opacity-20"></div>
                            <span class="text-3xl font-bold text-white opacity-50">{{ substr($game->name, 0, 1) }}</span>
                        @endif
                        @if($game->release_date && $game->release_date->gt(now()->subDays(7)))
                            <div class="absolute top-2 left-2">
                                <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">NEW</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-grow">
                        <h3 class="text-white font-semibold mb-1 line-clamp-2 text-sm group-hover:text-[#E53E3E] transition-colors flex-grow">{{ $game->name }}</h3>
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-zinc-400">{{ $game->genre->name ?? 'Gaming' }}</span>
                            <span class="text-zinc-500">{{ $game->release_date ? $game->release_date->format('M j') : 'TBA' }}</span>
                        </div>
                        <a href="{{ route('games.show', $game) }}" class="w-full bg-[#E53E3E] hover:bg-red-700 text-white py-1.5 px-2 rounded text-xs font-semibold transition-all duration-300 block text-center transform hover:scale-105 mt-auto">
                            View Game
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Recent Streamers -->
    @if($recentStreamers->count() > 0)
    <section class="bg-[#151515] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Featured Streamers</h2>
                    <p class="text-zinc-400 font-['Inter']">Meet the latest creators joining our community</p>
                </div>
                <a href="{{ route('streamer.profiles.index') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors font-['Inter'] flex items-center gap-2">
                    Discover All Streamers
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($recentStreamers as $streamer)
                <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-lg shadow-lg border border-zinc-700 p-4 group hover:border-purple-500 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl flex flex-col h-full">
                    <div class="flex flex-col flex-grow">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 flex-shrink-0 mr-3 relative">
                                @if($streamer->profile_photo_url)
                                    <img src="{{ $streamer->profile_photo_url }}" alt="{{ $streamer->channel_name }}" class="w-full h-full rounded-full object-cover border-2 border-purple-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center border-2 border-purple-500">
                                        <span class="text-white font-bold text-sm">{{ substr($streamer->channel_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                @if($streamer->isLive())
                                    <div class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse inline-block mr-1"></span>
                                        LIVE
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white font-semibold text-base group-hover:text-purple-400 transition-colors line-clamp-2 leading-tight">{{ $streamer->channel_name }}</h3>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $streamer->platform === 'twitch' ? 'bg-purple-600 text-white' : ($streamer->platform === 'youtube' ? 'bg-red-600 text-white' : 'bg-green-600 text-white') }}">
                                        {{ ucfirst($streamer->platform) }}
                                    </span>
                                    @if($streamer->is_verified)
                                        <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($streamer->bio)
                            <p class="text-zinc-400 text-sm mb-3 line-clamp-2 flex-grow">{{ Str::limit($streamer->bio, 100) }}</p>
                        @endif
                        <div class="flex items-center justify-between text-sm text-zinc-500 mb-3">
                            <span>{{ number_format($streamer->followers_count) }} followers</span>
                            <span>{{ $streamer->created_at->format('M j') }}</span>
                        </div>
                        <a href="{{ route('streamer.profile.show', $streamer) }}" 
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-3 rounded text-sm font-semibold transition-all duration-300 block text-center transform hover:scale-105 mt-auto">
                            View Profile
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Recent Podcasts -->
    @if($recentPodcasts->count() > 0)
    <section class="bg-[#1A1A1A] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Gaming Podcasts</h2>
                    <p class="text-zinc-400 font-['Inter']">Dive deep into gaming discussions and insights</p>
                </div>
                <a href="{{ route('podcasts.index') }}" class="text-green-400 hover:text-green-300 font-semibold transition-colors font-['Inter'] flex items-center gap-2">
                    All Podcasts
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($recentPodcasts as $podcast)
                <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-lg shadow-lg border border-zinc-700 p-4 group hover:border-green-500 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl flex flex-col h-full">
                    <div class="flex flex-col flex-grow">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($podcast->cover_image)
                                    <img src="{{ $podcast->cover_image }}" alt="{{ $podcast->name }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white font-semibold text-base mb-1 group-hover:text-green-400 transition-colors line-clamp-2 leading-tight">{{ $podcast->name }}</h3>
                                <p class="text-zinc-500 text-sm truncate">by {{ $podcast->owner->name }}</p>
                            </div>
                        </div>
                        @if($podcast->description)
                            <p class="text-zinc-400 text-sm mb-3 line-clamp-2 flex-grow">{{ Str::limit($podcast->description, 100) }}</p>
                        @endif
                        <div class="flex items-center justify-between text-sm text-zinc-500 mb-3">
                            <span>{{ $podcast->episodes_count }} episodes</span>
                            <span>{{ $podcast->reviews_count }} reviews</span>
                        </div>
                        <a href="{{ route('podcasts.show', $podcast) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded text-sm font-semibold transition-all duration-300 block text-center transform hover:scale-105 mt-auto">
                            Listen Now
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Recent Lists -->
    @if($recentLists->count() > 0)
    <section class="bg-[#151515] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Community Lists</h2>
                    <p class="text-zinc-400 font-['Inter']">Discover curated collections created by our community</p>
                </div>
                <a href="{{ route('lists.index') }}" class="text-yellow-400 hover:text-yellow-300 font-semibold transition-colors font-['Inter'] flex items-center gap-2">
                    Browse All Lists
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($recentLists as $list)
                <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-lg shadow-lg border border-zinc-700 p-4 group hover:border-yellow-500 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl flex flex-col h-full">
                    <div class="flex flex-col flex-grow">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white font-semibold text-base mb-1 group-hover:text-yellow-400 transition-colors line-clamp-2 leading-tight">{{ $list->name }}</h3>
                                <p class="text-zinc-500 text-sm truncate">by {{ $list->user->name }}</p>
                            </div>
                        </div>
                        @if($list->description)
                            <p class="text-zinc-400 text-sm mb-3 line-clamp-2 flex-grow">{{ Str::limit($list->description, 100) }}</p>
                        @endif
                        <div class="flex items-center justify-between text-sm text-zinc-500 mb-3">
                            <span>{{ $list->items_count }} games</span>
                            <span>{{ $list->followers_count }} followers</span>
                        </div>
                        <a href="{{ route('lists.public', $list->slug) }}" 
                           class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-3 rounded text-sm font-semibold transition-all duration-300 block text-center transform hover:scale-105 mt-auto">
                            View List
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Call to Action -->
    <section class="bg-gradient-to-r from-[#E53E3E] to-purple-600 py-16">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-4xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Join the Gaming Revolution</h2>
            <p class="text-xl text-white/90 mb-8 font-['Inter']">
                Connect with gamers, discover new content, and be part of our growing community of passionate players.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="/register" class="px-8 py-4 bg-white text-[#E53E3E] font-bold rounded-xl hover:bg-gray-100 transition-all duration-300 font-['Inter'] shadow-xl transform hover:-translate-y-1">
                    Sign Up Free
                </a>
                <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-transparent border-2 border-white text-white hover:bg-white hover:text-[#E53E3E] font-bold rounded-xl transition-all duration-300 font-['Inter'] shadow-xl transform hover:-translate-y-1">
                    Explore Dashboard
                </a>
            </div>
        </div>
    </section>
</x-layouts.app> 