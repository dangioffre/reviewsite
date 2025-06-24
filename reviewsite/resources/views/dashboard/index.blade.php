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

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Navigation Sidebar -->
            <div class="lg:col-span-1">
                <x-dashboard.navigation current-page="dashboard" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46]">
                    <div class="p-6 border-b border-[#3F3F46]">
                        <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Recent Activity</h2>
                        <p class="text-[#A1A1AA] mt-1 font-['Inter']">Your latest reviews and interactions</p>
                    </div>
                    <div class="divide-y divide-[#3F3F46]">
                        @forelse($recentActivity as $activity)
                            <x-dashboard.activity-item :activity="$activity" />
                        @empty
                            <div class="p-6 text-center text-[#A1A1AA]">
                                <svg class="w-12 h-12 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="font-['Inter']">No recent activity yet.</p>
                                <p class="text-sm mt-1 font-['Inter']">Start by writing your first review!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Reviews</h2>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">Your latest reviews</p>
                            </div>
                            <a href="{{ route('dashboard.reviews') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                View All →
                            </a>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($recentReviews as $review)
                            <x-dashboard.review-card :review="$review" :compact="true" />
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p>No reviews yet.</p>
                                <a href="{{ route('games.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mt-2 inline-block">
                                    Write your first review →
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Most Liked Reviews -->
                @if($mostLikedReviews->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Most Liked Reviews</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Your reviews with the most likes</p>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($mostLikedReviews as $review)
                            <x-dashboard.review-card :review="$review" :compact="true" />
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Game Collection Stats -->
                @if($gameStats['have_count'] > 0 || $gameStats['want_count'] > 0 || $gameStats['played_count'] > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Game Collection</h2>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">Your gaming stats</p>
                            </div>
                            <a href="{{ route('dashboard.collection') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                View Collection →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $gameStats['have_count'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Owned</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $gameStats['want_count'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Wishlist</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $gameStats['played_count'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Played</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-layouts.app> 