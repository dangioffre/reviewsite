<x-layouts.app>
    <div class="min-h-screen bg-[#151515]">
        <!-- Hero Section with Game/Product Background -->
        <div class="relative h-96 overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0">
                <img src="{{ $review->product->image ?? 'https://via.placeholder.com/1920x400/27272A/A1A1AA?text=No+Image' }}" 
                     alt="{{ $review->product->name }}" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#151515] via-[#151515]/80 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#151515]/90 via-transparent to-[#151515]/90"></div>
            </div>
            
            <!-- Hero Content -->
            <div class="relative h-full flex items-end">
                <div class="container mx-auto px-4 pb-12">
                    <!-- Breadcrumb -->
                    <nav class="mb-8">
                        <div class="flex items-center space-x-2 text-sm text-[#A1A1AA]">
                            <a href="{{ route('home') }}" class="hover:text-[#E53E3E] transition-colors">Home</a>
                            <span>/</span>
                            @if($review->product->type === 'game')
                                <a href="{{ route('games.index') }}" class="hover:text-[#E53E3E] transition-colors">Games</a>
                            @else
                                <a href="{{ route('tech.index') }}" class="hover:text-[#E53E3E] transition-colors">Tech</a>
                            @endif
                            <span>/</span>
                            @if($review->product->type === 'game')
                                <a href="{{ route('games.show', $review->product) }}" class="hover:text-[#E53E3E] transition-colors">{{ $review->product->name }}</a>
                            @else
                                <a href="{{ route('tech.show', $review->product) }}" class="hover:text-[#E53E3E] transition-colors">{{ $review->product->name }}</a>
                            @endif
                            <span>/</span>
                            <span class="text-white">Review</span>
                        </div>
                    </nav>

                    <div class="grid lg:grid-cols-3 gap-8 items-end">
                        <!-- Review Title & Info -->
                        <div class="lg:col-span-2">
                            @if($review->is_staff_review)
                                <div class="inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono'] mb-4 shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Staff Review
                                </div>
                            @endif
                            
                            <h1 class="text-5xl lg:text-6xl font-bold text-white mb-6 font-['Share_Tech_Mono'] leading-tight">
                                {{ $review->title }}
                            </h1>
                            
                            <div class="flex flex-wrap items-center gap-6 text-[#A1A1AA] mb-4">
                                <div class="bg-[#27272A]/80 backdrop-blur-sm rounded-full px-4 py-2 border border-[#3F3F46]">
                                    <x-review-identity :review="$review" :showEpisode="true" />
                                </div>
                                <div class="bg-[#27272A]/80 backdrop-blur-sm rounded-full px-4 py-2 border border-[#3F3F46]">
                                    <span class="font-['Inter'] text-white">{{ $review->created_at->format('M j, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2 ml-2" x-data="likeReview(
                                    {{ $review->id }},
                                    '{{ $review->product->type === 'game' ? route('games.reviews.like', [$review->product, $review]) : route('tech.reviews.like', [$review->product, $review]) }}',
                                    {{ (auth()->check() && $review->isLikedBy(auth()->user())) ? 'true' : 'false' }},
                                    {{ $review->likes_count }},
                                    {{ auth()->check() ? 'true' : 'false' }}
                                )">
                                    <button @click.prevent="toggleLike" :class="[liked ? 'text-blue-500' : 'text-gray-400 hover:text-blue-400', canLike ? 'cursor-pointer' : 'cursor-not-allowed']" class="focus:outline-none transition-colors" :disabled="!canLike">
                                        <!-- Heroicons solid thumbs-up -->
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 9V5a3 3 0 0 0-6 0v4" />
                                            <path d="M5 15V9a2 2 0 0 1 2-2h7.5a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z" />
                                        </svg>
                                    </button>
                                    <span x-text="count"></span>
                                    <template x-if="!canLike">
                                        <span class="ml-2 text-xs text-gray-400">Login to like</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Circle -->
                        <div class="flex justify-center lg:justify-end">
                            <div class="relative">
                                <div class="w-32 h-32 relative">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-[#3F3F46]" stroke="currentColor" stroke-width="4" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                        <path class="text-[#E53E3E]" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round" 
                                              stroke-dasharray="{{ ($review->rating / 10) * 100 }}, 100" 
                                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                                        </path>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <div class="text-4xl font-bold text-white font-['Share_Tech_Mono']">{{ $review->rating }}</div>
                                        <div class="text-sm text-[#A1A1AA] font-['Inter']">/ 10</div>
                                    </div>
                                </div>
                                <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2">
                                    <div class="bg-[#27272A]/90 backdrop-blur-sm rounded-full px-4 py-1 border border-[#3F3F46]">
                                        <span class="text-white font-['Inter'] text-sm font-semibold">Overall Score</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-12">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Main Content Column -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Review Content -->
                    <article class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
                        <div class="p-8 lg:p-12">
                            <div class="prose prose-invert prose-lg max-w-none">
                                <div class="text-[#FFFFFF] font-['Inter'] text-lg leading-relaxed space-y-6">
                                    @php
                                        $converter = new \League\CommonMark\CommonMarkConverter([
                                            'html_input' => 'escape',
                                            'allow_unsafe_links' => false,
                                        ]);
                                    @endphp
                                    {!! $converter->convert($review->content)->getContent() !!}
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Pros & Cons -->
                    @if($review->positive_points_list || $review->negative_points_list)
                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Positive Points -->
                            @if($review->positive_points_list)
                                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
                                    <div class="bg-gradient-to-r from-green-600/20 to-green-500/20 border-b border-green-500/30 p-6">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-green-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <h3 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">What's Great</h3>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <ul class="space-y-4">
                                            @foreach($review->positive_points_list as $point)
                                                <li class="flex items-start group">
                                                    <div class="w-6 h-6 bg-green-500/20 rounded-full flex items-center justify-center mr-4 mt-0.5 flex-shrink-0 group-hover:bg-green-500/30 transition-colors">
                                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <span class="text-[#FFFFFF] font-['Inter'] leading-relaxed">{{ $point }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Negative Points -->
                            @if($review->negative_points_list)
                                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
                                    <div class="bg-gradient-to-r from-red-600/20 to-red-500/20 border-b border-red-500/30 p-6">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-red-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <h3 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Areas for Improvement</h3>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <ul class="space-y-4">
                                            @foreach($review->negative_points_list as $point)
                                                <li class="flex items-start group">
                                                    <div class="w-6 h-6 bg-red-500/20 rounded-full flex items-center justify-center mr-4 mt-0.5 flex-shrink-0 group-hover:bg-red-500/30 transition-colors">
                                                        <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <span class="text-[#FFFFFF] font-['Inter'] leading-relaxed">{{ $point }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Product Info Card -->
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden sticky top-8">
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <img src="{{ $review->product->image ?? 'https://via.placeholder.com/200x150/27272A/A1A1AA?text=No+Image' }}" 
                                     alt="{{ $review->product->name }}" 
                                     class="w-full max-w-48 mx-auto rounded-xl shadow-lg">
                            </div>
                            
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">{{ $review->product->name }}</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-['Share_Tech_Mono'] font-bold uppercase tracking-wider bg-[#E53E3E]/20 text-[#E53E3E] border border-[#E53E3E]/30">
                                    {{ ucfirst($review->product->type) }}
                                </span>
                            </div>

                            <div class="space-y-4 mb-6">
                                @if($review->product->genre)
                                    <div class="flex justify-between items-center">
                                        <span class="text-[#A1A1AA] font-['Inter']">Genre</span>
                                        <span class="text-white font-semibold font-['Inter']">{{ $review->product->genre->name }}</span>
                                    </div>
                                @endif
                                @if($review->product->platform)
                                    <div class="flex justify-between items-center">
                                        <span class="text-[#A1A1AA] font-['Inter']">Platform</span>
                                        <span class="text-white font-semibold font-['Inter']">{{ $review->product->platform->name }}</span>
                                    </div>
                                @endif
                                @if($review->platform_played_on)
                                    <div class="flex justify-between items-center">
                                        <span class="text-[#A1A1AA] font-['Inter']">Played On</span>
                                        @php
                                            $hardware = \App\Models\Product::whereIn('type', ['hardware', 'accessory'])->where('slug', $review->platform_played_on)->first();
                                        @endphp
                                        @if($hardware)
                                            <span class="inline-flex items-center text-white text-xs font-bold px-2 py-1 rounded-full uppercase tracking-wider font-['Share_Tech_Mono'] shadow-lg border border-white/20" style="background: linear-gradient(135deg, {{ $hardware->color }}, {{ $hardware->color }}dd);">
                                                {{ $hardware->name }}
                                            </span>
                                        @else
                                            <span class="text-white font-['Inter']">{{ $review->platform_played_on }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @if($review->product->type === 'game')
                                <a href="{{ route('games.show', $review->product) }}" 
                                   class="w-full inline-flex items-center justify-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white px-4 py-3 rounded-xl font-bold font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Game
                                </a>
                            @else
                                <a href="{{ route('tech.show', $review->product) }}" 
                                   class="w-full inline-flex items-center justify-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white px-4 py-3 rounded-xl font-bold font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Product
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Card -->
                    @auth
                        @if(Auth::id() === $review->user_id || Auth::user()->is_admin)
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                                <h3 class="text-lg font-bold text-white mb-4 font-['Share_Tech_Mono']">Review Actions</h3>
                                <div class="space-y-3">
                                    @php
                                        $editRoute = $review->product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit';
                                        $deleteRoute = $review->product->type === 'game' ? 'games.reviews.destroy' : 'tech.reviews.destroy';
                                    @endphp
                                    <a href="{{ route($editRoute, [$review->product, $review]) }}" 
                                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#2563EB] text-white rounded-xl hover:bg-blue-700 transition-colors font-['Inter'] font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Review
                                    </a>
                                    <form action="{{ route($deleteRoute, [$review->product, $review]) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this review?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-['Inter'] font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Review
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <!-- Report Card -->
                    @auth
                        @php
                            $userHasReported = \App\Models\Report::where('review_id', $review->id)
                                ->where('user_id', Auth::id())
                                ->exists();
                        @endphp
                        @if(!$userHasReported)
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                                <h3 class="text-lg font-bold text-white mb-4 font-['Share_Tech_Mono']">Report Review</h3>
                                <p class="text-[#A1A1AA] text-sm mb-4 font-['Inter']">
                                    Found something inappropriate? Help us maintain quality by reporting this review.
                                </p>
                                <button id="reportButton" 
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-colors font-['Inter'] font-semibold">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6v1a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    Report Review
                                </button>
                            </div>
                        @else
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                                <h3 class="text-lg font-bold text-white mb-4 font-['Share_Tech_Mono']">Report Submitted</h3>
                                <div class="flex items-center text-green-400">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-['Inter'] text-sm">You have reported this review</span>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-[#27272A] rounded-2xl max-w-md w-full mx-auto shadow-2xl border border-[#3F3F46]">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">Report Review</h3>
                        <button id="closeModal" class="text-[#A1A1AA] hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form id="reportForm" method="POST" action="{{ route($review->product->type === 'game' ? 'games.reviews.report.store' : 'tech.reviews.report.store', [$review->product, $review]) }}">
                        @csrf
                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-semibold text-white mb-3 font-['Inter']">Reason for Report</label>
                            <select id="reason" name="reason" required 
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']">
                                <option value="">Select a reason...</option>
                                @foreach(\App\Models\Report::getReasons() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label for="additional_info" class="block text-sm font-semibold text-white mb-3 font-['Inter']">Additional Information (Optional)</label>
                            <textarea id="additional_info" name="additional_info" rows="4" maxlength="1000"
                                      class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter'] resize-none"
                                      placeholder="Please provide any additional details about why you're reporting this review..."></textarea>
                            <div class="text-xs text-[#A1A1AA] mt-2 font-['Inter']">
                                <span id="charCount">0</span>/1000 characters
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" id="cancelReport" 
                                    class="flex-1 px-4 py-3 bg-[#3F3F46] text-white rounded-xl hover:bg-[#52525B] transition-colors font-['Inter'] font-semibold">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-colors font-['Inter'] font-semibold">
                                Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @auth
        @php
            $userHasReported = \App\Models\Report::where('review_id', $review->id)
                ->where('user_id', Auth::id())
                ->exists();
        @endphp
        @if(!$userHasReported)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const reportButton = document.getElementById('reportButton');
                    const reportModal = document.getElementById('reportModal');
                    const closeModal = document.getElementById('closeModal');
                    const cancelReport = document.getElementById('cancelReport');
                    const additionalInfo = document.getElementById('additional_info');
                    const charCount = document.getElementById('charCount');

                    // Open modal
                    reportButton.addEventListener('click', function() {
                        reportModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });

                    // Close modal functions
                    function closeReportModal() {
                        reportModal.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                        document.getElementById('reportForm').reset();
                        charCount.textContent = '0';
                    }

                    closeModal.addEventListener('click', closeReportModal);
                    cancelReport.addEventListener('click', closeReportModal);

                    // Close modal when clicking outside
                    reportModal.addEventListener('click', function(e) {
                        if (e.target === reportModal) {
                            closeReportModal();
                        }
                    });

                    // Character counter
                    additionalInfo.addEventListener('input', function() {
                        const count = this.value.length;
                        charCount.textContent = count;
                        
                        if (count > 950) {
                            charCount.classList.remove('text-[#A1A1AA]');
                            charCount.classList.add('text-orange-400');
                        } else {
                            charCount.classList.remove('text-orange-400');
                            charCount.classList.add('text-[#A1A1AA]');
                        }
                    });

                    // Close modal on escape key
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && !reportModal.classList.contains('hidden')) {
                            closeReportModal();
                        }
                    });
                });
            </script>
        @endif
    @endauth

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
</x-layouts.app> 