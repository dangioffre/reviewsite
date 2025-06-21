<x-layouts.app>
    <div class="min-h-screen bg-[#151515]">
        <!-- Header Section -->
        <div class="bg-[#27272A] py-12">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Game & Hardware Reviews</h1>
                <p class="text-[#A1A1AA] text-lg font-['Inter']">Discover honest reviews from our expert team and gaming community</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="container mx-auto px-4 py-8">
            <form method="GET" action="{{ route('reviews.index') }}" class="bg-[#27272A] rounded-lg shadow-md p-6 mb-8 border border-[#3F3F46]">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search games and hardware..." 
                            value="{{ request('search') }}"
                            class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-2.5 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-[#2563EB] transition font-['Inter']"
                        >
                    </div>
                    
                    <!-- Type Filter -->
                    <div>
                        <select name="type" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-2.5 text-white focus:border-[#2563EB] focus:ring-[#2563EB] transition font-['Inter']">
                            <option value="">All Types</option>
                            <option value="game" {{ request('type') == 'game' ? 'selected' : '' }}>Games</option>
                            <option value="hardware" {{ request('type') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-semibold font-['Inter']">
                        Search
                    </button>
                </div>
            </form>

            <!-- Results Section -->
            <div class="space-y-6">
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