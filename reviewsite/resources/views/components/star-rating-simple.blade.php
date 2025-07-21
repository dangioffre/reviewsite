@props(['product', 'userRating' => null, 'size' => 'text-lg'])

@php
    $communityRating = $product->community_rating ?? 0;
    $communityCount = $product->community_reviews_count ?? 0;
@endphp

<div class="star-rating-simple" x-data="starRatingSimple('{{ $product->slug }}', {{ $userRating ?? 'null' }}, {{ $communityRating }})">
    <div class="flex items-center gap-2">
        <div class="flex">
            @for($i = 1; $i <= 5; $i++)
                <button 
                    class="star-rating {{ $size }} transition-colors duration-200 cursor-pointer"
                    :class="{
                        'text-yellow-400': (userRating && (userRating/2) >= {{ $i }}) || (!userRating && (communityRating/2) >= {{ $i }}),
                        'text-gray-600 hover:text-yellow-300': (userRating && (userRating/2) < {{ $i }}) || (!userRating && (communityRating/2) < {{ $i }})
                    }"
                    data-rating="{{ $i * 2 }}"
                    @guest onclick="showLoginPrompt()" @endguest
                    @auth @click="rateGame({{ $i * 2 }})" @endauth
                >
                    ★
                </button>
            @endfor
        </div>
        <span class="text-sm text-[#A1A1AA]">
            <span x-text="communityRating.toFixed(1)">{{ number_format($communityRating, 1) }}</span>/10
        </span>
    </div>
    
    <!-- Toast Notification -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50"
         style="display: none;">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span x-text="toastMessage">Rating submitted successfully!</span>
        </div>
    </div>
</div>

<script>
function starRatingSimple(productSlug, initialUserRating, initialCommunityRating) {
    return {
        userRating: initialUserRating,
        communityRating: initialCommunityRating,
        showToast: false,
        toastMessage: '',
        
        rateGame(rating) {
            // Update visual feedback immediately
            this.userRating = rating;
            
            // Send AJAX request
            fetch(`/games/${productSlug}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ rating: rating })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update community stats
                    this.communityRating = data.communityRating;
                    
                    // Show success toast
                    this.toastMessage = data.message || 'Rating submitted successfully!';
                    this.showToast = true;
                    
                    // Hide toast after 3 seconds
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                } else {
                    // Show error toast
                    this.toastMessage = data.error || 'Failed to submit rating.';
                    this.showToast = true;
                    
                    // Hide toast after 3 seconds
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Rating submission failed:', error);
                this.toastMessage = 'Failed to submit rating. Please try again.';
                this.showToast = true;
                
                setTimeout(() => {
                    this.showToast = false;
                }, 3000);
            });
        }
    }
}
</script> 
