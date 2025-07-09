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
                title="This Month"
                :value="$stats['reviews_this_month']"
                icon="review"
                color="green"
                :trend="$stats['reviews_this_month'] > 0 ? '+' . $stats['reviews_this_month'] : '0'"
                description="Reviews this month"
            />
        </div>

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