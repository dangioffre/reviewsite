@extends('layouts.app')

@section('title', 'Discover Streamers')

@section('content')
<div class="min-h-screen bg-[#151515]">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 via-blue-600/20 to-red-600/20"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center mb-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-purple-500 to-red-500 rounded-2xl mb-6 shadow-2xl">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-bold text-white mb-6 font-['Share_Tech_Mono'] bg-gradient-to-r from-white via-purple-200 to-red-200 bg-clip-text text-transparent">
                    Discover Streamers
                </h1>
                
                <p class="text-xl lg:text-2xl text-zinc-400 mb-8 max-w-3xl mx-auto font-['Inter'] leading-relaxed">
                    Find amazing content creators across Twitch, YouTube, and Kick. Connect with streamers who share your gaming passion.
                </p>
                
                <div class="flex items-center justify-center gap-8 text-zinc-400">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
                        <span class="font-medium">{{ $profiles->total() }} Streamers</span>
                    </div>
                    <div class="w-px h-6 bg-zinc-600"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="font-medium">Live Streams</span>
                    </div>
                    <div class="w-px h-6 bg-zinc-600"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                        <span class="font-medium">All Platforms</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <!-- Advanced Search and Filters -->
        <div class="relative mb-12 mt-16">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 to-red-600/10 rounded-3xl blur-xl"></div>
            <div class="relative bg-gradient-to-br from-zinc-800/80 to-zinc-900/80 backdrop-blur-xl border border-zinc-700/50 rounded-3xl p-8 shadow-2xl">
                <form method="GET" action="{{ route('streamer.profiles.index') }}" class="space-y-8">
                    <!-- Main Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-6 w-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name, bio, or streaming content..."
                               class="w-full bg-zinc-900/50 border border-zinc-600/50 rounded-2xl pl-12 pr-6 py-4 text-white placeholder-zinc-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-300 text-lg backdrop-blur-sm">
                        
                        @if(request('search'))
                            <a href="{{ route('streamer.profiles.index', request()->except('search')) }}" 
                               class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-white transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>

                    <!-- Filter Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Platform Filter -->
                        <div class="space-y-2">
                            <label for="platform" class="block text-sm font-semibold text-white mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                Platform
                            </label>
                            <select id="platform" name="platform" class="w-full bg-zinc-900/50 border border-zinc-600/50 rounded-xl px-4 py-3 text-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition-all duration-300">
                                <option value="">All Platforms</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform }}" {{ request('platform') === $platform ? 'selected' : '' }}>
                                        {{ ucfirst($platform) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Live Status Filter -->
                        <div class="space-y-2">
                            <label for="live_status" class="block text-sm font-semibold text-white mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                                Status
                            </label>
                            <select id="live_status" name="live_status" class="w-full bg-zinc-900/50 border border-zinc-600/50 rounded-xl px-4 py-3 text-white focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition-all duration-300">
                                <option value="">All Status</option>
                                <option value="live" {{ request('live_status') === 'live' ? 'selected' : '' }}>🔴 Live Now</option>
                                <option value="offline" {{ request('live_status') === 'offline' ? 'selected' : '' }}>⚫ Offline</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="space-y-2">
                            <label for="sort" class="block text-sm font-semibold text-white mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                                </svg>
                                Sort By
                            </label>
                            <select id="sort" name="sort" class="w-full bg-zinc-900/50 border border-zinc-600/50 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20 focus:outline-none transition-all duration-300">
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>🕐 Newest</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>📝 Name</option>
                                <option value="platform" {{ request('sort') === 'platform' ? 'selected' : '' }}>🎮 Platform</option>
                                <option value="live_status" {{ request('sort') === 'live_status' ? 'selected' : '' }}>🔴 Live Status</option>
                                <option value="followers" {{ request('sort') === 'followers' ? 'selected' : '' }}>👥 Followers</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-zinc-700/50">
                        <button type="submit" class="flex-1 sm:flex-none bg-gradient-to-r from-purple-600 to-red-600 hover:from-purple-700 hover:to-red-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Apply Filters
                        </button>
                        <a href="{{ route('streamer.profiles.index') }}" class="flex-1 sm:flex-none bg-zinc-700/50 hover:bg-zinc-600/50 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2 border border-zinc-600/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear All
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if($profiles->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-24">
                <div class="relative inline-block">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-red-600/20 rounded-full blur-xl"></div>
                    <div class="relative w-32 h-32 bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-full flex items-center justify-center mb-8 mx-auto shadow-2xl">
                        <svg class="w-16 h-16 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-3xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No streamers found</h3>
                <p class="text-xl text-zinc-400 mb-8 max-w-md mx-auto">We couldn't find any streamers matching your criteria. Try adjusting your search or filters.</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('streamer.profiles.index') }}" class="bg-gradient-to-r from-purple-600 to-red-600 hover:from-purple-700 hover:to-red-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Clear All Filters
                    </a>
                    <a href="{{ route('streamer.profiles.create') }}" class="bg-zinc-700/50 hover:bg-zinc-600/50 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 border border-zinc-600/50">
                        Become a Streamer
                    </a>
                </div>
            </div>
        @else
            <!-- Streamer Profiles Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
                @foreach($profiles as $profile)
                    <div class="group relative">
                        <!-- Background Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 via-blue-600/20 to-red-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-all duration-500 transform group-hover:scale-110"></div>
                        
                        <!-- Main Card -->
                        <div class="relative bg-gradient-to-br from-zinc-800/80 to-zinc-900/80 backdrop-blur-xl border border-zinc-700/50 rounded-3xl overflow-hidden hover:border-purple-500/50 transition-all duration-500 group-hover:transform group-hover:scale-105 shadow-xl hover:shadow-2xl">
                            <!-- Profile Image/Avatar -->
                            <div class="relative">
                                @if($profile->profile_photo_url)
                                    <div class="aspect-video overflow-hidden">
                                        <img src="{{ $profile->profile_photo_url }}" 
                                             alt="{{ $profile->channel_name }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    </div>
                                @else
                                    <div class="aspect-video bg-gradient-to-br from-zinc-700 to-zinc-800 flex items-center justify-center relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/10 via-blue-600/10 to-red-600/10"></div>
                                        <span class="relative text-4xl font-bold text-white">{{ substr($profile->channel_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                
                                <!-- Live Indicator -->
                                @if($profile->isLive())
                                    <div class="absolute top-4 left-4">
                                        <span class="bg-red-600 text-white px-3 py-1.5 rounded-full text-sm font-bold flex items-center gap-2 shadow-lg">
                                            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                            LIVE
                                        </span>
                                    </div>
                                @endif
                                
                                                                 <!-- Platform Badge -->
                                 <div class="absolute top-4 right-4">
                                     <span class="bg-black/50 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-sm font-semibold border border-white/20">
                                         {{ ucfirst($profile->platform) }}
                                     </span>
                                 </div>
                            </div>
                            
                            <!-- Card Content -->
                            <div class="p-6">
                                <!-- Streamer Name -->
                                <h3 class="font-bold text-xl text-white mb-3 group-hover:text-purple-400 transition-all duration-300">
                                    <a href="{{ route('streamer.profile.show', $profile) }}" class="hover:text-purple-300 transition-colors">
                                        {{ $profile->channel_name }}
                                    </a>
                                </h3>
                                
                                <!-- Bio -->
                                @if($profile->bio)
                                    <p class="text-zinc-400 text-sm mb-4 line-clamp-2 leading-relaxed">{{ Str::limit($profile->bio, 90) }}</p>
                                @endif
                                
                                <!-- Stats Row -->
                                <div class="flex items-center justify-between mb-6 text-sm">
                                    @if($profile->followers_count ?? 0 > 0)
                                        <div class="flex items-center gap-2 text-zinc-400">
                                            <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                            </svg>
                                            <span class="font-semibold">{{ number_format($profile->followers_count) }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center gap-2 text-zinc-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-xs">{{ $profile->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                <!-- View Profile Button -->
                                <a href="{{ route('streamer.profile.show', $profile) }}" 
                                   class="block w-full bg-gradient-to-r from-purple-600 to-red-600 hover:from-purple-700 hover:to-red-700 text-white text-center py-3 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 group-hover:scale-105">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Enhanced Pagination -->
            <div class="flex justify-center">
                <div class="bg-gradient-to-br from-zinc-800/80 to-zinc-900/80 backdrop-blur-xl border border-zinc-700/50 rounded-2xl p-4 shadow-xl">
                    {{ $profiles->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection