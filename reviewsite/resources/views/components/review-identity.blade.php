@props(['review', 'showEpisode' => false])

<div {{ $attributes->merge(['class' => 'flex items-center space-x-3']) }}>
    <!-- User Avatar -->
    <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
        <span class="text-white font-medium text-sm">{{ substr($review->user->name, 0, 1) }}</span>
    </div>

    <!-- Review Identity -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center space-x-2">
            <!-- User Name -->
            <span class="text-sm font-medium text-white">{{ $review->user->name }}</span>

            <!-- Podcast Identity -->
            @if($review->isPodcastReview())
                <span class="text-sm text-gray-400">reviewed as</span>
                <div class="flex items-center space-x-2">
                    @if($review->podcast->logo_url)
                        <img src="{{ $review->podcast->logo_url }}" 
                             alt="{{ $review->podcast->name }}"
                             class="w-6 h-6 rounded object-cover">
                    @else
                        <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center">
                            <span class="text-white font-medium text-xs">{{ substr($review->podcast->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="text-sm font-medium text-blue-400">{{ $review->podcast->name }}</span>
                </div>
            @endif
        </div>

        <!-- Episode Info (if available and requested) -->
        @if($showEpisode && $review->episode)
            <div class="mt-1 flex items-center space-x-2">
                <span class="text-xs text-gray-500">Episode:</span>
                <span class="text-xs text-gray-400">{{ $review->episode->display_number }}</span>
                <span class="text-xs text-gray-400">{{ $review->episode->title }}</span>
            </div>
        @endif

        <!-- Review Type Badge -->
        <div class="mt-1 flex items-center space-x-2">
            @if($review->isPodcastReview())
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                    Podcast Review
                </span>
            @elseif($review->is_staff_review)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Staff Review
                </span>
            @else
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    User Review
                </span>
            @endif
        </div>
    </div>
</div> 