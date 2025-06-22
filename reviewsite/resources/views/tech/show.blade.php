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
                        <a href="{{ route('tech.index') }}" class="text-[#A1A1AA] hover:text-white transition-colors">Tech</a>
                        <svg class="w-4 h-4 text-[#3F3F46]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-[#E53E3E] font-medium">{{ $product->name }}</span>
                    </div>
                </nav>

                <!-- Game Info Header -->
                <div class="grid lg:grid-cols-3 gap-12 items-start">
                    <!-- Game Image -->
                    <div class="lg:col-span-1">
                        <div class="relative overflow-hidden rounded-2xl border border-[#3F3F46] shadow-2xl">
                            <img 
                                src="{{ $product->image ?? 'https://via.placeholder.com/400x300/27272A/A1A1AA?text=No+Image' }}" 
                                alt="{{ $product->name }}"
                                class="w-full h-96 object-cover"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    
                    <!-- Game Details -->
                    <div class="lg:col-span-2">
                        <div class="mb-6">
                            <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">
                                {{ $product->name }}
                            </h1>
                            
                            <!-- Tags -->
                            <div class="flex flex-wrap items-center gap-3 mb-6">
                                <span class="inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white text-sm font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono']">
                                    {{ $product->type }}
                                </span>
                                @if($product->platform)
                                    <span class="inline-flex items-center text-white text-sm font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono']" style="background: linear-gradient(135deg, {{ $product->platform->color }}, {{ $product->platform->color }}dd);">
                                        {{ $product->platform->name }}
                                    </span>
                                @endif
                                @if($product->genre)
                                    <span class="inline-flex items-center text-white text-sm font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono']" style="background: linear-gradient(135deg, {{ $product->genre->color }}, {{ $product->genre->color }}dd);">
                                        {{ $product->genre->name }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Description -->
                            <p class="text-xl text-[#A1A1AA] leading-relaxed font-['Inter'] mb-8">
                                {{ $product->description ?? 'No description available.' }}
                            </p>
                        </div>
                        
                        <!-- Rating Summary -->
                        <div class="grid md:grid-cols-2 gap-6">
                            @if($product->staff_rating)
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl p-6 border border-[#3F3F46]">
                                <h3 class="text-lg font-bold text-white mb-4 font-['Share_Tech_Mono']">Staff Rating</h3>
                                <div class="flex items-center gap-4">
                                    <div class="relative w-16 h-16">
                                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                            <path class="text-[#3F3F46]" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                            <path class="text-[#E53E3E]" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" 
                                                  stroke-dasharray="{{ ($product->staff_rating / 10) * 100 }}, 100" 
                                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                                            </path>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-xl font-bold text-white font-['Share_Tech_Mono']">{{ number_format($product->staff_rating, 1) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ number_format($product->staff_rating, 1) }}/10</div>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ ($product->staff_rating/2) >= $i ? 'text-[#FFC107]' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <div class="text-sm text-[#A1A1AA] font-['Inter']">{{ $product->staff_reviews_count }} staff {{ Str::plural('review', $product->staff_reviews_count) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($product->community_rating)
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl p-6 border border-[#3F3F46]">
                                <h3 class="text-lg font-bold text-white mb-4 font-['Share_Tech_Mono']">Community Rating</h3>
                                <div class="flex items-center gap-4">
                                    <div class="relative w-16 h-16">
                                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                            <path class="text-[#3F3F46]" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                            <path class="text-[#2563EB]" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" 
                                                  stroke-dasharray="{{ ($product->community_rating / 10) * 100 }}, 100" 
                                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                                            </path>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-xl font-bold text-white font-['Share_Tech_Mono']">{{ number_format($product->community_rating, 1) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ number_format($product->community_rating, 1) }}/10</div>
                                        <div class="text-sm text-[#A1A1AA] font-['Inter']">{{ $product->community_reviews_count }} community {{ Str::plural('review', $product->community_reviews_count) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-12">
            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Left Column: Video & Staff Review -->
                <div class="lg:col-span-2 space-y-12">
                    <!-- Video Section -->
                    @if($product->video_url)
                    <section class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                        <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Product Video</h2>
                        <div class="relative aspect-video rounded-xl overflow-hidden border border-[#3F3F46]">
                            <iframe 
                                src="{{ $product->video_url }}" 
                                class="w-full h-full"
                                frameborder="0" 
                                allowfullscreen
                            ></iframe>
                        </div>
                    </section>
                    @endif

                    <!-- Detailed Information Section -->
                    @if($product->story)
                    <section class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                        <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Detailed Information</h2>
                        <div class="text-[#A1A1AA] leading-relaxed font-['Inter'] prose prose-invert max-w-none">
                            {!! $product->story !!}
                        </div>
                    </section>
                    @endif

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
                                            <span class="text-white font-bold font-['Share_Tech_Mono']">{{ $review->rating }}/10</span>
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ ($review->rating/2) >= $i ? 'text-[#FFC107]' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[#A1A1AA] leading-relaxed font-['Inter']">{{ $review->review }}</p>
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
                                                <span class="text-white font-bold font-['Share_Tech_Mono']">{{ $review->rating }}/10</span>
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ ($review->rating/2) >= $i ? 'text-[#FFC107]' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-[#A1A1AA] leading-relaxed font-['Inter']">
                                            @if($review->content)
                                                <p>{{ Str::limit($review->content, 150) }}</p>
                                                @if(strlen($review->content) > 150 && $review->slug)
                                                    <a href="{{ route('tech.reviews.show', [$product, $review]) }}" class="text-[#2563EB] hover:text-blue-400 font-semibold mt-2 inline-block">
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

                <!-- Right Column: Review Form -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8">
                        @auth
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl text-center">
                            <div class="text-4xl mb-4">✍️</div>
                            <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Share Your Review</h3>
                            <p class="text-[#A1A1AA] mb-6 font-['Inter']">Write a detailed review with your thoughts, ratings, and more.</p>
                            <a href="{{ route('tech.reviews.create', $product) }}" class="inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white px-6 py-3 rounded-xl font-bold font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Write Review
                            </a>
                        </div>
                        @else
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl text-center">
                            <div class="text-4xl mb-4">🔐</div>
                            <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Login to Review</h3>
                            <p class="text-[#A1A1AA] mb-6 font-['Inter']">Sign in to share your thoughts and rate this game.</p>
                            <a href="{{ route('login') }}" class="inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white px-6 py-3 rounded-xl font-bold font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                Sign In
                            </a>
                        </div>
                        @endauth
                        
                        <!-- Product Info Sidebar -->
                        <div class="mt-8 bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                            <h3 class="text-xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Product Info</h3>
                            <div class="space-y-4">
                                @if($product->genre)
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Category</span>
                                    <a href="{{ route('tech.by-category', $product->genre) }}" class="text-[#E53E3E] hover:text-[#DC2626] font-semibold font-['Inter'] transition-colors duration-200">
                                        {{ $product->genre->name }}
                                    </a>
                                </div>
                                @endif
                                @if($product->platform)
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Compatibility</span>
                                    <a href="{{ route('tech.by-platform', $product->platform) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white hover:opacity-80 transition-opacity" 
                                          style="background-color: {{ $product->platform->color }};">
                                        {{ $product->platform->name }}
                                    </a>
                                </div>
                                @endif
                                @if($product->hardware)
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Hardware Type</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white" 
                                          style="background-color: {{ $product->hardware->color }};">
                                        {{ $product->hardware->name }}
                                    </span>
                                </div>
                                @endif
                                @if($product->theme)
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Theme</span>
                                    <a href="{{ route('tech.by-theme', urlencode($product->theme)) }}" class="text-[#E53E3E] hover:text-[#DC2626] font-semibold font-['Inter'] transition-colors duration-200">
                                        {{ $product->theme }}
                                    </a>
                                </div>
                                @endif
                                @if($product->game_modes)
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Features</span>
                                    <span class="text-white font-semibold font-['Inter']">{{ $product->game_modes }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Release Date</span>
                                    <span class="text-white font-['Inter'] font-medium">{{ $product->release_date ? $product->release_date->format('M d, Y') : 'TBA' }}</span>
                                </div>
                                @if($product->developer)
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Manufacturer</span>
                                    <a href="{{ route('tech.by-brand', urlencode($product->developer)) }}" class="text-[#E53E3E] hover:text-[#DC2626] font-semibold font-['Inter'] transition-colors duration-200">
                                        {{ $product->developer }}
                                    </a>
                                </div>
                                @endif
                                @if($product->publisher)
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Publisher</span>
                                    <a href="{{ route('tech.by-publisher', urlencode($product->publisher)) }}" class="text-[#E53E3E] hover:text-[#DC2626] font-semibold font-['Inter'] transition-colors duration-200">
                                        {{ $product->publisher }}
                                    </a>
                                </div>
                                @endif
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Reviews</span>
                                    <span class="text-white font-semibold font-['Inter']">{{ $userReviews->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#A1A1AA] font-['Inter']">Added</span>
                                    <span class="text-white font-semibold font-['Inter']">{{ $product->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-layouts.app> 