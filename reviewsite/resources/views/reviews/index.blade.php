<x-layouts.app>
    <div class="min-h-screen bg-[#151515]">
        <!-- Enhanced Header Section -->
        <div class="relative bg-gradient-to-br from-[#27272A] via-[#1A1A1B] to-[#151515] py-16 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, #E53E3E 0%, transparent 50%), radial-gradient(circle at 75% 75%, #2563EB 0%, transparent 50%)"></div>
                <div class="absolute top-0 left-0 w-full h-full" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(255,255,255,0.03) 2px, rgba(255,255,255,0.03) 4px)"></div>
            </div>
            
            <!-- Floating Elements -->
            <div class="absolute top-10 right-10 w-20 h-20 bg-[#E53E3E] opacity-20 rounded-full blur-xl"></div>
            <div class="absolute bottom-10 left-10 w-16 h-16 bg-[#2563EB] opacity-20 rounded-full blur-xl"></div>
            
            <div class="container mx-auto px-4 relative z-10">
                <div class="max-w-6xl mx-auto">
                    <!-- Breadcrumb -->
                    <nav class="mb-8">
                        <div class="flex items-center gap-2 text-sm font-['Inter']">
                            <a href="{{ route('home') }}" class="text-[#A1A1AA] hover:text-white transition-colors">Home</a>
                            <svg class="w-4 h-4 text-[#3F3F46]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-[#E53E3E] font-medium">Reviews</span>
                        </div>
                    </nav>

                    <div class="grid lg:grid-cols-3 gap-12 items-center">
                        <!-- Main Content -->
                        <div class="lg:col-span-2">
                            <div class="mb-6">
                                <span class="inline-block bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white text-sm font-['Share_Tech_Mono'] px-4 py-2 rounded-full uppercase tracking-wider mb-4">
                                    COMPREHENSIVE REVIEWS
                                </span>
                                <h1 class="text-5xl lg:text-6xl font-bold text-white mb-6 font-['Share_Tech_Mono'] leading-tight">
                                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#E53E3E] to-[#2563EB]">
                                        Reviews
                                    </span>
                                </h1>
                                <p class="text-xl text-[#A1A1AA] leading-relaxed font-['Inter'] max-w-2xl">
                                    Discover honest, in-depth reviews from our team and passionate gaming community. 
                                    From the latest AAA titles to cutting-edge hardware.
                                </p>
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex flex-wrap gap-4">
                                <a href="#trending" class="bg-[#E53E3E] hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors font-['Inter'] flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    Trending Now
                                </a>
                            </div>
                        </div>

                        <!-- Stats Panel -->
                        <div class="lg:col-span-1">
                            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 shadow-2xl">
                                <h3 class="text-lg font-bold text-white mb-6 font-['Share_Tech_Mono'] uppercase tracking-wider">
                                    Review Stats
                                </h3>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $products->total() ?? 0 }}</div>
                                            <div class="text-sm text-[#A1A1AA] font-['Inter']">Total Reviews</div>
                                        </div>
                                        <div class="w-12 h-12 bg-[#E53E3E] bg-opacity-20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-[#E53E3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $genres->count() }}</div>
                                            <div class="text-sm text-[#A1A1AA] font-['Inter']">Genres Covered</div>
                                        </div>
                                        <div class="w-12 h-12 bg-[#2563EB] bg-opacity-20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $platforms->count() }}</div>
                                            <div class="text-sm text-[#A1A1AA] font-['Inter']">Platforms</div>
                                        </div>
                                        <div class="w-12 h-12 bg-[#4CAF50] bg-opacity-20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-[#4CAF50]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>

                                    @php
                                        $avgRating = $products->avg('staff_rating') ?? 0;
                                    @endphp
                                    <div class="pt-4 border-t border-[#3F3F46]">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ number_format($avgRating, 1) }}</div>
                                                <div class="text-sm text-[#A1A1AA] font-['Inter']">Average Rating</div>
                                            </div>
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ ($avgRating/2) >= $i ? 'text-[#FFC107]' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="container mx-auto px-4 py-8">
            <form method="GET" action="{{ route('reviews.index') }}" class="bg-[#27272A] rounded-lg shadow-md border border-[#3F3F46] overflow-hidden">
                <!-- Category Tabs -->
                <div class="border-b border-[#3F3F46]">
                    <div class="flex overflow-x-auto">
                        <button type="submit" name="category" value="" 
                                class="flex-shrink-0 px-6 py-4 text-sm font-['Inter'] font-medium transition-colors
                                       {{ request('category') == '' ? 'bg-white text-[#151515] border-b-2 border-white' : 'text-[#A1A1AA] hover:text-white hover:bg-[#3F3F46]' }}">
                            All
                        </button>
                        <button type="submit" name="category" value="games" 
                                class="flex-shrink-0 px-6 py-4 text-sm font-['Inter'] font-medium transition-colors
                                       {{ request('category') == 'games' ? 'bg-white text-[#151515] border-b-2 border-white' : 'text-[#A1A1AA] hover:text-white hover:bg-[#3F3F46]' }}">
                            Games
                        </button>
                        <button type="submit" name="category" value="hardware" 
                                class="flex-shrink-0 px-6 py-4 text-sm font-['Inter'] font-medium transition-colors
                                       {{ request('category') == 'hardware' ? 'bg-white text-[#151515] border-b-2 border-white' : 'text-[#A1A1AA] hover:text-white hover:bg-[#3F3F46]' }}">
                            Hardware
                        </button>
                        <button type="submit" name="category" value="accessories" 
                                class="flex-shrink-0 px-6 py-4 text-sm font-['Inter'] font-medium transition-colors
                                       {{ request('category') == 'accessories' ? 'bg-white text-[#151515] border-b-2 border-white' : 'text-[#A1A1AA] hover:text-white hover:bg-[#3F3F46]' }}">
                            Accessories
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="p-6">
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        <!-- Dropdown Filters -->
                        <div class="flex flex-wrap gap-4">
                            <!-- Sort By -->
                            <select name="sort" class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-2 text-white text-sm font-['Inter'] focus:border-[#2563EB] focus:ring-[#2563EB] min-w-[140px]">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Sort by Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Sort by Oldest</option>
                                <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Highest Rated</option>
                                <option value="rating_low" {{ request('sort') == 'rating_low' ? 'selected' : '' }}>Lowest Rated</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                            </select>

                            <!-- Score Range -->
                            <select name="score_range" class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-2 text-white text-sm font-['Inter'] focus:border-[#2563EB] focus:ring-[#2563EB] min-w-[120px]">
                                <option value="">All Scores</option>
                                <option value="9-10" {{ request('score_range') == '9-10' ? 'selected' : '' }}>9-10 (Excellent)</option>
                                <option value="7-8" {{ request('score_range') == '7-8' ? 'selected' : '' }}>7-8 (Great)</option>
                                <option value="5-6" {{ request('score_range') == '5-6' ? 'selected' : '' }}>5-6 (Good)</option>
                                <option value="3-4" {{ request('score_range') == '3-4' ? 'selected' : '' }}>3-4 (Fair)</option>
                                <option value="1-2" {{ request('score_range') == '1-2' ? 'selected' : '' }}>1-2 (Poor)</option>
                            </select>

                            <!-- Platform Filter (for games) -->
                            <select name="platform" class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-2 text-white text-sm font-['Inter'] focus:border-[#2563EB] focus:ring-[#2563EB] min-w-[130px]">
                                <option value="">All Platforms</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->slug }}" {{ request('platform') == $platform->slug ? 'selected' : '' }}>
                                        {{ $platform->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Genre Filter -->
                            <select name="genre" class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-4 py-2 text-white text-sm font-['Inter'] focus:border-[#2563EB] focus:ring-[#2563EB] min-w-[120px]">
                                <option value="">All Genres</option>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->slug }}" {{ request('genre') == $genre->slug ? 'selected' : '' }}>
                                        {{ $genre->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search games, hardware, and reviews..." 
                                value="{{ request('search') }}"
                                class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-2.5 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-[#2563EB] transition font-['Inter']"
                            >
                        </div>
                        <button type="submit" class="bg-[#E53E3E] text-white px-6 py-2.5 rounded-lg hover:bg-red-700 transition-colors font-semibold font-['Inter'] flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search
                        </button>
                        @if(request()->hasAny(['search', 'category', 'sort', 'score_range', 'platform', 'genre']))
                            <a href="{{ route('reviews.index') }}" class="bg-[#27272A] text-white px-4 py-2.5 rounded-lg border border-[#E53E3E] hover:bg-red-900/50 transition-colors font-['Inter'] flex items-center">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Active Filters Display -->
            @if(request()->hasAny(['search', 'category', 'sort', 'score_range', 'platform', 'genre']))
                <div class="mb-6 mt-4">
                    <div class="flex flex-wrap gap-2">
                        <span class="text-sm text-[#A1A1AA] font-['Inter'] mr-2">Active filters:</span>
                        @if(request('search'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-['Share_Tech_Mono'] bg-[#E53E3E] text-white">
                                Search: {{ request('search') }}
                            </span>
                        @endif
                        @if(request('category'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-['Share_Tech_Mono'] bg-[#2563EB] text-white">
                                {{ ucfirst(request('category')) }}
                            </span>
                        @endif
                        @if(request('score_range'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-['Share_Tech_Mono'] bg-[#4CAF50] text-white">
                                Score: {{ request('score_range') }}
                            </span>
                        @endif
                        @if(request('platform'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-['Share_Tech_Mono'] bg-[#FFC107] text-[#151515]">
                                {{ ucfirst(request('platform')) }}
                            </span>
                        @endif
                        @if(request('genre'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-['Share_Tech_Mono'] bg-[#27272A] text-white border border-[#3F3F46]">
                                {{ ucfirst(request('genre')) }}
                            </span>
                        @endif

                    </div>
                </div>
            @endif

            <!-- Results Section -->
            <div class="space-y-6 mt-8">
                @forelse($products as $product)
                    <div class="bg-[#27272A] rounded-lg shadow-md p-4 border border-[#3F3F46] hover:border-[#E53E3E] transition-colors duration-300">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <img 
                                        src="{{ $product->image ?? 'https://via.placeholder.com/200x150/27272A/A1A1AA?text=No+Image' }}" 
                                        alt="{{ $product->name }}"
                                        class="w-full md:w-48 h-36 object-cover rounded-lg border border-[#3F3F46]"
                                    >
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                        <div class="flex-1">
                                            <h3 class="text-2xl font-bold text-white mb-2 font-['Share_Tech_Mono']">{{ $product->name }}</h3>
                                            <div class="flex items-center gap-3 mb-3">
                                                <span class="inline-block bg-[#E53E3E] text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide font-['Share_Tech_Mono']">
                                                    {{ $product->type }}
                                                </span>
                                                @if($product->platform)
                                                    <span class="inline-block text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide font-['Share_Tech_Mono']" style="background-color: {{ $product->platform->color }}">
                                                        {{ $product->platform->name }}
                                                    </span>
                                                @endif
                                                @if($product->genre)
                                                    <span class="inline-block text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide font-['Share_Tech_Mono']" style="background-color: {{ $product->genre->color }}">
                                                        {{ $product->genre->name }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[#A1A1AA] leading-relaxed font-['Inter']">
                                                {{ Str::limit($product->description ?? 'No description available.', 200) }}
                                            </p>
                                        </div>
                                        
                                        <!-- Rating Section -->
                                        @if($product->staff_rating)
                                            <div class="flex-shrink-0 text-center">
                                                <div class="bg-[#E53E3E] text-white rounded-lg p-4">
                                                    <div class="text-3xl font-bold font-['Share_Tech_Mono']">{{ $product->staff_rating }}</div>
                                                    <div class="text-sm opacity-90 font-['Inter']">/ 10</div>
                                                </div>
                                                <div class="text-[#A1A1AA] text-sm mt-2 font-['Inter']">Staff Rating</div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="mt-6">
                                        <a 
                                            href="{{ route('reviews.show', $product) }}" 
                                            class="inline-flex items-center bg-[#27272A] text-white px-4 py-2 rounded-lg border border-[#E53E3E] hover:bg-red-900/50 transition-colors font-semibold font-['Inter']"
                                        >
                                            Read Full Review
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-[#27272A] rounded-lg p-12 text-center border border-[#3F3F46] shadow-md">
                        <div class="text-[#A1A1AA] text-6xl mb-4">🎮</div>
                        <h3 class="text-xl font-semibold text-white mb-2 font-['Share_Tech_Mono']">No Reviews Found</h3>
                        <p class="text-[#A1A1AA] font-['Inter']">Try adjusting your search criteria or check back later for new reviews.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-12">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app> 