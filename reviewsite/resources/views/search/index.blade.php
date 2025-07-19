@extends('layouts.app')

@section('title', 'Search Results' . ($query ? ' for "' . $query . '"' : ''))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-4">
            @if($query)
                Search Results for "{{ $query }}"
            @else
                Search
            @endif
        </h1>
        
        @if($query)
            <p class="text-[#A1A1AA]">
                Found {{ $totalResults }} {{ Str::plural('result', $totalResults) }}
            </p>
        @endif
    </div>
    
    <!-- Search Form -->
    <div class="bg-[#27272A] border border-[#3F3F46] rounded-xl p-6 mb-8">
        <form method="GET" action="{{ route('search.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $query }}" 
                    placeholder="Search games, streamers, reviews, and more..."
                    class="w-full bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none"
                    autofocus
                >
            </div>
            <div class="md:w-48">
                <select name="category" class="w-full bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none">
                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Categories</option>
                    <option value="games" {{ $category === 'games' ? 'selected' : '' }}>Games</option>
                    <option value="tech" {{ $category === 'tech' ? 'selected' : '' }}>Tech</option>
                    <option value="streamers" {{ $category === 'streamers' ? 'selected' : '' }}>Streamers</option>
                    <option value="reviews" {{ $category === 'reviews' ? 'selected' : '' }}>Streamer Reviews</option>
                    <option value="lists" {{ $category === 'lists' ? 'selected' : '' }}>Lists</option>
                </select>
            </div>
            <button type="submit" class="bg-[#E53E3E] hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Search
            </button>
        </form>
    </div>
    
    @if($query && $totalResults > 0)
        <!-- Search Results -->
        
        <!-- Games Results -->
        @if($results['games']->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="bg-[#E53E3E] text-white px-2 py-1 rounded text-sm mr-3">{{ $results['games']->count() }}</span>
                    Games
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($results['games'] as $game)
                        <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-4 hover:border-[#E53E3E] transition-colors">
                            <h3 class="font-semibold text-white mb-2">
                                <a href="{{ route('games.show', $game->slug) }}" class="hover:text-[#E53E3E] transition-colors">
                                    {{ $game->name }}
                                </a>
                            </h3>
                            @if($game->description)
                                <p class="text-[#A1A1AA] text-sm mb-2">{{ Str::limit($game->description, 100) }}</p>
                            @endif
                            <div class="flex items-center gap-2 text-xs">
                                @if($game->genre)
                                    <span class="bg-[#3F3F46] text-[#A1A1AA] px-2 py-1 rounded">{{ $game->genre->name }}</span>
                                @endif
                                @if($game->platform)
                                    <span class="bg-[#3F3F46] text-[#A1A1AA] px-2 py-1 rounded">{{ $game->platform->name }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Tech Results -->
        @if($results['tech']->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="bg-[#E53E3E] text-white px-2 py-1 rounded text-sm mr-3">{{ $results['tech']->count() }}</span>
                    Tech Products
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($results['tech'] as $tech)
                        <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-4 hover:border-[#E53E3E] transition-colors">
                            <h3 class="font-semibold text-white mb-2">
                                <a href="{{ route('tech.show', $tech->slug) }}" class="hover:text-[#E53E3E] transition-colors">
                                    {{ $tech->name }}
                                </a>
                            </h3>
                            @if($tech->description)
                                <p class="text-[#A1A1AA] text-sm mb-2">{{ Str::limit($tech->description, 100) }}</p>
                            @endif
                            <div class="flex items-center gap-2 text-xs">
                                @if($tech->genre)
                                    <span class="bg-[#3F3F46] text-[#A1A1AA] px-2 py-1 rounded">{{ $tech->genre->name }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Streamers Results -->
        @if($results['streamers']->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="bg-[#E53E3E] text-white px-2 py-1 rounded text-sm mr-3">{{ $results['streamers']->count() }}</span>
                    Streamers
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($results['streamers'] as $streamer)
                        <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-4 hover:border-[#E53E3E] transition-colors">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    @if($streamer->profile_photo_url)
                                        <img src="{{ $streamer->profile_photo_url }}" alt="{{ $streamer->channel_name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-[#3F3F46] rounded-full flex items-center justify-center">
                                            <span class="text-[#A1A1AA] text-lg">{{ substr($streamer->channel_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-semibold text-white">
                                            <a href="{{ route('streamer.profile.show', $streamer) }}" class="hover:text-[#E53E3E] transition-colors">
                                                {{ $streamer->channel_name }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="bg-[#3F3F46] text-[#A1A1AA] px-2 py-1 rounded">{{ ucfirst($streamer->platform) }}</span>
                                            @if($streamer->isLive())
                                                <span class="bg-red-600 text-white px-2 py-1 rounded flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-white rounded-full"></span>
                                                    LIVE
                                                </span>
                                            @endif
                                            @if($streamer->is_verified)
                                                <span class="bg-green-600 text-white px-2 py-1 rounded">✓ Verified</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($streamer->bio)
                                <p class="text-[#A1A1AA] text-sm">{{ Str::limit($streamer->bio, 80) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Streamer Reviews Results -->
        @if($results['streamer_reviews']->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="bg-[#E53E3E] text-white px-2 py-1 rounded text-sm mr-3">{{ $results['streamer_reviews']->count() }}</span>
                    Streamer Reviews
                </h2>
                <div class="space-y-4">
                    @foreach($results['streamer_reviews'] as $review)
                        <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-4 hover:border-[#E53E3E] transition-colors">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-white mb-1">
                                        <a href="{{ $review->product->type === 'game' ? route('games.reviews.show', [$review->product, $review]) : route('tech.reviews.show', [$review->product, $review]) }}" 
                                           class="hover:text-[#E53E3E] transition-colors">
                                            {{ $review->title }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center gap-2 text-sm text-[#A1A1AA] mb-2">
                                        <span>by</span>
                                        <a href="{{ route('streamer.profile.show', $review->streamerProfile) }}" class="text-[#E53E3E] hover:underline">
                                            {{ $review->user->name }} ({{ $review->streamerProfile->channel_name }})
                                        </a>
                                        <span>•</span>
                                        <span>{{ $review->created_at->format('M j, Y') }}</span>
                                        <span>•</span>
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="text-{{ $i <= $review->rating ? 'yellow-400' : '[#3F3F46]' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-[#A1A1AA] text-sm mb-2">{{ Str::limit($review->content, 150) }}</p>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="bg-[#3F3F46] text-[#A1A1AA] px-2 py-1 rounded">{{ $review->product->name }}</span>
                                        <span class="bg-[#3F3F46] text-[#A1A1AA] px-2 py-1 rounded">{{ ucfirst($review->streamerProfile->platform) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Lists Results -->
        @if($results['lists']->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="bg-[#E53E3E] text-white px-2 py-1 rounded text-sm mr-3">{{ $results['lists']->count() }}</span>
                    Lists
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($results['lists'] as $list)
                        <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-4 hover:border-[#E53E3E] transition-colors">
                            <h3 class="font-semibold text-white mb-2">
                                <a href="{{ route('lists.public', $list->slug) }}" class="hover:text-[#E53E3E] transition-colors">
                                    {{ $list->name }}
                                </a>
                            </h3>
                            @if($list->description)
                                <p class="text-[#A1A1AA] text-sm mb-2">{{ Str::limit($list->description, 100) }}</p>
                            @endif
                            <div class="flex items-center justify-between text-xs text-[#A1A1AA]">
                                <span>by {{ $list->user->name }}</span>
                                <div class="flex items-center gap-3">
                                    <span>{{ $list->items_count }} items</span>
                                    <span>{{ $list->followers_count }} followers</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
    @elseif($query && $totalResults === 0)
        <!-- No Results -->
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🔍</div>
            <h2 class="text-2xl font-bold text-white mb-2">No results found</h2>
            <p class="text-[#A1A1AA] mb-6">We couldn't find anything matching "{{ $query }}"</p>
            <div class="text-[#A1A1AA] text-sm">
                <p class="mb-2">Try:</p>
                <ul class="space-y-1">
                    <li>• Checking your spelling</li>
                    <li>• Using different keywords</li>
                    <li>• Searching for a different category</li>
                </ul>
            </div>
        </div>
    @else
        <!-- Search Tips -->
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🔍</div>
            <h2 class="text-2xl font-bold text-white mb-2">Search Everything</h2>
            <p class="text-[#A1A1AA] mb-6">Find games, tech products, streamers, reviews, and lists</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-6">
                    <div class="text-3xl mb-3">🎮</div>
                    <h3 class="font-semibold text-white mb-2">Games & Tech</h3>
                    <p class="text-[#A1A1AA] text-sm">Search through our database of games and tech products</p>
                </div>
                <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-6">
                    <div class="text-3xl mb-3">📺</div>
                    <h3 class="font-semibold text-white mb-2">Streamers</h3>
                    <p class="text-[#A1A1AA] text-sm">Discover streamers from Twitch, YouTube, and Kick</p>
                </div>
                <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg p-6">
                    <div class="text-3xl mb-3">📝</div>
                    <h3 class="font-semibold text-white mb-2">Reviews & Lists</h3>
                    <p class="text-[#A1A1AA] text-sm">Find reviews by streamers and community lists</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection