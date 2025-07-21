@props([
    'activity',
    'showIcon' => true
])

@php
    $activityColors = [
        'review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'like' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        'comment' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'follow' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    ];
    
    $activityIcons = [
        'review' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        'like' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />',
        'comment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />',
        'follow' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />',
    ];
@endphp

<div class="flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
    @if($showIcon)
        <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full {{ $activityColors[$activity['type']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }} flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $activityIcons[$activity['type']] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />' !!}
                </svg>
            </div>
        </div>
    @endif
    
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
            @if($activity['type'] === 'review')
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $activity['action'] === 'published' ? 'Published' : 'Created' }} a review
                </span>
            @elseif($activity['type'] === 'like')
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                    Received a like from <span class="font-semibold">{{ $activity['liked_by'] }}</span>
                </span>
            @elseif($activity['type'] === 'comment')
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                    Published a comment
                </span>
            @elseif($activity['type'] === 'follow')
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                    Started following
                    @if($activity['is_live'] ?? false)
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            <span class="text-red-500 font-semibold">LIVE</span>
                        </span>
                    @endif
                </span>
            @endif
            
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ $activity['created_at']->diffForHumans() }}
            </span>
        </div>
        
        <div class="text-sm text-gray-600 dark:text-gray-300">
            @if($activity['type'] === 'review')
                <a href="{{ $activity['url'] }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    "{{ $activity['title'] }}"
                </a>
                <span class="text-gray-500 dark:text-gray-400"> for </span>
                <span class="font-medium">{{ $activity['product'] }}</span>
                <span class="text-gray-500 dark:text-gray-400"> ({{ $activity['rating'] }}/10)</span>
            @elseif($activity['type'] === 'like')
                <span class="text-gray-500 dark:text-gray-400">on review </span>
                <a href="{{ $activity['url'] }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    "{{ $activity['title'] }}"
                </a>
                <span class="text-gray-500 dark:text-gray-400"> for </span>
                <span class="font-medium">{{ $activity['product'] }}</span>
            @elseif($activity['type'] === 'comment')
                <a href="{{ $activity['url'] }}" class="font-medium text-green-600 dark:text-green-400 hover:underline">
                    "{{ $activity['title'] }}"
                </a>
                <span class="text-gray-500 dark:text-gray-400"> on episode </span>
                <span class="font-medium">{{ $activity['episode_title'] }}</span>
                <span class="text-gray-500 dark:text-gray-400"> ({{ $activity['rating'] }}/10)</span>
            @elseif($activity['type'] === 'follow')
                <a href="{{ $activity['url'] }}" class="font-medium text-purple-600 dark:text-purple-400 hover:underline">
                    {{ $activity['title'] }}
                </a>
                <span class="text-gray-500 dark:text-gray-400"> on </span>
                <span class="font-medium">{{ $activity['platform'] }}</span>
            @endif
        </div>
        
        <div class="flex items-center gap-2 mt-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ 
                $activity['product_type'] === 'game' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                ($activity['product_type'] === 'Podcast' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                ($activity['product_type'] === 'Streamer' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 
                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'))
            }}">
                {{ ucfirst($activity['product_type']) }}
            </span>
            @if($activity['type'] === 'follow' && ($activity['is_live'] ?? false))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse mr-1"></span>
                    LIVE
                </span>
            @endif
        </div>
    </div>
    
    <div class="flex-shrink-0">
        <a href="{{ $activity['url'] }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>
</div> 
