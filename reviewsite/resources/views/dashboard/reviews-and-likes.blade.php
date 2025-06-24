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

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Navigation Sidebar -->
            <div class="lg:col-span-1">
                <x-dashboard.navigation current-page="reviews-and-likes" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
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
                        @if($item['type'] === 'review')
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30">
                                                    My Review
                                                </span>
                                                <div class="flex items-center gap-2">
                                                    @if($item['data']->product->genre)
                                                        <span class="text-sm text-[#A1A1AA] font-['Inter']">
                                                            {{ $item['data']->product->genre->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <div class="flex items-center gap-1">
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <span class="text-sm {{ $i <= $item['data']->rating ? 'text-yellow-400' : 'text-[#3F3F46]' }}">
                                                            ★
                                                        </span>
                                                    @endfor
                                                    <span class="text-sm text-[#A1A1AA] ml-1 font-['Inter']">({{ $item['data']->rating }}/10)</span>
                                                </div>
                                            </div>
                                            
                                            <h3 class="text-lg font-semibold text-white mb-2 font-['Inter']">
                                                <a href="{{ route($item['data']->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$item['data']->product, $item['data']]) }}" 
                                                   class="hover:text-[#2563EB] transition-colors">
                                                    {{ $item['data']->title }}
                                                </a>
                                            </h3>
                                            
                                            <p class="text-sm text-[#A1A1AA] mb-3 font-['Inter']">
                                                Review for <a href="{{ route($item['data']->product->type === 'game' ? 'games.show' : 'tech.show', $item['data']->product) }}" 
                                                             class="text-[#2563EB] hover:underline">
                                                    {{ $item['data']->product->name }}
                                                </a>
                                            </p>
                                            
                                            <div class="text-[#A1A1AA] text-sm line-clamp-3 mb-4 font-['Inter']">
                                                {{ Str::limit(strip_tags($item['data']->content), 200) }}
                                            </div>
                                            
                                            <div class="flex items-center justify-between text-sm text-[#71717A] font-['Inter']">
                                                <div class="flex items-center gap-4">
                                                    <span>{{ $item['data']->created_at->format('M j, Y') }}</span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-[#E53E3E]" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                                        </svg>
                                                        <span>{{ $item['data']->likes_count ?? 0 }}</span>
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route($item['data']->product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit', [$item['data']->product, $item['data']]) }}" 
                                                       class="text-[#2563EB] hover:text-[#3B82F6] transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#E53E3E]/20 text-[#E53E3E] border border-[#E53E3E]/30">
                                                    Liked Review
                                                </span>
                                                <div class="flex items-center gap-2">
                                                    @if($item['data']->product->genre)
                                                        <span class="text-sm text-[#A1A1AA] font-['Inter']">
                                                            {{ $item['data']->product->genre->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <div class="flex items-center gap-1">
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <span class="text-sm {{ $i <= $item['data']->rating ? 'text-yellow-400' : 'text-[#3F3F46]' }}">
                                                            ★
                                                        </span>
                                                    @endfor
                                                    <span class="text-sm text-[#A1A1AA] ml-1 font-['Inter']">({{ $item['data']->rating }}/10)</span>
                                                </div>
                                            </div>
                                            
                                            <h3 class="text-lg font-semibold text-white mb-2 font-['Inter']">
                                                <a href="{{ route($item['data']->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$item['data']->product, $item['data']]) }}" 
                                                   class="hover:text-[#2563EB] transition-colors">
                                                    {{ $item['data']->title }}
                                                </a>
                                            </h3>
                                            
                                            <p class="text-sm text-[#A1A1AA] mb-3 font-['Inter']">
                                                by <span class="font-medium">{{ $item['data']->user->name }}</span> • 
                                                Review for <a href="{{ route($item['data']->product->type === 'game' ? 'games.show' : 'tech.show', $item['data']->product) }}" 
                                                             class="text-[#2563EB] hover:underline">
                                                    {{ $item['data']->product->name }}
                                                </a>
                                            </p>
                                            
                                            <div class="text-[#A1A1AA] text-sm line-clamp-3 mb-4 font-['Inter']">
                                                {{ Str::limit(strip_tags($item['data']->content), 200) }}
                                            </div>
                                            
                                            <div class="flex items-center justify-between text-sm text-[#71717A] font-['Inter']">
                                                <div class="flex items-center gap-4">
                                                    <span>{{ $item['data']->created_at->format('M j, Y') }}</span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-[#E53E3E]" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                                        </svg>
                                                        <span>{{ $item['data']->likes_count ?? 0 }}</span>
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-[#71717A]">
                                                        Liked {{ $item['date']->diffForHumans() }}
                                                    </span>
                                                    <button onclick="unlikeReview({{ $item['data']->id }})" 
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
                        @endif
                    @empty
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-12 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-white mb-2 font-['Share_Tech_Mono']">No content found</h3>
                            <p class="text-[#A1A1AA] mb-6 font-['Inter']">Start by writing reviews or liking content!</p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('games.index') }}" class="inline-flex items-center px-4 py-2 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors font-['Inter']">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Review a Game
                                </a>
                                <a href="{{ route('tech.index') }}" class="inline-flex items-center px-4 py-2 bg-[#7C3AED] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-['Inter']">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Review Tech
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($paginator->hasPages())
                    <div class="mt-8">
                        {{ $paginator->links() }}
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
        const reviewElement = event.target.closest('.bg-gradient-to-br');
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