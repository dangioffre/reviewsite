@php
    $isStaff = $type === 'staff';
    $showRoute = $review->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show';
@endphp
<div class="relative flex group transition-transform duration-200 hover:scale-[1.025] hover:shadow-2xl">
    <div class="w-2 rounded-l-xl {{ $isStaff ? 'bg-gradient-to-b from-[#E53E3E] to-[#DC2626]' : 'bg-gradient-to-b from-[#2563EB] to-[#1e40af]' }} mr-4"></div>
    <div class="bg-[#1A1A1B] rounded-xl p-6 border border-[#3F3F46] flex-1 flex flex-col">
        <div class="flex items-center gap-3 mb-2">
            <x-review-identity :review="$review" />
            <div class="ml-auto">
                <div class="text-[#A1A1AA] text-xs font-['Inter']">{{ $review->created_at->format('M d, Y') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2 mb-2">
            <span class="text-yellow-400 font-bold text-xl font-['Share_Tech_Mono']">{{ $review->rating }}/10</span>
            <div class="flex">
                @for($i = 1; $i <= 10; $i++)
                    <svg class="w-5 h-5 {{ $review->rating >= $i ? 'text-[#FFC107]' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
            </div>
        </div>
        <div class="font-bold text-lg mb-1 text-white font-['Inter']">{{ $review->title }}</div>
        <div class="text-[#A1A1AA] leading-relaxed font-['Inter'] mb-2 flex-1">
            @if(isset($review->content) && $review->content)
                <p>{{ Str::limit($review->content, 150) }}</p>
            @else
                <p>{{ $review->review ?? 'No review content available.' }}</p>
            @endif
        </div>
        <div class="mt-auto pt-4 flex items-center justify-between">
            <a href="{{ route($showRoute, [$review->product, $review]) }}" class="text-[#2563EB] hover:text-blue-400 font-semibold inline-block">
                View Full Review &rarr;
            </a>
            <x-like-button 
                :review="$review"
                :like-url="$review->product->type === 'game' ? route('games.reviews.like', [$review->product, $review]) : route('tech.reviews.like', [$review->product, $review])"
                :liked="auth()->check() && $review->isLikedBy(auth()->user())"
                :count="$review->likes_count"
                :can-like="auth()->check()"
            />
        </div>
    </div>
</div>

<script>
function likeReview(reviewId, likeUrl, initiallyLiked, initialCount, canLike) {
    return {
        liked: initiallyLiked,
        count: initialCount,
        canLike: canLike,
        toggleLike() {
            if (!this.canLike) {
                showLoginPrompt();
                return;
            }
            fetch(likeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.liked !== undefined) {
                    this.liked = data.liked;
                    this.count = data.likes_count;
                }
            });
        }
    }
}
</script> 
