<x-layouts.app>
    <div class="min-h-screen bg-[#151515]">
        <!-- Header Section -->
        <div class="bg-[#27272A] py-12">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Game & Hardware Reviews</h1>
                <p class="text-[#A1A1AA] text-lg font-['Inter']">Discover honest reviews from our expert team and gaming community</p>
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