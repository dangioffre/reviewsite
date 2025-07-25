<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6 mb-8">
    <form method="GET" action="{{ route('podcasts.index') }}" class="space-y-6" id="podcast-filters-form">
        <!-- Search Bar -->
        <div>
            <label for="search" class="block text-sm font-medium text-white mb-2 font-['Inter']">Search Podcasts</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       id="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by name, description, host, or owner..."
                       class="block w-full pl-10 pr-3 py-3 border border-[#3F3F46] rounded-lg bg-[#1A1A1B] text-white placeholder-[#A1A1AA] focus:outline-none focus:ring-2 focus:ring-[#E53E3E] focus:border-transparent font-['Inter']">
            </div>
        </div>

        <!-- Filters Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Sort Dropdown -->
            <div>
                <label for="sort" class="block text-sm font-medium text-white mb-2 font-['Inter']">Sort By</label>
                <select name="sort" 
                        id="sort" 
                        class="block w-full px-3 py-3 border border-[#3F3F46] rounded-lg bg-[#1A1A1B] text-white focus:outline-none focus:ring-2 focus:ring-[#E53E3E] focus:border-transparent font-['Inter']">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="episodes_high" {{ request('sort') === 'episodes_high' ? 'selected' : '' }}>Most Episodes</option>
                    <option value="episodes_low" {{ request('sort') === 'episodes_low' ? 'selected' : '' }}>Least Episodes</option>
                    <option value="reviews_high" {{ request('sort') === 'reviews_high' ? 'selected' : '' }}>Most Reviews</option>
                    <option value="reviews_low" {{ request('sort') === 'reviews_low' ? 'selected' : '' }}>Least Reviews</option>
                </select>
            </div>

            <!-- Featured Filter -->
            <div>
                <label for="featured" class="block text-sm font-medium text-white mb-2 font-['Inter']">Featured Status</label>
                <select name="featured" 
                        id="featured" 
                        class="block w-full px-3 py-3 border border-[#3F3F46] rounded-lg bg-[#1A1A1B] text-white focus:outline-none focus:ring-2 focus:ring-[#E53E3E] focus:border-transparent font-['Inter']">
                    <option value="">All Podcasts</option>
                    <option value="true" {{ request('featured') === 'true' ? 'selected' : '' }}>Featured Only</option>
                    <option value="false" {{ request('featured') === 'false' ? 'selected' : '' }}>Non-Featured Only</option>
                </select>
            </div>

            <!-- Host Filter -->
            <div>
                <label for="host" class="block text-sm font-medium text-white mb-2 font-['Inter']">Filter by Host</label>
                <select name="host" 
                        id="host" 
                        class="block w-full px-3 py-3 border border-[#3F3F46] rounded-lg bg-[#1A1A1B] text-white focus:outline-none focus:ring-2 focus:ring-[#E53E3E] focus:border-transparent font-['Inter']">
                    <option value="">All Hosts</option>
                    @foreach($hosts as $host)
                        <option value="{{ $host }}" {{ request('host') === $host ? 'selected' : '' }}>{{ $host }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Apply Filters
            </button>
            
            @if(request()->hasAny(['search', 'sort', 'featured', 'host']))
                <a href="{{ route('podcasts.index') }}" 
                   class="flex-1 bg-gradient-to-r from-[#3F3F46] to-[#27272A] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#52525B] hover:to-[#3F3F46] transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear Filters
                </a>
            @endif
        </div>

        <!-- Active Filters Display -->
        @if(request()->hasAny(['search', 'sort', 'featured', 'host']))
            <div class="pt-4 border-t border-[#3F3F46]">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-[#A1A1AA] font-['Inter']">Active filters:</span>
                    
                    @if(request('search'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#E53E3E]/20 text-[#E53E3E] font-['Inter']">
                            Search: "{{ request('search') }}"
                        </span>
                    @endif
                    
                    @if(request('featured') === 'true')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400 font-['Inter']">
                            Featured Only
                        </span>
                    @elseif(request('featured') === 'false')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 font-['Inter']">
                            Non-Featured Only
                        </span>
                    @endif
                    
                    @if(request('host'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400 font-['Inter']">
                            Host: {{ request('host') }}
                        </span>
                    @endif
                    
                    @if(request('sort') && request('sort') !== 'newest')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 font-['Inter']">
                            Sorted: {{ ucfirst(str_replace('_', ' ', request('sort'))) }}
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('podcast-filters-form');
    const searchInput = document.getElementById('search');
    const sortSelect = document.getElementById('sort');
    const featuredSelect = document.getElementById('featured');
    const hostSelect = document.getElementById('host');
    
    let searchTimeout;
    
    // Auto-submit on dropdown changes
    [sortSelect, featuredSelect, hostSelect].forEach(element => {
        element.addEventListener('change', function() {
            form.submit();
        });
    });
    
    // Debounced search
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            form.submit();
        }, 500); // Wait 500ms after user stops typing
    });
    
    // Prevent form submission on Enter key in search (let the debounced search handle it)
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});
</script> 