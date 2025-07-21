<x-layouts.app>
<div class="min-h-screen bg-[#151515]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">Liked Reviews</h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">Reviews you've liked and bookmarked</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-[#2563EB] hover:text-[#3B82F6] transition-colors font-['Inter']">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Navigation -->
        <div class="mb-8">
            <nav class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                        </svg>
                        <span class="font-medium">Overview</span>
                    </a>
                    
                    <a href="{{ route('dashboard.reviews-and-likes') }}" 
                       class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-medium">Reviews & Likes</span>
                    </a>
                    
                    <a href="{{ route('dashboard.lists') }}" 
                       class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span class="font-medium">Lists</span>
                    </a>
                    
                    <a href="{{ route('dashboard.collection') }}" 
                       class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">Game Collection</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Stats Summary -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-[#E53E3E] font-['Share_Tech_Mono']">{{ $likedReviews->total() }}</div>
                    <div class="text-sm text-[#A1A1AA] font-['Inter']">Total Liked</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-[#2563EB] font-['Share_Tech_Mono']">
                        {{ $likedReviews->where('product.type', 'game')->count() }}
                    </div>
                    <div class="text-sm text-[#A1A1AA] font-['Inter']">Games</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-[#7C3AED] font-['Share_Tech_Mono']">
                        {{ $likedReviews->where('product.type', 'hardware')->count() + $likedReviews->where('product.type', 'accessory')->count() }}
                    </div>
                    <div class="text-sm text-[#A1A1AA] font-['Inter']">Tech</div>
                </div>
            </div>
        </div>

        <!-- Liked Reviews List -->
        <div class="space-y-6">
            @forelse($likedReviews as $review)
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $review->product->type === 'game' ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'bg-[#7C3AED]/20 text-[#7C3AED] border border-[#7C3AED]/30' }}">
                                            {{ ucfirst($review->product->type) }}
                                        </span>
                                        @if($review->product->genre)
                                            <span class="text-sm text-[#A1A1AA] font-['Inter']">
                                                {{ $review->product->genre->name }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 10; $i++)
                                            <span class="text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-[#3F3F46]' }}">
                                                ★
                                            </span>
                                        @endfor
                                        <span class="text-sm text-[#A1A1AA] ml-1 font-['Inter']">({{ $review->rating }}/10)</span>
                                    </div>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-white mb-2 font-['Inter']">
                                    <a href="{{ route($review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$review->product, $review]) }}" 
                                       class="hover:text-[#2563EB] transition-colors">
                                        {{ $review->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-sm text-[#A1A1AA] mb-3 font-['Inter']">
                                    by <span class="font-medium">{{ $review->user->name }}</span> • 
                                    Review for <a href="{{ route($review->product->type === 'game' ? 'games.show' : 'tech.show', $review->product) }}" 
                                                 class="text-[#2563EB] hover:underline">
                                        {{ $review->product->name }}
                                    </a>
                                </p>
                                
                                <div class="text-[#A1A1AA] text-sm line-clamp-3 mb-4 font-['Inter']">
                                    {{ Str::limit(strip_tags($review->content), 200) }}
                                </div>
                                
                                <div class="flex items-center justify-between text-sm text-[#71717A] font-['Inter']">
                                    <div class="flex items-center gap-4">
                                        <span>{{ $review->created_at->format('M j, Y') }}</span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-[#E53E3E]" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                            </svg>
                                            <span>{{ $review->likes_count ?? 0 }}</span>
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-[#71717A]">
                                            Liked {{ optional($review->pivot->created_at)->diffForHumans() }}
                                        </span>
                                        <button onclick="unlikeReview({{ $review->id }})" 
                                                class="text-[#E53E3E] hover:text-[#DC2626] transition-colors">
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
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                    <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No liked reviews yet</h3>
                    <p class="text-[#A1A1AA] mb-6 text-lg font-['Inter']">Start exploring reviews and like the ones you find helpful!</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('games.index') }}" class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                            Browse Games
                        </a>
                        <a href="{{ route('tech.index') }}" class="bg-gradient-to-r from-[#7C3AED] to-[#8B5CF6] hover:from-[#6D28D9] hover:to-[#7C3AED] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                            Browse Tech
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($likedReviews->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $likedReviews->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function unlikeReview(reviewId) {
    if (confirm('Are you sure you want to unlike this review?')) {
        // Make AJAX call to unlike the review
        fetch(`/reviews/${reviewId}/unlike`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => {
            // Reload the page to update the list
            window.location.reload();
        });
    }
}
</script>
</x-layouts.app> 
