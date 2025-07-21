<x-layouts.app>
<div class="min-h-screen bg-[#151515]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">Reviews & Likes</h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">Manage and view all your reviews and liked content</p>
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
            <x-dashboard.navigation />
        </div>

        <!-- Filter Tabs -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Filter Content</h2>
                <div class="text-[#A1A1AA] text-sm font-['Inter']">
                    {{ $paginator->total() }} total items
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('dashboard.reviews-and-likes', ['filter' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg font-medium text-sm font-['Inter'] transition-colors {{ $filter === 'all' ? 'bg-[#2563EB] text-white' : 'bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white' }}">
                    All Content
                </a>
                <a href="{{ route('dashboard.reviews-and-likes', ['filter' => 'reviews']) }}" 
                   class="px-4 py-2 rounded-lg font-medium text-sm font-['Inter'] transition-colors {{ $filter === 'reviews' ? 'bg-[#2563EB] text-white' : 'bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white' }}">
                    My Reviews
                </a>
                <a href="{{ route('dashboard.reviews-and-likes', ['filter' => 'likes']) }}" 
                   class="px-4 py-2 rounded-lg font-medium text-sm font-['Inter'] transition-colors {{ $filter === 'likes' ? 'bg-[#2563EB] text-white' : 'bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white' }}">
                    Liked Reviews
                </a>
            </div>
        </div>

        <!-- Content List -->
        <div class="space-y-6">
            @forelse($paginator as $item)
                <x-dashboard.review-card :item="$item" />
            @empty
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No content found</h3>
                    <p class="text-[#A1A1AA] mb-6 text-lg font-['Inter']">
                        @if($filter === 'reviews')
                            You haven't written any reviews yet.
                        @elseif($filter === 'likes')
                            You haven't liked any reviews yet.
                        @else
                            No reviews or likes to display.
                        @endif
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('games.index') }}" class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Browse Games
                        </a>
                        <a href="{{ route('tech.index') }}" class="bg-gradient-to-r from-[#7C3AED] to-[#8B5CF6] hover:from-[#6D28D9] hover:to-[#7C3AED] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Browse Tech
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($paginator->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $paginator->links() }}
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
