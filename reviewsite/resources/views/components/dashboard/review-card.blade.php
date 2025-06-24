@props([
    'review',
    'showProduct' => true,
    'showActions' => true,
    'compact' => false
])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    @if($showProduct)
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
                    @endif
                    
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
                
                @if($showProduct)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Review for <a href="{{ route($review->product->type === 'game' ? 'games.show' : 'tech.show', $review->product) }}" 
                                     class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $review->product->name }}
                        </a>
                    </p>
                @endif
                
                @if(!$compact)
                    <div class="text-gray-700 dark:text-gray-300 text-sm line-clamp-3 mb-4">
                        {{ Str::limit(strip_tags($review->content), 200) }}
                    </div>
                @endif
                
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        <span>{{ $review->created_at->format('M j, Y') }}</span>
                        @if($review->is_published)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Draft
                            </span>
                        @endif
                    </div>
                    
                    @if($showActions)
                        <div class="flex items-center gap-2">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                </svg>
                                <span>{{ $review->likes_count ?? 0 }}</span>
                            </span>
                            
                            <a href="{{ route($review->product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit', [$review->product, $review]) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                Edit
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 