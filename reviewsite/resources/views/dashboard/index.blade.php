<x-layouts.app>
<div class="min-h-screen bg-[#151515]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">Dashboard</h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">Welcome back, {{ $user->name }}!</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-[#2563EB] hover:text-[#3B82F6] transition-colors font-['Inter']">
                        Back to Site
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Navigation -->
        <div class="mb-8">
            <x-dashboard.navigation />
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-dashboard.stats-card 
                title="Total Reviews"
                :value="$stats['total_reviews']"
                icon="review"
                color="blue"
                description="All time reviews"
            />
            
            <x-dashboard.stats-card 
                title="Likes Received"
                :value="$stats['total_likes_received']"
                icon="like"
                color="orange"
                description="Total likes on your reviews"
            />
            
            <x-dashboard.stats-card 
                title="Average Rating"
                :value="$stats['average_rating']"
                icon="star"
                color="yellow"
                description="Your average review rating"
            />
            
            <x-dashboard.stats-card 
                title="Following"
                :value="$stats['followed_streamers']"
                icon="stream"
                color="purple"
                description="Streamers you follow"
            />
        </div>

        <!-- Followed Streamers -->
        @if($followedStreamers->count() > 0)
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Following Streamers</h2>
                    <p class="text-[#A1A1AA] font-['Inter']">Streamers you're currently following</p>
                </div>
                <a href="{{ route('streamer.followers.index') }}" 
                   class="text-[#2563EB] hover:text-[#3B82F6] transition-colors font-['Inter'] text-sm">
                    View All ({{ auth()->user()->followedStreamers()->count() }})
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($followedStreamers as $streamer)
                    <div class="bg-[#1A1A1B] rounded-xl border border-[#3F3F46] p-4 hover:border-[#2563EB] transition-colors group">
                        <!-- Streamer Header -->
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 flex-shrink-0 mr-3">
                                @if($streamer->profile_photo_url)
                                    <img src="{{ $streamer->profile_photo_url }}" 
                                         alt="{{ $streamer->channel_name }}" 
                                         class="w-full h-full rounded-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">{{ substr($streamer->channel_name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white font-semibold truncate group-hover:text-[#2563EB] transition-colors">
                                    {{ $streamer->channel_name }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $streamer->platform === 'twitch' ? 'bg-purple-600 text-white' : 
                                           ($streamer->platform === 'youtube' ? 'bg-red-600 text-white' : 'bg-green-600 text-white') }}">
                                        {{ ucfirst($streamer->platform) }}
                                    </span>
                                    @if($streamer->isLive())
                                        <span class="flex items-center text-xs text-red-400">
                                            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-1"></span>
                                            LIVE
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Streamer Bio -->
                        @if($streamer->bio)
                            <p class="text-[#A1A1AA] text-sm mb-3 line-clamp-2">{{ Str::limit($streamer->bio, 100) }}</p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center justify-between text-sm text-[#A1A1AA] mb-4">
                            <span>{{ number_format($streamer->followers_count) }} followers</span>
                            <span>{{ $streamer->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('streamer.profile.show', $streamer) }}" 
                               class="flex-1 bg-[#2563EB] hover:bg-[#1D4ED8] text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors text-center font-['Inter']">
                                View Profile
                            </a>
                            
                            <form action="{{ route('streamer.unfollow', $streamer) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-2 bg-[#3F3F46] hover:bg-red-600 text-[#A1A1AA] hover:text-white rounded-lg text-sm transition-colors font-['Inter']"
                                        onclick="return confirm('Unfollow {{ $streamer->channel_name }}?')"
                                        title="Unfollow">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
            <h2 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Recent Activity</h2>
            <p class="text-[#A1A1AA] mb-6 font-['Inter']">Your latest reviews and interactions</p>

            <div class="space-y-2">
                @forelse($recentActivity as $activity)
                    <x-dashboard.activity-item :activity="$activity" />
                @empty
                    <div class="text-center py-8">
                        <p class="text-[#A1A1AA]">No recent activity to show.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</x-layouts.app> 