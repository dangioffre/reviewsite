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
        </div>

        <!-- Episode Info (if available and requested) -->
        @if($showEpisode && $review->episode)
            <div class="mt-1 flex items-center space-x-2">
                <span class="text-xs text-gray-500">Episode:</span>
                <span class="text-xs text-gray-400">{{ $review->episode->display_number }}</span>
                <span class="text-xs text-gray-400">{{ $review->episode->title }}</span>
            </div>
        @endif
    </div>
</div> 
