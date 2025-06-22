<x-layouts.app>
    <div class="min-h-screen bg-[#151515]">
        <!-- Game Header Section -->
        <div class="relative bg-gradient-to-br from-[#27272A] via-[#1A1A1B] to-[#151515] py-16 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, #E53E3E 0%, transparent 50%), radial-gradient(circle at 75% 75%, #2563EB 0%, transparent 50%)"></div>
            </div>
            
            <div class="container mx-auto px-4 relative z-10">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <div class="flex items-center gap-2 text-sm font-['Inter']">
                        <a href="{{ route('home') }}" class="text-[#A1A1AA] hover:text-white transition-colors">Home</a>
                        <svg class="w-4 h-4 text-[#3F3F46]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('games.index') }}" class="text-[#A1A1AA] hover:text-white transition-colors">Games</a>
                        <svg class="w-4 h-4 text-[#3F3F46]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-[#E53E3E] font-medium">{{ $product->name }}</span>
                    </div>
                </nav>

                <!-- Game Title -->
                <div class="mb-8">
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">
                        {{ $product->name }}
                    </h1>
                    
                    <!-- Tags -->
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white text-sm font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono']">
                            {{ $product->type }}
                        </span>
                        @if($product->platform)
                            <span class="inline-flex items-center text-white text-sm font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono']" style="background: linear-gradient(135deg, {{ $product->platform->color ?? '#2563EB' }}, {{ $product->platform->color ?? '#2563EB' }}dd);">
                                {{ $product->platform->name }}
                            </span>
                        @endif
                        @if($product->genre)
                            <span class="inline-flex items-center text-white text-sm font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono']" style="background: linear-gradient(135deg, {{ $product->genre->color ?? '#10B981' }}, {{ $product->genre->color ?? '#10B981' }}dd);">
                                {{ $product->genre->name }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Clean Media Section -->
                <div class="grid lg:grid-cols-12 gap-3 items-start mb-12">
                    <!-- Left Side: Photo and Video -->
                    <div class="lg:col-span-9">
                        <div class="flex flex-col lg:flex-row gap-2 items-start">
                            <!-- Game Poster -->
                            <div class="flex-shrink-0">
                                <div class="bg-[#1A1A1B] rounded-xl overflow-hidden border border-[#3F3F46]/20 max-w-[264px] mx-auto lg:mx-0">
                                    <img 
                                        src="{{ $product->image ?? 'https://via.placeholder.com/264x352/27272A/A1A1AA?text=No+Image' }}" 
                                        alt="{{ $product->name }}"
                                        class="w-full h-auto object-cover"
                                        style="aspect-ratio: 264/352;"
                                    >
                                </div>
                            </div>
                            
                            <!-- YouTube Video -->
                            <div class="flex-1">
                                @if($product->video)
                                    <div class="bg-[#1A1A1B] rounded-xl overflow-hidden border border-[#3F3F46]/20 aspect-video">
                                        <iframe 
                                            src="{{ $product->video }}" 
                                            class="w-full h-full"
                                            frameborder="0" 
                                            allowfullscreen
                                            title="{{ $product->name }} - Gameplay Video"
                                        ></iframe>
                                    </div>
                                @else
                                    <div class="bg-[#1A1A1B] rounded-xl border border-[#3F3F46]/20 aspect-video flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-4xl mb-3 opacity-50">🎮</div>
                                            <h3 class="text-lg font-semibold text-white mb-1 font-['Inter']">No Video Available</h3>
                                            <p class="text-sm text-[#A1A1AA] font-['Inter']">Gameplay video coming soon</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side: Ratings Section -->
                    <div class="lg:col-span-3">
                        <div class="bg-[#1A1A1B] border border-[#3F3F46]/20 rounded-xl p-6">
                            <!-- Staff Rating -->
                            @if($product->staff_rating)
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="bg-green-500 text-black text-lg font-bold px-2 py-1 rounded mr-3">{{ number_format($product->staff_rating, 1) }}</div>
                                    <div>
                                        <div class="text-white font-semibold text-sm">Staff Rating</div>
                                        <div class="text-[#A1A1AA] text-xs">{{ $product->staff_reviews_count }} staff {{ Str::plural('rating', $product->staff_reviews_count) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Community Rating -->
                            <div class="flex items-center justify-between mb-4" id="community-rating-display">
                                <div class="flex items-center">
                                    <div class="bg-green-500 text-black text-lg font-bold px-2 py-1 rounded mr-3">
                                        <span id="community-rating-value">{{ $product->community_rating ? number_format($product->community_rating, 1) : '0.0' }}</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-semibold text-sm">User Rating</div>
                                        <div class="text-[#A1A1AA] text-xs">
                                            <span id="community-rating-count">{{ $product->community_reviews_count ?? 0 }}</span> 
                                            user <span id="community-rating-plural">{{ Str::plural('rating', $product->community_reviews_count ?? 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Star Rating Display -->
                            <div class="flex justify-center mb-4" id="star-rating-container">
                                @for($i = 1; $i <= 10; $i++)
                                    <button 
                                        class="star-rating text-2xl mx-1 transition-colors duration-200 cursor-pointer {{ ($userRating && $userRating >= $i) || (!$userRating && $product->community_rating && $product->community_rating >= $i) ? 'text-yellow-400' : 'text-gray-600 hover:text-yellow-300' }}"
                                        data-rating="{{ $i }}"
                                        @guest onclick="showLoginPrompt()" @endguest
                                        @auth onclick="rateGame({{ $i }})" @endauth
                                    >
                                        ★
                                    </button>
                                @endfor
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <!-- Add to Lists Button -->
                                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add to lists
                                </button>
                                
                                <!-- Write Review Button -->
                                @auth
                                <a href="{{ route('games.reviews.create', $product) }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Write a review
                                </a>
                                @else
                                <button onclick="showLoginPrompt()" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Write a review
                                </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simple Description Section -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Description</h2>
                    <p class="text-[#A1A1AA] leading-relaxed font-['Inter'] text-lg">
                        {{ $product->description ?? 'No description available for this game.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Reviews Content -->
        <div class="container mx-auto px-4 py-12">
            <div class="space-y-12">
                <!-- Staff Reviews Section -->
                @if($staffReviews->count() > 0)
                <section class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-[#E53E3E] bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#E53E3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Staff Reviews</h2>
                    </div>
                    
                    <div class="space-y-6">
                        @foreach($staffReviews as $review)
                            <div class="bg-[#1A1A1B] rounded-xl p-6 border border-[#3F3F46]">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-[#E53E3E] to-[#DC2626] rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-white font-semibold font-['Inter']">{{ $review->user->name }} <span class="text-[#E53E3E] text-sm">STAFF</span></div>
                                            <div class="text-[#A1A1AA] text-sm font-['Inter']">{{ $review->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-[#E53E3E] text-white font-bold font-['Share_Tech_Mono'] px-3 py-1 rounded-lg">{{ $review->rating }}/10</span>
                                    </div>
                                </div>
                                <div class="text-[#A1A1AA] leading-relaxed font-['Inter']">
                                    @if($review->content)
                                        <p>{{ Str::limit($review->content, 200) }}</p>
                                        @if(strlen($review->content) > 200 && $review->slug)
                                            <a href="{{ route('games.reviews.show', [$product, $review]) }}" class="text-[#E53E3E] hover:text-red-400 font-semibold mt-2 inline-block">
                                                Read Full Review →
                                            </a>
                                        @endif
                                    @else
                                        <p>{{ $review->review ?? 'No review content available.' }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- User Reviews Section -->
                <section class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Community Reviews</h2>
                    
                    @if($userReviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($userReviews as $review)
                                <div class="bg-[#1A1A1B] rounded-xl p-6 border border-[#3F3F46]">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-[#E53E3E] to-[#2563EB] rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold font-['Share_Tech_Mono']">{{ substr($review->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-white font-semibold font-['Inter']">{{ $review->user->name }}</div>
                                                <div class="text-[#A1A1AA] text-sm font-['Inter']">{{ $review->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="bg-[#2563EB] text-white font-bold font-['Share_Tech_Mono'] px-3 py-1 rounded-lg">{{ $review->rating }}/10</span>
                                        </div>
                                    </div>
                                    <div class="text-[#A1A1AA] leading-relaxed font-['Inter']">
                                        @if($review->content)
                                            <p>{{ Str::limit($review->content, 150) }}</p>
                                            @if(strlen($review->content) > 150 && $review->slug)
                                                <a href="{{ route('games.reviews.show', [$product, $review]) }}" class="text-[#2563EB] hover:text-blue-400 font-semibold mt-2 inline-block">
                                                    Read Full Review →
                                                </a>
                                            @endif
                                        @else
                                            <p>{{ $review->review ?? 'No review content available.' }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">💬</div>
                            <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">No Community Reviews Yet</h3>
                            <p class="text-[#A1A1AA] font-['Inter']">Be the first to share your thoughts on this game!</p>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    <!-- Login Prompt Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="text-4xl mb-4">🔐</div>
                <h3 class="text-xl font-bold text-white mb-2">Login Required</h3>
                <p class="text-[#A1A1AA] mb-6">You need to be logged in to rate games and write reviews.</p>
                <div class="flex space-x-3">
                    <a href="{{ route('login') }}" class="flex-1 bg-[#E53E3E] hover:bg-[#DC2626] text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 bg-[#3F3F46] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200">
                        Register
                    </a>
                </div>
                <button onclick="closeLoginPrompt()" class="mt-4 text-[#A1A1AA] hover:text-white text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        function showLoginPrompt() {
            document.getElementById('loginModal').classList.remove('hidden');
        }

        function closeLoginPrompt() {
            document.getElementById('loginModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginPrompt();
            }
        });

        @auth
        function rateGame(rating) {
            // Show loading state
            const stars = document.querySelectorAll('.star-rating');
            stars.forEach(star => star.style.pointerEvents = 'none');

            // Submit rating via AJAX
            fetch('{{ route('games.rate', $product) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rating: rating
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the rating display
                    document.getElementById('community-rating-value').textContent = data.communityRating;
                    document.getElementById('community-rating-count').textContent = data.communityCount;
                    document.getElementById('community-rating-plural').textContent = data.communityCount === 1 ? 'rating' : 'ratings';
                    
                    // Update star display to show user's rating
                    updateStarDisplay(data.userRating);
                    
                    // Show success message
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.error || 'Failed to submit rating', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to submit rating. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable stars
                stars.forEach(star => star.style.pointerEvents = 'auto');
            });
        }

        function updateStarDisplay(userRating) {
            const stars = document.querySelectorAll('.star-rating');
            stars.forEach((star, index) => {
                const starRating = parseInt(star.dataset.rating);
                if (starRating <= userRating) {
                    star.className = 'star-rating text-2xl mx-1 transition-colors duration-200 cursor-pointer text-yellow-400';
                } else {
                    star.className = 'star-rating text-2xl mx-1 transition-colors duration-200 cursor-pointer text-gray-600 hover:text-yellow-300';
                }
            });
        }

        function showNotification(message, type) {
            // Create a simple notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-semibold ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        @endauth
    </script>

</x-layouts.app> 