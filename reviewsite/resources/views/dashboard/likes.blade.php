<x-layouts.app>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Liked Reviews</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Reviews you've liked and bookmarked</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Navigation Sidebar -->
            <div class="lg:col-span-1">
                <x-dashboard.navigation current-page="likes" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Stats Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $likedReviews->total() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Liked</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $likedReviews->where('product.type', 'game')->count() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Games</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $likedReviews->where('product.type', 'hardware')->count() + $likedReviews->where('product.type', 'accessory')->count() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Tech</div>
                        </div>
                    </div>
                </div>

                <!-- Liked Reviews List -->
                <div class="space-y-6">
                    @forelse($likedReviews as $review)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $review->product->type === 'game' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' }}">
                                                    {{ ucfirst($review->product->type) }}
                                                </span>
                                                @if($review->product->genre)
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $review->product->genre->name }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <span class="text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">
                                                        ★
                                                    </span>
                                                @endfor
                                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">({{ $review->rating }}/10)</span>
                                            </div>
                                        </div>
                                        
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                            <a href="{{ route($review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$review->product, $review]) }}" 
                                               class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                {{ $review->title }}
                                            </a>
                                        </h3>
                                        
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            by <span class="font-medium">{{ $review->user->name }}</span> • 
                                            Review for <a href="{{ route($review->product->type === 'game' ? 'games.show' : 'tech.show', $review->product) }}" 
                                                         class="text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $review->product->name }}
                                            </a>
                                        </p>
                                        
                                        <div class="text-gray-700 dark:text-gray-300 text-sm line-clamp-3 mb-4">
                                            {{ Str::limit(strip_tags($review->content), 200) }}
                                        </div>
                                        
                                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center gap-4">
                                                <span>{{ $review->created_at->format('M j, Y') }}</span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                                    </svg>
                                                    <span>{{ $review->likes_count ?? 0 }}</span>
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-400">
                                                    Liked {{ optional($review->pivot->created_at)->diffForHumans() }}
                                                </span>
                                                <button onclick="unlikeReview({{ $review->id }})" 
                                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No liked reviews yet</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Start exploring reviews and like the ones you find helpful!</p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('games.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Browse Games
                                </a>
                                <a href="{{ route('tech.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    Browse Tech
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($likedReviews->hasPages())
                    <div class="mt-8">
                        {{ $likedReviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function unlikeReview(reviewId) {
    if (confirm('Are you sure you want to unlike this review?')) {
        // Find the review in the liked reviews and remove it
        const reviewElement = event.target.closest('.bg-white');
        if (reviewElement) {
            reviewElement.remove();
        }
        
        // You could also make an AJAX call here to unlike the review
        // fetch(`/reviews/${reviewId}/unlike`, {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        //     }
        // });
    }
}
</script>
</x-layouts.app> 