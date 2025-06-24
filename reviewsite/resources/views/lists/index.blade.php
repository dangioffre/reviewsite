@section('title', 'Discover Game Lists - Browse Curated Collections')
@section('description', 'Explore thousands of curated game lists created by our community. Search by name, genre, platform, game mode, and more to find the perfect gaming collection.')

<x-layouts.app>
<div class="min-h-screen bg-gradient-to-br from-[#0F0F0F] via-[#1A1A1A] to-[#0F0F0F]">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-5xl lg:text-7xl font-bold text-white mb-4 font-['Share_Tech_Mono']">
                Discover Lists
            </h1>
            <p class="text-xl text-[#A1A1AA] font-['Inter'] max-w-3xl mx-auto">
                Explore curated game collections from our community. Find lists by name, games, genres, platforms, and more.
            </p>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-8 mb-12">
            <form method="GET" action="{{ route('lists.index') }}" class="space-y-6">
                <!-- Main Search Bar -->
                <x-search-input 
                    name="search" 
                    :value="request('search')" 
                    placeholder="Search lists by name or description..."
                    class="rounded-xl py-4 text-lg" />

                <!-- Advanced Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Category</label>
                        <select name="category" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                            <option value="all">All Categories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Genre Filter -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Genre</label>
                        <select name="genre" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                            <option value="">All Genres</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->slug }}" {{ request('genre') == $genre->slug ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Platform Filter -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Platform</label>
                        <select name="platform" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                            <option value="">All Platforms</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->slug }}" {{ request('platform') == $platform->slug ? 'selected' : '' }}>
                                    {{ $platform->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Publisher Filter -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Publisher</label>
                        <select name="publisher" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                            <option value="">All Publishers</option>
                            @foreach($publishers as $publisher)
                                <option value="{{ $publisher->slug }}" {{ request('publisher') == $publisher->slug ? 'selected' : '' }}>
                                    {{ $publisher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Developer Filter -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Developer</label>
                        <select name="developer" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                            <option value="">All Developers</option>
                            @foreach($developers as $developer)
                                <option value="{{ $developer->slug }}" {{ request('developer') == $developer->slug ? 'selected' : '' }}>
                                    {{ $developer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Game Mode Filter -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Game Mode</label>
                        <select name="game_mode" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                            <option value="">All Game Modes</option>
                            @foreach($gameModes as $mode)
                                <option value="{{ $mode->slug }}" {{ request('game_mode') == $mode->slug ? 'selected' : '' }}>
                                    {{ $mode->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Game Search with Autocomplete -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Contains Game</label>
                        <input type="text" 
                               id="game-search"
                               name="game" 
                               value="{{ request('game') }}" 
                               placeholder="Search by game name..."
                               autocomplete="off"
                               class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                        <div id="game-suggestions" class="absolute z-50 w-full bg-[#18181B] border border-[#3F3F46] rounded-lg mt-1 max-h-48 overflow-y-auto hidden"></div>
                    </div>

                    <!-- User Search with Autocomplete -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Created By</label>
                        <input type="text" 
                               id="user-search"
                               name="user" 
                               value="{{ request('user') }}" 
                               placeholder="Search by username..."
                               autocomplete="off"
                               class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                        <div id="user-suggestions" class="absolute z-50 w-full bg-[#18181B] border border-[#3F3F46] rounded-lg mt-1 max-h-48 overflow-y-auto hidden"></div>
                    </div>

                    <!-- Sort Options -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Sort By</label>
                        <select name="sort" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest</option>
                            <option value="updated_at" {{ request('sort') == 'updated_at' ? 'selected' : '' }}>Recently Updated</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="items_count" {{ request('sort') == 'items_count' ? 'selected' : '' }}>Number of Games</option>
                            <option value="followers_count" {{ request('sort') == 'followers_count' ? 'selected' : '' }}>Most Followed</option>
                            <option value="comments_count" {{ request('sort') == 'comments_count' ? 'selected' : '' }}>Most Discussed</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between">
                    <div class="flex gap-3">
                        <button type="submit" class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search Lists
                        </button>
                        <a href="{{ route('lists.index') }}" class="bg-[#374151] hover:bg-[#4B5563] text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Clear Filters
                        </a>
                    </div>
                    
                    @auth
                        <a href="{{ route('dashboard.lists') }}" class="bg-gradient-to-r from-[#059669] to-[#10B981] hover:from-[#047857] hover:to-[#059669] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create New List
                        </a>
                    @endauth
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">
                    @if(request()->hasAny(['search', 'category', 'genre', 'platform', 'publisher', 'developer', 'game_mode', 'game', 'user']))
                        Search Results
                    @else
                        All Public Lists
                    @endif
                    <span class="text-[#A1A1AA] text-lg ml-2">({{ $lists->total() }} {{ Str::plural('list', $lists->total()) }})</span>
                </h2>
                
                <!-- Results Info -->
                @if($lists->total() > 0)
                    <div class="text-[#A1A1AA] text-sm font-['Inter']">
                        Showing {{ $lists->firstItem() }}-{{ $lists->lastItem() }} of {{ $lists->total() }} results
                    </div>
                @endif
            </div>

            <!-- Active Filters Display -->
            @if(request()->hasAny(['search', 'category', 'genre', 'platform', 'publisher', 'developer', 'game_mode', 'game', 'user']))
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-[#A1A1AA] mb-3 font-['Inter']">Active Filters:</h3>
                    <div class="flex flex-wrap gap-2">
                        @if(request('search'))
                            <span class="inline-flex items-center bg-[#2563EB] text-white px-3 py-1 rounded-full text-sm">
                                Search: "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                        @if(request('category') && request('category') !== 'all')
                            <span class="inline-flex items-center bg-[#059669] text-white px-3 py-1 rounded-full text-sm">
                                Category: {{ $categories[request('category')] }}
                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                        @if(request('genre'))
                            <span class="inline-flex items-center bg-[#7C3AED] text-white px-3 py-1 rounded-full text-sm">
                                Genre: {{ $genres->where('slug', request('genre'))->first()?->name }}
                                <a href="{{ request()->fullUrlWithQuery(['genre' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                        @if(request('platform'))
                            <span class="inline-flex items-center bg-[#EA580C] text-white px-3 py-1 rounded-full text-sm">
                                Platform: {{ $platforms->where('slug', request('platform'))->first()?->name }}
                                <a href="{{ request()->fullUrlWithQuery(['platform' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                        @if(request('publisher'))
                            <span class="inline-flex items-center bg-[#F59E0B] text-white px-3 py-1 rounded-full text-sm">
                                Publisher: {{ $publishers->where('slug', request('publisher'))->first()?->name }}
                                <a href="{{ request()->fullUrlWithQuery(['publisher' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                        @if(request('developer'))
                            <span class="inline-flex items-center bg-[#10B981] text-white px-3 py-1 rounded-full text-sm">
                                Developer: {{ $developers->where('slug', request('developer'))->first()?->name }}
                                <a href="{{ request()->fullUrlWithQuery(['developer' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                        @if(request('game'))
                            <span class="inline-flex items-center bg-[#DC2626] text-white px-3 py-1 rounded-full text-sm">
                                Game: "{{ request('game') }}"
                                <a href="{{ request()->fullUrlWithQuery(['game' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                        @if(request('user'))
                            <span class="inline-flex items-center bg-[#9333EA] text-white px-3 py-1 rounded-full text-sm">
                                User: "{{ request('user') }}"
                                <a href="{{ request()->fullUrlWithQuery(['user' => null]) }}" class="ml-1 hover:text-gray-300">×</a>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Lists Grid -->
        @if($lists->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                @foreach($lists as $list)
                    <x-list-card :list="$list" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $lists->links('pagination::tailwind') }}
            </div>
        @else
            <!-- No Results State -->
            <div class="text-center py-16">
                <svg class="w-24 h-24 mx-auto mb-6 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No Lists Found</h3>
                <p class="text-[#A1A1AA] mb-8 font-['Inter'] max-w-md mx-auto">
                    @if(request()->hasAny(['search', 'category', 'genre', 'platform', 'publisher', 'developer', 'game_mode', 'game', 'user']))
                        No lists match your search criteria. Try adjusting your filters or search terms.
                    @else
                        There are no public lists available yet. Be the first to create and share a list!
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if(request()->hasAny(['search', 'category', 'genre', 'platform', 'publisher', 'developer', 'game_mode', 'game', 'user']))
                        <a href="{{ route('lists.index') }}" class="bg-[#374151] hover:bg-[#4B5563] text-white px-8 py-3 rounded-xl font-semibold transition-colors">
                            Clear All Filters
                        </a>
                    @endif
                    @auth
                        <a href="{{ route('dashboard.lists') }}" class="bg-gradient-to-r from-[#059669] to-[#10B981] hover:from-[#047857] hover:to-[#059669] text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200">
                            Create Your First List
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-gradient-to-r from-[#7C3AED] to-[#6D28D9] hover:from-[#6D28D9] hover:to-[#5B21B6] text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200">
                            Sign In to Create Lists
                        </a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Game autocomplete functionality
    const gameInput = document.getElementById('game-search');
    const gameSuggestions = document.getElementById('game-suggestions');
    let gameTimeout;

    gameInput.addEventListener('input', function() {
        clearTimeout(gameTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            gameSuggestions.classList.add('hidden');
            return;
        }
        
        gameTimeout = setTimeout(() => {
            fetch(`/api/search/games?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(games => {
                    gameSuggestions.innerHTML = '';
                    
                    if (games.length === 0) {
                        gameSuggestions.classList.add('hidden');
                        return;
                    }
                    
                    games.forEach(game => {
                        const suggestion = document.createElement('div');
                        suggestion.className = 'px-3 py-2 text-white hover:bg-[#2563EB] cursor-pointer border-b border-[#3F3F46] last:border-b-0';
                        suggestion.textContent = game.name;
                        suggestion.addEventListener('click', () => {
                            gameInput.value = game.name;
                            gameSuggestions.classList.add('hidden');
                        });
                        gameSuggestions.appendChild(suggestion);
                    });
                    
                    gameSuggestions.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching game suggestions:', error);
                    gameSuggestions.classList.add('hidden');
                });
        }, 300);
    });

    // User autocomplete functionality
    const userInput = document.getElementById('user-search');
    const userSuggestions = document.getElementById('user-suggestions');
    let userTimeout;

    userInput.addEventListener('input', function() {
        clearTimeout(userTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            userSuggestions.classList.add('hidden');
            return;
        }
        
        userTimeout = setTimeout(() => {
            fetch(`/api/search/users?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(users => {
                    userSuggestions.innerHTML = '';
                    
                    if (users.length === 0) {
                        userSuggestions.classList.add('hidden');
                        return;
                    }
                    
                    users.forEach(user => {
                        const suggestion = document.createElement('div');
                        suggestion.className = 'px-3 py-2 text-white hover:bg-[#2563EB] cursor-pointer border-b border-[#3F3F46] last:border-b-0';
                        suggestion.textContent = user.name;
                        suggestion.addEventListener('click', () => {
                            userInput.value = user.name;
                            userSuggestions.classList.add('hidden');
                        });
                        userSuggestions.appendChild(suggestion);
                    });
                    
                    userSuggestions.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching user suggestions:', error);
                    userSuggestions.classList.add('hidden');
                });
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!gameInput.contains(e.target) && !gameSuggestions.contains(e.target)) {
            gameSuggestions.classList.add('hidden');
        }
        if (!userInput.contains(e.target) && !userSuggestions.contains(e.target)) {
            userSuggestions.classList.add('hidden');
        }
    });

    // Handle keyboard navigation
    function handleKeyNavigation(input, suggestions) {
        input.addEventListener('keydown', function(e) {
            const items = suggestions.querySelectorAll('div');
            const currentActive = suggestions.querySelector('.bg-[#2563EB]');
            let activeIndex = -1;
            
            if (currentActive) {
                activeIndex = Array.from(items).indexOf(currentActive);
            }
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = activeIndex < items.length - 1 ? activeIndex + 1 : 0;
                setActiveItem(items, nextIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = activeIndex > 0 ? activeIndex - 1 : items.length - 1;
                setActiveItem(items, prevIndex);
            } else if (e.key === 'Enter' && currentActive) {
                e.preventDefault();
                currentActive.click();
            } else if (e.key === 'Escape') {
                suggestions.classList.add('hidden');
            }
        });
    }
    
    function setActiveItem(items, index) {
        items.forEach((item, i) => {
            if (i === index) {
                item.classList.add('bg-[#2563EB]');
            } else {
                item.classList.remove('bg-[#2563EB]');
            }
        });
    }
    
    handleKeyNavigation(gameInput, gameSuggestions);
    handleKeyNavigation(userInput, userSuggestions);
});
</script>
</x-layouts.app> 