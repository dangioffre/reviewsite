@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('podcasts.team.manage', $podcast) }}" 
                       class="text-[#A1A1AA] hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono']">
                        Episode Reviews
                    </h1>
                </div>
                <p class="text-[#A1A1AA] font-['Inter'] ml-8">
                    Manage team review attachments for {{ $podcast->name }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('podcasts.team.manage', $podcast) }}" 
                   class="px-4 py-2 bg-[#6366F1] text-white rounded-lg font-['Inter'] hover:bg-[#5B21B6] transition-all duration-200 text-sm">
                    Back to Podcast Management
                </a>
                <a href="{{ route('podcasts.show', $podcast) }}" 
                   class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg font-['Inter'] hover:bg-[#52525B] transition-all duration-200 text-sm">
                    View Podcast
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Episodes Grid -->
        @if($episodes->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($episodes as $episode)
                    <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl overflow-hidden hover:border-[#52525B] transition-colors">
                        <!-- Episode Header -->
                        <div class="p-4 border-b border-[#3F3F46]">
                            <div class="flex items-start space-x-3">
                                @if($episode->artwork_url)
                                    <img src="{{ $episode->artwork_url }}" alt="{{ $episode->title }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 bg-[#3F3F46] rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        @if($episode->display_number)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#E53E3E]/20 text-[#E53E3E]">
                                                {{ $episode->display_number }}
                                            </span>
                                        @endif
                                        @if($episode->episode_type !== 'full')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                                                {{ ucfirst($episode->episode_type) }}
                                            </span>
                                        @endif
                                        @if($episode->is_explicit)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400">
                                                E
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-white font-medium text-sm leading-tight mb-1">{{ Str::limit($episode->title, 50) }}</h3>
                                    <div class="flex items-center justify-between text-xs text-[#A1A1AA]">
                                        <span>{{ $episode->published_at->format('M j, Y') }}</span>
                                        @if($episode->formatted_duration)
                                            <span>{{ $episode->formatted_duration }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attached Reviews -->
                        <div class="p-4">
                            @if($episode->attachedReviews->count() > 0)
                                <div class="mb-3">
                                    <h4 class="text-xs font-medium text-[#A1A1AA] mb-2">Attached Reviews ({{ $episode->attachedReviews->count() }})</h4>
                                    <div class="space-y-2">
                                        @foreach($episode->attachedReviews->take(3) as $review)
                                            <div class="flex items-center justify-between bg-[#27272A] rounded-lg p-2">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-medium text-white truncate">{{ $review->product->name }}</p>
                                                    <div class="flex items-center space-x-2">
                                                        <div class="flex items-center">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <svg class="w-2.5 h-2.5 {{ $i <= ($review->rating / 2) ? 'text-yellow-400' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                        <span class="text-xs text-[#A1A1AA]">{{ $review->rating }}/10</span>
                                                    </div>
                                                </div>
                                                <span class="text-xs text-[#A1A1AA] ml-2">{{ $review->user->name }}</span>
                                            </div>
                                        @endforeach
                                        @if($episode->attachedReviews->count() > 3)
                                            <div class="text-center">
                                                <span class="text-xs text-[#A1A1AA]">+{{ $episode->attachedReviews->count() - 3 }} more reviews</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="mb-3">
                                    <h4 class="text-xs font-medium text-[#A1A1AA] mb-2">Attached Reviews (0)</h4>
                                    <div class="text-center py-4">
                                        <svg class="w-8 h-8 text-[#3F3F46] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                        <p class="text-xs text-[#A1A1AA]">No reviews attached</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-3 border-t border-[#3F3F46]">
                                <div class="flex items-center space-x-3 text-xs text-[#A1A1AA]">
                                    <span title="Episode reviews">{{ $episode->reviews_count }} comments</span>
                                    @if($episode->isPublished())
                                        <span class="text-green-400">Published</span>
                                    @else
                                        <span class="text-yellow-400">Scheduled</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" 
                                       class="text-blue-400 hover:text-blue-300 text-xs transition-colors">
                                        View
                                    </a>
                                    <button class="manage-reviews-btn text-[#E53E3E] hover:text-red-400 text-xs transition-colors font-medium"
                                            data-episode-slug="{{ $episode->slug }}"
                                            data-episode-title="{{ $episode->title }}">
                                        Manage Reviews
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($episodes->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $episodes->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-12 text-center">
                <svg class="w-16 h-16 text-[#3F3F46] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
                <h3 class="text-xl font-bold text-white mb-2 font-['Inter']">No Episodes Found</h3>
                <p class="text-[#A1A1AA] font-['Inter']">Episodes will appear here once they're synced from your RSS feed.</p>
            </div>
        @endif
    </div>
</div>

<!-- Review Management Modal -->
<div id="review-management-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg max-w-5xl w-full max-h-[85vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-[#3F3F46]">
                <h3 class="text-lg font-semibold text-white">Manage Reviews: <span id="episode-title" class="text-[#E53E3E]"></span></h3>
                <button id="close-modal-btn" class="text-[#A1A1AA] hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4 overflow-y-auto max-h-[65vh]">
                <!-- Search and Filter -->
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="review-search" 
                               placeholder="Search reviews by title, game, or author..."
                               class="w-full px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none text-sm">
                    </div>
                    <select id="review-filter" class="px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none text-sm">
                        <option value="all">All Reviews</option>
                        <option value="attached">Currently Attached</option>
                        <option value="available">Available to Attach</option>
                    </select>
                </div>

                <!-- Available Reviews -->
                <div id="available-reviews-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <!-- Reviews will be loaded here via JavaScript -->
                </div>

                <!-- Loading State -->
                <div id="reviews-loading" class="text-center py-8 hidden">
                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-[#E53E3E]"></div>
                    <p class="text-[#A1A1AA] mt-2 text-sm">Loading reviews...</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between p-4 border-t border-[#3F3F46] bg-[#27272A]">
                <div class="text-sm text-[#A1A1AA]">
                    <span id="selected-count">0</span> reviews selected
                </div>
                <div class="flex items-center space-x-3">
                    <button id="bulk-attach-btn" 
                            class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Attach Selected
                    </button>
                    <button id="bulk-detach-btn" 
                            class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Remove Selected
                    </button>
                    <button id="close-modal-btn-2" 
                            class="px-3 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('review-management-modal');
    const manageBtns = document.querySelectorAll('.manage-reviews-btn');
    const closeBtns = document.querySelectorAll('#close-modal-btn, #close-modal-btn-2');
    const searchInput = document.getElementById('review-search');
    const filterSelect = document.getElementById('review-filter');
    const reviewsGrid = document.getElementById('available-reviews-grid');
    const loadingDiv = document.getElementById('reviews-loading');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkAttachBtn = document.getElementById('bulk-attach-btn');
    const bulkDetachBtn = document.getElementById('bulk-detach-btn');
    const episodeTitleSpan = document.getElementById('episode-title');

    let allReviews = [];
    let selectedReviews = new Set();
    let currentEpisodeSlug = null;

    // Open modal
    manageBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentEpisodeSlug = this.dataset.episodeSlug;
            episodeTitleSpan.textContent = this.dataset.episodeTitle;
            modal.classList.remove('hidden');
            loadReviews();
        });
    });

    // Close modal
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.classList.add('hidden');
            selectedReviews.clear();
            currentEpisodeSlug = null;
            updateSelectedCount();
        });
    });

    // Close modal on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            selectedReviews.clear();
            currentEpisodeSlug = null;
            updateSelectedCount();
        }
    });

    // Search and filter
    searchInput.addEventListener('input', debounce(filterReviews, 300));
    filterSelect.addEventListener('change', filterReviews);

    // Load reviews from server
    async function loadReviews() {
        if (!currentEpisodeSlug) return;
        
        loadingDiv.classList.remove('hidden');
        reviewsGrid.innerHTML = '';
        selectedReviews.clear();

        try {
            console.log('Loading reviews for episode:', currentEpisodeSlug);
            const url = `/api/podcasts/{{ $podcast->slug }}/episodes/${currentEpisodeSlug}/available-reviews`;
            console.log('API URL:', url);
            
            const response = await fetch(url);
            console.log('Response status:', response.status);
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                allReviews = data.reviews;
                renderReviews(allReviews);
            } else {
                showError('Failed to load reviews: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading reviews:', error);
            showError('Failed to load reviews: ' + error.message);
        } finally {
            loadingDiv.classList.add('hidden');
        }
    }

    // Render reviews in the grid
    function renderReviews(reviews) {
        reviewsGrid.innerHTML = '';

        if (reviews.length === 0) {
            reviewsGrid.innerHTML = `
                <div class="col-span-3 text-center py-8">
                    <p class="text-[#A1A1AA]">No reviews found</p>
                </div>
            `;
            return;
        }

        reviews.forEach(review => {
            const isAttached = review.is_attached;
            const isSelected = selectedReviews.has(review.id);
            
            const reviewCard = document.createElement('div');
            reviewCard.className = `review-card bg-[#27272A] rounded-lg border border-[#3F3F46] p-3 cursor-pointer transition-all ${isSelected ? 'border-[#E53E3E] bg-[#E53E3E]/10' : 'hover:border-[#A1A1AA]'}`;
            reviewCard.dataset.reviewId = review.id;
            reviewCard.dataset.attached = isAttached;

            reviewCard.innerHTML = `
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <input type="checkbox" 
                                   class="review-checkbox rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                   ${isSelected ? 'checked' : ''}>
                            ${isAttached ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400">Attached</span>' : ''}
                        </div>
                        <h4 class="text-white font-medium text-sm mb-1">${review.title}</h4>
                        <p class="text-[#A1A1AA] text-xs mb-2">${review.product.name} (${review.product.type})</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                ${generateStars(review.rating)}
                                <span class="ml-1 text-xs text-[#A1A1AA]">${review.rating}/10</span>
                            </div>
                            <span class="text-xs text-[#A1A1AA]">by ${review.user.name}</span>
                        </div>
                    </div>
                </div>
            `;

            // Add click handler
            reviewCard.addEventListener('click', function(e) {
                if (e.target.type !== 'checkbox') {
                    const checkbox = reviewCard.querySelector('.review-checkbox');
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });

            // Add checkbox handler
            const checkbox = reviewCard.querySelector('.review-checkbox');
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    selectedReviews.add(review.id);
                    reviewCard.classList.add('border-[#E53E3E]', 'bg-[#E53E3E]/10');
                } else {
                    selectedReviews.delete(review.id);
                    reviewCard.classList.remove('border-[#E53E3E]', 'bg-[#E53E3E]/10');
                }
                updateSelectedCount();
            });

            reviewsGrid.appendChild(reviewCard);
        });
    }

    // Filter reviews based on search and filter
    function filterReviews() {
        const searchTerm = searchInput.value.toLowerCase();
        const filterValue = filterSelect.value;

        let filteredReviews = allReviews.filter(review => {
            const matchesSearch = !searchTerm || 
                review.title.toLowerCase().includes(searchTerm) ||
                review.product.name.toLowerCase().includes(searchTerm) ||
                review.user.name.toLowerCase().includes(searchTerm);

            const matchesFilter = filterValue === 'all' ||
                (filterValue === 'attached' && review.is_attached) ||
                (filterValue === 'available' && !review.is_attached);

            return matchesSearch && matchesFilter;
        });

        renderReviews(filteredReviews);
    }

    // Update selected count and button states
    function updateSelectedCount() {
        const count = selectedReviews.size;
        selectedCountSpan.textContent = count;
        
        bulkAttachBtn.disabled = count === 0;
        bulkDetachBtn.disabled = count === 0;
    }

    // Bulk attach reviews
    bulkAttachBtn.addEventListener('click', async function() {
        if (selectedReviews.size === 0) return;

        const reviewIds = Array.from(selectedReviews);
        await bulkAction('attach', reviewIds);
    });

    // Bulk detach reviews
    bulkDetachBtn.addEventListener('click', async function() {
        if (selectedReviews.size === 0) return;

        const reviewIds = Array.from(selectedReviews);
        await bulkAction('detach', reviewIds);
    });

    // Perform bulk action
    async function bulkAction(action, reviewIds) {
        if (!currentEpisodeSlug) return;

        try {
            const response = await fetch(`/api/podcasts/{{ $podcast->slug }}/episodes/${currentEpisodeSlug}/bulk-review-action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: action,
                    review_ids: reviewIds
                })
            });

            const data = await response.json();
            
            if (data.success) {
                showSuccess(`${reviewIds.length} reviews ${action}ed successfully`);
                selectedReviews.clear();
                loadReviews();
                // Refresh the page to show updated attached reviews
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showError(data.message || `Failed to ${action} reviews`);
            }
        } catch (error) {
            console.error(`Error ${action}ing reviews:`, error);
            showError(`Failed to ${action} reviews`);
        }
    }

    // Utility functions
    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            const filled = i <= (rating / 2) ? 'text-yellow-400' : 'text-gray-600';
            stars += `<svg class="w-3 h-3 ${filled}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
        }
        return stars;
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showSuccess(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    function showError(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection