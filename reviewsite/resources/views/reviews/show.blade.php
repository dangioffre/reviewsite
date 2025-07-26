        <x-layouts.app>
            <div class="min-h-screen bg-[#151515]">
                

                <!-- Desktop Hero Section -->
                <div class="hidden lg:block relative h-96 overflow-hidden">
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
                                    <h1 class="text-4xl xl:text-5xl font-bold text-white mb-3 font-['Poppins'] leading-tight drop-shadow-sm break-words">
                                        {{ $review->title }}
                                    </h1>
                                    <div class="flex flex-row items-center gap-4 bg-[#18181B]/80 border border-[#232326] rounded-xl px-6 py-3 shadow-sm w-fit">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-[#232326] flex items-center justify-center text-white font-bold text-lg font-['Poppins']">
                                                {{ substr($review->user->name, 0, 1) }}
                                            </div>
                                            <span class="font-semibold text-white font-['Inter'] text-base">{{ $review->user->name }}</span>
                                            @if($review->is_staff_review)
                                                <span class="ml-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#DC2626]/20 text-[#DC2626] border border-[#DC2626]/30 font-['Poppins']">Staff Review</span>
                                            @endif
                                        </div>
                                        <span class="text-[#A0A0A0] font-['Inter'] text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4 text-[#A0A0A0]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $review->created_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Rating Circle -->
                                <div class="flex justify-end">
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
                                                <div class="text-sm text-[#A0A0A0] font-['Inter']">/ 10</div>
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

                    <!-- Mobile Main Content -->
            <div class="lg:hidden px-3 py-2 space-y-3">
                <!-- Mobile Game Info (Moved to top) -->
                <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                    <div class="flex items-center space-x-2 mb-2">
                        <img src="{{ $review->product->image ?? 'https://via.placeholder.com/40x30/27272A/A1A1AA?text=No+Image' }}" 
                             alt="{{ $review->product->name }}" 
                             class="w-10 h-7.5 rounded object-cover">
                        <div class="flex-1">
                            <h3 class="text-xs font-bold text-white break-words">{{ $review->product->name }}</h3>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-bold uppercase bg-[#DC2626]/20 text-[#DC2626] border border-[#DC2626]/30">
                                {{ ucfirst($review->product->type) }}
                            </span>
                        </div>
                    </div>
                    @if($review->product->type === 'game')
                        <a href="{{ route('games.show', $review->product) }}" 
                           class="w-full inline-flex items-center justify-center bg-[#DC2626] hover:bg-[#B91C1C] text-white px-2 py-1.5 rounded text-xs font-bold font-['Inter'] shadow-lg transition-all duration-200">
                            View Game
                        </a>
                    @else
                        <a href="{{ route('tech.show', $review->product) }}" 
                           class="w-full inline-flex items-center justify-center bg-[#DC2626] hover:bg-[#B91C1C] text-white px-2 py-1.5 rounded text-xs font-bold font-['Inter'] shadow-lg transition-all duration-200">
                            View Product
                        </a>
                    @endif
                </div>

                <!-- Mobile Title & Rating -->
                <div class="bg-[#18181B] rounded-lg p-3 border border-[#232326]">
                    <h1 class="text-base font-bold text-white mb-2 font-['Poppins'] break-words">{{ $review->title }}</h1>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-[#232326] flex items-center justify-center text-white font-bold text-xs font-['Poppins']">
                                {{ substr($review->user->name, 0, 1) }}
                            </div>
                            <span class="text-[#A0A0A0] text-xs">{{ $review->user->name }}</span>
                            <span class="text-[#A0A0A0] text-xs">{{ $review->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-6 h-6 bg-[#E53E3E] rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs">{{ $review->rating }}</span>
                            </div>
                            <span class="text-[#A0A0A0] text-xs">/10</span>
                        </div>
                    </div>
                </div>

            <!-- Mobile Review Content -->
            <div class="bg-[#18181B] rounded-lg p-3 border border-[#232326]">
                <div class="text-[#FFFFFF] font-['Inter'] text-xs leading-relaxed">
                                    @php
                                        $converter = new \League\CommonMark\CommonMarkConverter([
                                            'html_input' => 'escape',
                                            'allow_unsafe_links' => false,
                                        ]);
                                    @endphp
                                                                        {!! $converter->convert($review->content)->getContent() !!}
                                </div>
                                <!-- Mobile Like Button -->
                                <div class="mt-3 flex justify-end" x-data="likeReview(
                                    {{ $review->id }},
                                    '{{ $review->product->type === 'game' ? route('games.reviews.like', [$review->product, $review]) : route('tech.reviews.like', [$review->product, $review]) }}',
                                    {{ (auth()->check() && $review->isLikedBy(auth()->user())) ? 'true' : 'false' }},
                                    {{ $review->likes_count }},
                                    {{ auth()->check() ? 'true' : 'false' }}
                                )">
                                    <button @click.prevent="canLike ? toggleLike() : window.location.href='{{ route('login') }}'" :class="[liked ? 'bg-[#DC2626] text-white' : 'bg-[#232326] text-[#A0A0A0] hover:bg-[#292929] hover:text-white']" class="flex items-center gap-1 px-2 py-1 rounded-full shadow-lg transition-colors focus:outline-none cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 10v12" />
                                            <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a2 2 0 0 1 3 3.88Z" />
                                        </svg>
                                        <span class="font-semibold text-xs" x-text="count"></span>
                                    </button>
                                </div>
                            </div>

            <!-- Mobile Pros & Cons -->
            @if($review->positive_points_list || $review->negative_points_list)
                <div class="space-y-2">
                    <!-- Positive Points -->
                    @if($review->positive_points_list)
                        <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h3 class="text-xs font-bold text-white">What's Great</h3>
                            </div>
                            <ul class="space-y-1">
                                @foreach($review->positive_points_list as $point)
                                    <li class="flex items-start">
                                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5 mt-1 flex-shrink-0"></div>
                                        <span class="text-[#FFFFFF] font-['Inter'] text-xs leading-relaxed">{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Negative Points -->
                    @if($review->negative_points_list)
                        <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 bg-red-500 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h3 class="text-xs font-bold text-white">Areas for Improvement</h3>
                            </div>
                            <ul class="space-y-1">
                                @foreach($review->negative_points_list as $point)
                                    <li class="flex items-start">
                                        <div class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1.5 mt-1 flex-shrink-0"></div>
                                        <span class="text-[#FFFFFF] font-['Inter'] text-xs leading-relaxed">{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                            @endif

                <!-- Mobile Comments Section -->
                <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                    <h2 class="text-xs font-bold text-white mb-2 font-['Poppins']">Comments</h2>
                    @if(session('success'))
                        <div class="mb-2 text-green-500 text-xs font-semibold">{{ session('success') }}</div>
                    @endif
                    <!-- Comment Form -->
                    @auth
                    <form action="{{ $review->product->type === 'game' ? route('games.reviews.comments.store', [$review->product, $review]) : route('tech.reviews.comments.store', [$review->product, $review]) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="parent_id" id="parent_id" value="">
                        <textarea name="content" rows="2" class="w-full bg-[#232326] border border-[#292929] rounded-lg p-2 text-white font-['Inter'] text-xs focus:outline-none focus:ring-2 focus:ring-[#2563EB]" placeholder="Write a comment..." required></textarea>
                        <div class="flex justify-end mt-1">
                            <button type="submit" class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold px-3 py-1 rounded text-xs font-['Inter'] transition">Post</button>
                        </div>
                    </form>
                    @else
                    <div class="mb-2">
                        <a href="{{ route('login') }}" class="text-[#2563EB] hover:underline font-semibold text-xs">Log in to comment</a>
                    </div>
                    @endauth
                    <!-- Comments List -->
                    <div class="space-y-1">
                        @foreach($review->comments->where('parent_id', null) as $comment)
                            @include('reviews.partials.comment', ['comment' => $comment])
                        @endforeach
                    </div>
                </div>

                <!-- Mobile Other Reviews by This User -->
                @php
                    $otherReviews = \App\Models\Review::where('user_id', $review->user_id)
                        ->where('id', '!=', $review->id)
                        ->latest()->take(4)->get();
                    $user = $review->user;
                @endphp
                @if($otherReviews->count() > 0)
                <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                    <h3 class="text-xs font-bold text-white mb-2 font-['Poppins']">More by {{ $user->name }}</h3>
                    <div class="space-y-2">
                        @foreach($otherReviews as $other)
                            <a href="{{ route($other->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$other->product, $other]) }}" class="flex items-center gap-2 p-2 rounded hover:bg-[#232326] transition group">
                                <div class="w-6 h-6 bg-[#232326] rounded-full flex items-center justify-center font-bold text-white font-['Poppins'] text-xs">
                                    {{ substr($other->product->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-white font-['Inter'] text-xs truncate">{{ $other->product->name }}</div>
                                    <div class="text-[#A0A0A0] font-['Inter'] text-xs truncate">{{ $other->title }}</div>
                                </div>
                                <div class="text-yellow-400 font-bold font-['Poppins'] text-xs">{{ $other->rating }}/10</div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Mobile Podcast Section -->
                @php $podcast = $user->podcasts->first(); @endphp
                @if($podcast)
                <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326] flex flex-col items-center">
                    <h3 class="text-xs font-bold text-white mb-2 font-['Poppins']">Podcast</h3>
                    <img src="{{ $podcast->logo_url ?? 'https://via.placeholder.com/32x32/27272A/A1A1AA?text=Podcast' }}" alt="{{ $podcast->name }}" class="w-8 h-8 rounded object-cover mb-1">
                    <div class="font-bold text-white font-['Inter'] text-xs mb-1">{{ $podcast->name }}</div>
                    <a href="{{ route('podcasts.show', $podcast) }}" class="inline-block px-3 py-1 bg-[#2563EB] hover:bg-[#1D4ED8] text-white rounded text-xs font-semibold font-['Inter'] transition">View Podcast</a>
                </div>
                @endif

                <!-- Mobile Streamer Section -->
                @if($user->streamerProfile)
                <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326] flex flex-col items-center">
                    <h3 class="text-xs font-bold text-white mb-2 font-['Poppins']">Streamer</h3>
                    <img src="{{ $user->streamerProfile->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->streamerProfile->channel_name) . '&background=232326&color=fff&bold=true' }}" alt="{{ $user->streamerProfile->channel_name }}" class="w-8 h-8 rounded object-cover mb-1">
                    <div class="font-bold text-white font-['Inter'] text-xs mb-1">{{ $user->streamerProfile->channel_name }}</div>
                    <a href="{{ route('streamer.profile.show', $user->streamerProfile) }}" class="inline-block px-3 py-1 bg-[#4CAF50] hover:bg-[#388E3C] text-white rounded text-xs font-semibold font-['Inter'] transition">View Streamer</a>
                </div>
                @endif

                <!-- Mobile Review Actions -->
                @auth
                    @if(Auth::id() === $review->user_id || Auth::user()->is_admin)
                        <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                            <h3 class="text-xs font-bold text-white mb-2 font-['Poppins']">Review Actions</h3>
                            <div class="space-y-2">
                                @php
                                    $editRoute = $review->product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit';
                                    $deleteRoute = $review->product->type === 'game' ? 'games.reviews.destroy' : 'tech.reviews.destroy';
                                @endphp
                                <a href="{{ route($editRoute, [$review->product, $review]) }}" 
                                   class="w-full inline-flex items-center justify-center px-3 py-1 bg-[#2563EB] text-white rounded text-xs hover:bg-blue-700 transition-colors font-['Inter'] font-semibold">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Review
                                </a>
                                <form action="{{ route($deleteRoute, [$review->product, $review]) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full inline-flex items-center justify-center px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition-colors font-['Inter'] font-semibold">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete Review
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- Mobile Report Card -->
                @auth
                    @php
                        $userHasReported = \App\Models\Report::where('review_id', $review->id)
                            ->where('user_id', Auth::id())
                            ->exists();
                    @endphp
                    @if(!$userHasReported)
                        <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                            <h3 class="text-xs font-bold text-white mb-2 font-['Poppins']">Report Review</h3>
                            <p class="text-[#A1A1AA] text-xs mb-2 font-['Inter']">
                                Found something inappropriate? Help us maintain quality by reporting this review.
                            </p>
                            <button id="reportButton" 
                                    class="w-full inline-flex items-center justify-center px-3 py-1 bg-orange-600 text-white rounded text-xs hover:bg-orange-700 transition-colors font-['Inter'] font-semibold">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6v1a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Report Review
                            </button>
                        </div>
                    @else
                        <div class="bg-[#18181B] rounded-lg p-2 border border-[#232326]">
                            <h3 class="text-xs font-bold text-white mb-2 font-['Poppins']">Report Submitted</h3>
                            <div class="flex items-center text-green-400 text-xs">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Thank you for your report
                            </div>
                        </div>
                    @endif
                @endauth

            
        </div>

        <!-- Desktop Main Content -->
        <div class="hidden lg:block container mx-auto px-4 py-12">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Main Content Column -->
                <div class="lg:col-span-3 space-y-8 w-full">
                    <!-- Review Content -->
                    <article class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden relative">
                        <div class="p-8 xl:p-12">
                            <div class="prose prose-invert prose-lg">
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
                        <!-- Desktop Like Button -->
                        <div class="absolute bottom-4 right-6 z-10" x-data="likeReview(
                            {{ $review->id }},
                            '{{ $review->product->type === 'game' ? route('games.reviews.like', [$review->product, $review]) : route('tech.reviews.like', [$review->product, $review]) }}',
                            {{ (auth()->check() && $review->isLikedBy(auth()->user())) ? 'true' : 'false' }},
                            {{ $review->likes_count }},
                            {{ auth()->check() ? 'true' : 'false' }}
                        )">
                            <button @click.prevent="canLike ? toggleLike() : window.location.href='{{ route('login') }}'" :class="[liked ? 'bg-[#DC2626] text-white' : 'bg-[#232326] text-[#A0A0A0] hover:bg-[#292929] hover:text-white']" class="flex items-center gap-2 px-4 py-2 rounded-full shadow-lg transition-colors focus:outline-none cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 10v12" />
                                    <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a2 2 0 0 1 3 3.88Z" />
                                </svg>
                                <span class="font-semibold text-base" x-text="count"></span>
                            </button>
                        </div>
                    </article>

                    <!-- Desktop Pros & Cons -->
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
                                                    <span class="text-[#FFFFFF] font-['Inter'] text-base leading-relaxed">{{ $point }}</span>
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
                                                    <span class="text-[#FFFFFF] font-['Inter'] text-base leading-relaxed">{{ $point }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Desktop Comments Section -->
                    <section id="review-comments" class="mt-10">
                        <h2 class="text-2xl font-bold text-white mb-6 font-['Poppins']">Comments</h2>
                        @if(session('success'))
                            <div class="mb-4 text-green-500 font-semibold">{{ session('success') }}</div>
                        @endif
                        <!-- Comment Form -->
                        @auth
                        <form action="{{ $review->product->type === 'game' ? route('games.reviews.comments.store', [$review->product, $review]) : route('tech.reviews.comments.store', [$review->product, $review]) }}" method="POST" class="mb-8 bg-[#18181B] border border-[#232326] rounded-xl p-6">
                            @csrf
                            <input type="hidden" name="parent_id" id="parent_id" value="">
                            <textarea name="content" rows="3" class="w-full bg-[#232326] border border-[#292929] rounded-lg p-3 text-white font-['Inter'] focus:outline-none focus:ring-2 focus:ring-[#2563EB]" placeholder="Write a comment..." required></textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold px-6 py-2 rounded-lg font-['Inter'] transition">Post Comment</button>
                            </div>
                        </form>
                        @else
                        <div class="mb-8">
                            <a href="{{ route('login') }}" class="text-[#2563EB] hover:underline font-semibold">Log in to comment</a>
                        </div>
                        @endauth
                        <!-- Comments List -->
                        <div class="space-y-6">
                            @foreach($review->comments->where('parent_id', null) as $comment)
                                @include('reviews.partials.comment', ['comment' => $comment])
                            @endforeach
                        </div>
                    </section>
                </div>

                <!-- Desktop Sidebar -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Product Info Card -->
                    <div class="bg-[#18181B] border border-[#292929] rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <img src="{{ $review->product->image ?? 'https://via.placeholder.com/200x150/27272A/A1A1AA?text=No+Image' }}" 
                                     alt="{{ $review->product->name }}" 
                                     class="w-full max-w-48 mx-auto rounded-xl shadow-lg">
                            </div>
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-bold text-white mb-2 font-['Poppins'] break-words">{{ $review->product->name }}</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-['Poppins'] font-bold uppercase tracking-wider bg-[#DC2626]/20 text-[#DC2626] border border-[#DC2626]/30">
                                    {{ ucfirst($review->product->type) }}
                                </span>
                            </div>
                            <div class="space-y-4 mb-6">
                                @if($review->product->genre)
                                    <div class="flex justify-between items-center">
                                        <span class="text-[#A0A0A0] font-['Inter'] text-base">Genre</span>
                                        <span class="text-white font-semibold font-['Inter'] text-base break-words">{{ $review->product->genre->name }}</span>
                                    </div>
                                @endif
                                @if($review->product->platform)
                                    <div class="flex justify-between items-center">
                                        <span class="text-[#A0A0A0] font-['Inter'] text-base">Platform</span>
                                        <span class="text-white font-semibold font-['Inter'] text-base break-words">{{ $review->product->platform->name }}</span>
                                    </div>
                                @endif
                                @if($review->platform_played_on)
                                    <div class="flex justify-between items-center">
                                        <span class="text-[#A0A0A0] font-['Inter'] text-base">Played On</span>
                                        @php
                                            $hardware = \App\Models\Product::whereIn('type', ['hardware', 'accessory'])->where('slug', $review->platform_played_on)->first();
                                        @endphp
                                        @if($hardware)
                                            <span class="inline-flex items-center text-white text-xs font-bold px-2 py-1 rounded-full uppercase tracking-wider font-['Poppins'] shadow-lg border border-white/20 break-words" style="background: linear-gradient(135deg, {{ $hardware->color }}, {{ $hardware->color }}dd);">
                                                {{ $hardware->name }}
                                            </span>
                                        @else
                                            <span class="text-white font-['Inter'] text-base break-words">{{ $review->platform_played_on }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @if($review->product->type === 'game')
                                <a href="{{ route('games.show', $review->product) }}" 
                                   class="w-full inline-flex items-center justify-center bg-[#DC2626] hover:bg-[#B91C1C] text-white px-4 py-3 rounded-xl font-bold font-['Inter'] shadow-lg transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Game
                                </a>
                            @else
                                <a href="{{ route('tech.show', $review->product) }}" 
                                   class="w-full inline-flex items-center justify-center bg-[#DC2626] hover:bg-[#B91C1C] text-white px-4 py-3 rounded-xl font-bold font-['Inter'] shadow-lg transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Product
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Other Reviews by This User -->
                    @php
                        $otherReviews = \App\Models\Review::where('user_id', $review->user_id)
                            ->where('id', '!=', $review->id)
                            ->latest()->take(4)->get();
                        $user = $review->user;
                    @endphp
                    @if($otherReviews->count() > 0)
                    <div class="bg-[#18181B] border border-[#292929] rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4 font-['Poppins']">More by {{ $user->name }}</h3>
                        <div class="space-y-4">
                            @foreach($otherReviews as $other)
                                <a href="{{ route($other->product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$other->product, $other]) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#232326] transition group border border-transparent hover:border-[#292929]">
                                    <div class="w-10 h-10 bg-[#232326] rounded-full flex items-center justify-center font-bold text-white font-['Poppins'] text-lg">
                                        {{ substr($other->product->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-white font-['Inter'] truncate">{{ $other->product->name }}</div>
                                        <div class="text-xs text-[#A0A0A0] font-['Inter'] truncate">{{ $other->title }}</div>
                                    </div>
                                    <div class="text-yellow-400 font-bold font-['Poppins']">{{ $other->rating }}/10</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Podcast Section -->
                    @php $podcast = $user->podcasts->first(); @endphp
                    @if($podcast)
                    <div class="bg-[#18181B] border border-[#292929] rounded-2xl shadow-xl p-6 flex flex-col items-center">
                        <h3 class="text-lg font-bold text-white mb-4 font-['Poppins']">Podcast</h3>
                        <img src="{{ $podcast->logo_url ?? 'https://via.placeholder.com/64x64/27272A/A1A1AA?text=Podcast' }}" alt="{{ $podcast->name }}" class="w-16 h-16 rounded-lg object-cover mb-2">
                        <div class="font-bold text-white font-['Inter'] mb-1">{{ $podcast->name }}</div>
                        <a href="{{ route('podcasts.show', $podcast) }}" class="inline-block mt-2 px-4 py-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white rounded-lg font-semibold font-['Inter'] transition">View Podcast</a>
                    </div>
                    @endif

                    <!-- Streamer Section -->
                    @if($user->streamerProfile)
                    <div class="bg-[#18181B] border border-[#292929] rounded-2xl shadow-xl p-6 flex flex-col items-center">
                        <h3 class="text-lg font-bold text-white mb-4 font-['Poppins']">Streamer</h3>
                        <img src="{{ $user->streamerProfile->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->streamerProfile->channel_name) . '&background=232326&color=fff&bold=true' }}" alt="{{ $user->streamerProfile->channel_name }}" class="w-16 h-16 rounded-lg object-cover mb-2">
                        <div class="font-bold text-white font-['Inter'] mb-1">{{ $user->streamerProfile->channel_name }}</div>
                        <a href="{{ route('streamer.profile.show', $user->streamerProfile) }}" class="inline-block mt-2 px-4 py-2 bg-[#4CAF50] hover:bg-[#388E3C] text-white rounded-lg font-semibold font-['Inter'] transition">View Streamer</a>
                    </div>
                    @endif

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
