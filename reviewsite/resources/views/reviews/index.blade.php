<x-layouts.app>
    <div class="min-h-screen bg-gray-900">
        <!-- Header Section -->
        <div class="bg-gray-800 py-12">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-white mb-4">Game & Hardware Reviews</h1>
                <p class="text-gray-300 text-lg">Discover honest reviews from our expert team and gaming community</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="container mx-auto px-4 py-8">
            <form method="GET" action="{{ route('reviews.index') }}" class="bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search games and hardware..." 
                            value="{{ request('search') }}"
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                        >
                    </div>
                    
                    <!-- Type Filter -->
                    <div>
                        <select name="type" class="w-full bg-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                            <option value="">All Types</option>
                            <option value="game" {{ request('type') == 'game' ? 'selected' : '' }}>Games</option>
                            <option value="hardware" {{ request('type') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200">
                        Search
                    </button>
                </div>
            </form>

            <!-- Results Section -->
            <div class="space-y-6">
                @forelse($products as $product)
                    <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <img 
                                        src="{{ $product->image ?? 'https://via.placeholder.com/200x150/374151/9CA3AF?text=No+Image' }}" 
                                        alt="{{ $product->name }}"
                                        class="w-full md:w-48 h-36 object-cover rounded-lg"
                                    >
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                        <div class="flex-1">
                                            <h3 class="text-2xl font-bold text-white mb-2">{{ $product->name }}</h3>
                                            <div class="flex items-center gap-3 mb-3">
                                                <span class="inline-block bg-red-600 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide">
                                                    {{ $product->type }}
                                                </span>
                                            </div>
                                            <p class="text-gray-300 leading-relaxed">
                                                {{ Str::limit($product->description ?? 'No description available.', 200) }}
                                            </p>
                                        </div>
                                        
                                        <!-- Rating Section -->
                                        @if($product->staff_rating)
                                            <div class="flex-shrink-0 text-center">
                                                <div class="bg-red-600 text-white rounded-lg p-4">
                                                    <div class="text-3xl font-bold">{{ $product->staff_rating }}</div>
                                                    <div class="text-sm opacity-90">/ 10</div>
                                                </div>
                                                <div class="text-gray-400 text-sm mt-2">Staff Rating</div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="mt-6">
                                        <a 
                                            href="{{ route('reviews.show', $product) }}" 
                                            class="inline-flex items-center bg-gray-700 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg transition duration-200"
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
                    <div class="bg-gray-800 rounded-xl p-12 text-center">
                        <div class="text-gray-400 text-6xl mb-4">🎮</div>
                        <h3 class="text-xl font-semibold text-white mb-2">No Reviews Found</h3>
                        <p class="text-gray-400">Try adjusting your search criteria or check back later for new reviews.</p>
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