<x-layouts.app>
<div class="min-h-screen bg-[#151515]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">My Reviews</h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">Manage and view all your reviews</p>
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
                    <div class="text-2xl font-bold text-[#2563EB] font-['Share_Tech_Mono']">{{ $reviews->total() }}</div>
                    <div class="text-sm text-[#A1A1AA] font-['Inter']">Total Reviews</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-[#10B981] font-['Share_Tech_Mono']">
                        {{ $reviews->where('is_published', true)->count() }}
                    </div>
                    <div class="text-sm text-[#A1A1AA] font-['Inter']">Published</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-[#F59E0B] font-['Share_Tech_Mono']">
                        {{ $reviews->where('is_published', false)->count() }}
                    </div>
                    <div class="text-sm text-[#A1A1AA] font-['Inter']">Drafts</div>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="space-y-6">
            @forelse($reviews as $review)
                <x-dashboard.review-card :review="$review" />
            @empty
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No reviews yet</h3>
                    <p class="text-[#A1A1AA] mb-6 text-lg font-['Inter']">Start sharing your thoughts about games and tech products!</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('games.index') }}" class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Review a Game
                        </a>
                        <a href="{{ route('tech.index') }}" class="bg-gradient-to-r from-[#7C3AED] to-[#8B5CF6] hover:from-[#6D28D9] hover:to-[#7C3AED] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Review Tech
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
</x-layouts.app> 
