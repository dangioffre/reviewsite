@props(['streamerProfile'])

@php
    $visibleReviews = $streamerProfile->reviews->where('show_on_streamer_profile', true);
@endphp

@if($visibleReviews->count() > 0)
<div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-xl border border-zinc-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white">Recent Reviews</h3>
        </div>
        @if($visibleReviews->count() > 5)
            <button class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                View More ({{ $visibleReviews->count() }})
            </button>
        @endif
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        @foreach($visibleReviews->take(5) as $review)
            <a href="{{ $review->product->type === 'game' ? route('games.reviews.show', [$review->product, $review]) : route('tech.reviews.show', [$review->product, $review]) }}" class="bg-zinc-800/50 rounded-lg border border-zinc-600 p-3 hover:border-blue-500 hover:bg-zinc-800/70 transition-all group cursor-pointer block">
                <!-- Product & Rating Header -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-white font-semibold text-xs line-clamp-1 group-hover:text-blue-400 transition-colors">
                            {{ Str::limit($review->product->name, 20) }}
                        </h4>
                        <div class="text-yellow-400 font-bold text-sm ml-2">
                            {{ $review->rating }}/10
                        </div>
                    </div>
                    
                    <!-- Star Rating -->
                    <div class="flex items-center mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3 h-3 {{ $i <= ($review->rating / 2) ? 'text-yellow-400' : 'text-zinc-600' }}" 
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                </div>
                
                <!-- Review Title -->
                <h5 class="text-blue-400 font-medium text-xs mb-2 line-clamp-1">
                    {{ Str::limit($review->title, 25) }}
                </h5>
                
                <!-- Review Content -->
                <p class="text-zinc-300 text-xs leading-relaxed mb-3 line-clamp-2">
                    {{ Str::limit($review->content, 60) }}
                </p>
                
                <!-- Meta Info -->
                <div class="flex items-center justify-between text-xs text-zinc-500 pt-2 border-t border-zinc-700">
                    <span>{{ $review->created_at->diffForHumans() }}</span>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Review
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    
    @if($visibleReviews->count() > 5)
        <div class="text-center mt-6">
            <button class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold shadow-lg">
                View More Reviews ({{ $visibleReviews->count() - 5 }} more)
            </button>
        </div>
    @endif
</div>

@once
@push('styles')
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endonce
@endif 
