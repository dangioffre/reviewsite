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
            <div class="grid gap-8 mt-8">
                @forelse($products as $product)
                    <div class="group relative bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden hover:border-[#E53E3E] transition-all duration-500 hover:shadow-[0_20px_40px_rgba(229,62,62,0.15)] hover:-translate-y-1">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-5">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#E53E3E] rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-[#2563EB] rounded-full blur-2xl"></div>
                        </div>
                        
                        <div class="relative p-8">
                            <div class="flex flex-col lg:flex-row gap-8">
                                <!-- Enhanced Product Image -->
                                <div class="flex-shrink-0 relative">
                                    <div class="relative overflow-hidden rounded-xl border border-[#3F3F46] group-hover:border-[#E53E3E] transition-colors duration-300">
                                        <img 
                                            src="{{ $product->image ?? 'https://via.placeholder.com/280x200/27272A/A1A1AA?text=No+Image' }}" 
                                            alt="{{ $product->name }}"
                                            class="w-full lg:w-72 h-48 object-cover transform group-hover:scale-105 transition-transform duration-500"
                                        >
                                        <!-- Image Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        
                                        <!-- Quick Preview Badge -->
                                        <div class="absolute top-3 left-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <span class="bg-black/70 backdrop-blur text-white text-xs px-3 py-1 rounded-full font-['Inter'] font-medium">
                                                Quick Preview
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Enhanced Product Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                                        <div class="flex-1 min-w-0">
                                            <!-- Title with enhanced styling -->
                                            <div class="mb-4">
                                                <h3 class="text-3xl font-bold text-white mb-3 font-['Share_Tech_Mono'] leading-tight group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-[#E53E3E] group-hover:to-[#2563EB] transition-all duration-300">
                                                    {{ $product->name }}
                                                </h3>
                                                
                                                <!-- Enhanced Tags -->
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono'] shadow-lg">
                                                        <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ $product->type }}
                                                    </span>
                                                    @if($product->platform)
                                                        <span class="inline-flex items-center text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono'] shadow-lg border border-white/20" style="background: linear-gradient(135deg, {{ $product->platform->color }}, {{ $product->platform->color }}dd);">
                                                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8z" clip-rule="evenodd" />
                                                            </svg>
                                                            {{ $product->platform->name }}
                                                        </span>
                                                    @endif
                                                    @if($product->genre)
                                                        <span class="inline-flex items-center text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider font-['Share_Tech_Mono'] shadow-lg border border-white/20" style="background: linear-gradient(135deg, {{ $product->genre->color }}, {{ $product->genre->color }}dd);">
                                                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                            </svg>
                                                            {{ $product->genre->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Enhanced Description -->
                                            <div class="mb-6">
                                                <p class="text-[#A1A1AA] leading-relaxed font-['Inter'] text-lg">
                                                    {{ Str::limit($product->description ?? 'No description available.', 180) }}
                                                </p>
                                            </div>
                                            
                                            <!-- Enhanced Action Button -->
                                            <div class="flex items-center gap-4">
                                                <a 
                                                    href="{{ route('reviews.show', $product) }}" 
                                                    class="group/btn inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] hover:from-[#DC2626] hover:to-[#B91C1C] text-white px-8 py-3 rounded-xl font-bold font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300"
                                                >
                                                    <svg class="w-5 h-5 mr-2 transform group-hover/btn:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                    Read Full Review
                                                    <svg class="w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                                
                                                <!-- Share Button -->
                                                <button class="p-3 bg-[#27272A] hover:bg-[#3F3F46] border border-[#3F3F46] hover:border-[#E53E3E] rounded-xl transition-all duration-300 group/share">
                                                    <svg class="w-5 h-5 text-[#A1A1AA] group-hover/share:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Enhanced Rating Section -->
                                        @if($product->staff_rating)
                                            <div class="flex-shrink-0">
                                                <div class="relative">
                                                    <!-- Rating Circle with Gradient -->
                                                    <div class="relative w-24 h-24 mx-auto">
                                                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                                            <!-- Background Circle -->
                                                            <path class="text-[#3F3F46]" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                                            <!-- Progress Circle -->
                                                            <path class="text-[#E53E3E]" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" 
                                                                  stroke-dasharray="{{ $product->staff_rating * 10 }}, 100" 
                                                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831">
                                                            </path>
                                                        </svg>
                                                        <!-- Rating Number -->
                                                        <div class="absolute inset-0 flex items-center justify-center">
                                                            <div class="text-center">
                                                                <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $product->staff_rating }}</div>
                                                                <div class="text-xs text-[#A1A1AA] font-['Inter']">/ 10</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-center mt-3">
                                                        <div class="text-sm font-semibold text-white font-['Inter']">Staff Rating</div>
                                                        <!-- Star Rating -->
                                                        <div class="flex justify-center mt-1">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <svg class="w-3 h-3 {{ ($product->staff_rating/2) >= $i ? 'text-[#FFC107]' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="relative bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-16 text-center border border-[#3F3F46] shadow-2xl overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-5">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#E53E3E] rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-[#2563EB] rounded-full blur-2xl"></div>
                        </div>
                        
                        <div class="relative">
                            <div class="text-6xl mb-6 animate-bounce">🎮</div>
                            <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No Reviews Found</h3>
                            <p class="text-[#A1A1AA] font-['Inter'] text-lg max-w-md mx-auto">Try adjusting your search criteria or check back later for new reviews.</p>
                            <div class="mt-8">
                                <a href="{{ route('reviews.index') }}" class="inline-flex items-center bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white px-8 py-3 rounded-xl font-bold font-['Inter'] shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Clear All Filters
                                </a>
                            </div>
                        </div>
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