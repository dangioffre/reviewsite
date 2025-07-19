@extends('layouts.app')

@section('title', 'Discover Streamers')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">Discover Streamers</h1>
        <div class="text-[#A1A1AA]">
            {{ $profiles->total() }} {{ Str::plural('streamer', $profiles->total()) }} found
        </div>
    </div>
    
    <!-- Search and Filter Form -->
    <div class="bg-[#27272A] border border-[#3F3F46] rounded-xl p-6 mb-8">
        <form method="GET" action="{{ route('streamer.profiles.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-white mb-2">Search</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name or bio..."
                           class="w-full bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none">
                </div>
                
                <!-- Platform Filter -->
                <div>
                    <label for="platform" class="block text-sm font-medium text-white mb-2">Platform</label>
                    <select id="platform" name="platform" class="w-full bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none">
                        <option value="">All Platforms</option>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform }}" {{ request('platform') === $platform ? 'selected' : '' }}>
                                {{ ucfirst($platform) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Live Status Filter -->
                <div>
                    <label for="live_status" class="block text-sm font-medium text-white mb-2">Status</label>
                    <select id="live_status" name="live_status" class="w-full bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none">
                        <option value="">All Status</option>
                        <option value="live" {{ request('live_status') === 'live' ? 'selected' : '' }}>Live Now</option>
                        <option value="offline" {{ request('live_status') === 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </div>
                
                <!-- Verified Filter -->
                <div>
                    <label for="verified" class="block text-sm font-medium text-white mb-2">Verified</label>
                    <select id="verified" name="verified" class="w-full bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none">
                        <option value="">All</option>
                        <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified Only</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Sort -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-white mb-2">Sort By</label>
                    <select id="sort" name="sort" class="w-full bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] focus:outline-none">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Newest</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="platform" {{ request('sort') === 'platform' ? 'selected' : '' }}>Platform</option>
                        <option value="live_status" {{ request('sort') === 'live_status' ? 'selected' : '' }}>Live Status</option>
                        <option value="followers" {{ request('sort') === 'followers' ? 'selected' : '' }}>Followers</option>
                    </select>
                </div>
                
                <!-- Submit and Clear -->
                <div class="flex items-end gap-3">
                    <button type="submit" class="bg-[#E53E3E] hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Apply Filters
                    </button>
                    <a href="{{ route('streamer.profiles.index') }}" class="bg-[#3F3F46] hover:bg-[#52525B] text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Clear All
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    @if($profiles->isEmpty())
        <div class="text-center py-12">
            <div class="text-6xl mb-4">📺</div>
            <h3 class="text-2xl font-bold text-white mb-2">No streamers found</h3>
            <p class="text-[#A1A1AA] mb-6">Try adjusting your search criteria or filters.</p>
            <a href="{{ route('streamer.profiles.index') }}" class="bg-[#E53E3E] hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Clear Filters
            </a>
        </div>
    @else
        <!-- Streamer Profiles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($profiles as $profile)
                <div class="bg-[#27272A] border border-[#3F3F46] rounded-xl overflow-hidden hover:border-[#E53E3E] transition-colors group">
                    @if($profile->profile_photo_url)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ $profile->profile_photo_url }}" 
                                 alt="{{ $profile->channel_name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @else
                        <div class="aspect-video bg-[#3F3F46] flex items-center justify-center">
                            <span class="text-4xl text-[#A1A1AA]">{{ substr($profile->channel_name, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-white text-lg leading-tight">
                                <a href="{{ route('streamer.profile.show', $profile) }}" class="hover:text-[#E53E3E] transition-colors">
                                    {{ $profile->channel_name }}
                                </a>
                            </h3>
                            <div class="flex flex-col gap-1 ml-2">
                                <!-- Platform Badge -->
                                <span class="bg-[#3F3F46] text-[#A1A1AA] px-2 py-1 rounded text-xs font-medium">
                                    {{ ucfirst($profile->platform) }}
                                </span>
                                
                                <!-- Live Status -->
                                @if($profile->isLive())
                                    <span class="bg-red-600 text-white px-2 py-1 rounded text-xs font-medium flex items-center gap-1">
                                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                        LIVE
                                    </span>
                                @endif
                                
                                <!-- Verification Badge -->
                                <x-verification-badge :profile="$profile" size="xs" />
                            </div>
                        </div>
                        
                        @if($profile->bio)
                            <p class="text-[#A1A1AA] text-sm mb-3 line-clamp-2">{{ Str::limit($profile->bio, 80) }}</p>
                        @endif
                        
                        <!-- Follower Count -->
                        @if($profile->followers_count ?? 0 > 0)
                            <div class="text-[#A1A1AA] text-sm mb-4 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                {{ $profile->followers_count }} {{ Str::plural('follower', $profile->followers_count) }}
                            </div>
                        @endif
                        
                        <a href="{{ route('streamer.profile.show', $profile) }}" 
                           class="block w-full bg-[#E53E3E] hover:bg-red-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors">
                            View Profile
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $profiles->links() }}
        </div>
    @endif
</div>
@endsection