<x-layouts.app>
<div class="min-h-screen bg-gradient-to-br from-[#0F0F0F] via-[#1A1A1A] to-[#0F0F0F] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white font-['Share_Tech_Mono'] mb-2">Game Collection</h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">Your personal game library and detailed tracking</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-400 hover:text-blue-300 hover:underline transition-colors font-['Inter']">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Navigation -->
        <div class="mb-8">
            <x-dashboard.navigation />
        </div>

        <!-- Enhanced Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-[#22C55E]/20 to-[#16A34A]/20 rounded-2xl border border-[#22C55E]/30 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#22C55E] font-semibold font-['Inter']">Total Games</p>
                        <p class="text-3xl font-bold text-white font-['Share_Tech_Mono']">{{ $totalGames }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-[#22C55E] to-[#16A34A] rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-[#3B82F6]/20 to-[#2563EB]/20 rounded-2xl border border-[#3B82F6]/30 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#3B82F6] font-semibold font-['Inter']">Completed</p>
                        <p class="text-3xl font-bold text-white font-['Share_Tech_Mono']">{{ $completedGames }}</p>
                        <p class="text-xs text-[#A1A1AA] font-['Inter']">{{ $totalGames > 0 ? round(($completedGames / $totalGames) * 100) : 0 }}% completion rate</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-[#3B82F6] to-[#2563EB] rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-[#F59E0B]/20 to-[#D97706]/20 rounded-2xl border border-[#F59E0B]/30 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#F59E0B] font-semibold font-['Inter']">Favorites</p>
                        <p class="text-3xl font-bold text-white font-['Share_Tech_Mono']">{{ $favoriteGames }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-[#F59E0B] to-[#D97706] rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-[#8B5CF6]/20 to-[#7C3AED]/20 rounded-2xl border border-[#8B5CF6]/30 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-[#8B5CF6] font-semibold font-['Inter']">Total Playtime</p>
                        <p class="text-3xl font-bold text-white font-['Share_Tech_Mono']">{{ $totalPlaytime }}h</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-[#8B5CF6] to-[#7C3AED] rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-6 mb-8">
            <form method="GET" action="{{ route('dashboard.collection') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search your games..."
                           class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 pl-12 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] font-['Inter']">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                        <svg class="w-5 h-5 text-[#71717A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Filter Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Status Filter -->
                    <select name="status" class="bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] font-['Inter']">
                        <option value="">All Status</option>
                        <option value="have" {{ request('status') === 'have' ? 'selected' : '' }}>Owned</option>
                        <option value="want" {{ request('status') === 'want' ? 'selected' : '' }}>Wishlist</option>
                        <option value="played" {{ request('status') === 'played' ? 'selected' : '' }}>Played</option>
                        <option value="favorites" {{ request('status') === 'favorites' ? 'selected' : '' }}>Favorites</option>
                        <option value="dropped" {{ request('status') === 'dropped' ? 'selected' : '' }}>Dropped</option>
                    </select>

                    <!-- Completion Filter -->
                    <select name="completion" class="bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] font-['Inter']">
                        <option value="">All Completion</option>
                        <option value="not_started" {{ request('completion') === 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="started" {{ request('completion') === 'started' ? 'selected' : '' }}>Started</option>
                        <option value="in_progress" {{ request('completion') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('completion') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="fully_completed" {{ request('completion') === 'fully_completed' ? 'selected' : '' }}>100% Completed</option>
                        <option value="replaying" {{ request('completion') === 'replaying' ? 'selected' : '' }}>Replaying</option>
                        <option value="on_hold" {{ request('completion') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="abandoned" {{ request('completion') === 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                    </select>

                    <!-- Genre Filter -->
                    <select name="genre" class="bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] font-['Inter']">
                        <option value="">All Genres</option>
                        @foreach($genres ?? [] as $genre)
                            <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                        @endforeach
                    </select>

                    <!-- Sort Options -->
                    <select name="sort" class="bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] font-['Inter']">
                        <option value="updated_desc" {{ request('sort') === 'updated_desc' ? 'selected' : '' }}>Recently Updated</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                        <option value="rating_desc" {{ request('sort') === 'rating_desc' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="rating_asc" {{ request('sort') === 'rating_asc' ? 'selected' : '' }}>Lowest Rated</option>
                        <option value="playtime_desc" {{ request('sort') === 'playtime_desc' ? 'selected' : '' }}>Most Playtime</option>
                        <option value="playtime_asc" {{ request('sort') === 'playtime_asc' ? 'selected' : '' }}>Least Playtime</option>
                        <option value="completion_desc" {{ request('sort') === 'completion_desc' ? 'selected' : '' }}>Most Complete</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2 font-['Inter']">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search & Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'status', 'completion', 'genre', 'sort']))
                        <a href="{{ route('dashboard.collection') }}" class="border border-[#3F3F46] hover:border-[#71717A] text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center gap-2 font-['Inter']">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Clear Filters
                        </a>
                    @endif
                </div>

                <!-- Active Filters Display -->
                @if(request()->hasAny(['search', 'status', 'completion', 'genre']))
                    <div class="bg-[#18181B] rounded-xl border border-[#3F3F46] p-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[#A1A1AA] text-sm font-medium font-['Inter']">Active Filters:</span>
                            
                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600/20 text-blue-400 border border-blue-600/30">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 hover:text-blue-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                            
                            @if(request('status'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-600/20 text-green-400 border border-green-600/30">
                                    Status: {{ ucfirst(request('status')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-2 hover:text-green-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                            
                            @if(request('completion'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-600/20 text-purple-400 border border-purple-600/30">
                                    Completion: {{ str_replace('_', ' ', ucfirst(request('completion'))) }}
                                    <a href="{{ request()->fullUrlWithQuery(['completion' => null]) }}" class="ml-2 hover:text-purple-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                            
                            @if(request('genre'))
                                @php $selectedGenre = $genres->find(request('genre')) @endphp
                                @if($selectedGenre)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-600/20 text-yellow-400 border border-yellow-600/30">
                                        Genre: {{ $selectedGenre->name }}
                                        <a href="{{ request()->fullUrlWithQuery(['genre' => null]) }}" class="ml-2 hover:text-yellow-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </a>
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Games Collection -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-white font-['Share_Tech_Mono']">Your Games</h2>
                <div class="text-sm text-[#A1A1AA] font-['Inter']">
                    {{ $gameStatuses->total() ?? $gameStatuses->count() }} games
                    @if(request()->hasAny(['search', 'status', 'completion', 'genre']))
                        (filtered)
                    @else
                        tracked
                    @endif
                </div>
            </div>
            
            @if($gameStatuses->isNotEmpty())
                <!-- 3-Column Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($gameStatuses as $gameStatus)
                        <x-enhanced-game-card :gameStatus="$gameStatus" />
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($gameStatuses->hasPages())
                    <div class="flex justify-center mt-8">
                        {{ $gameStatuses->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="bg-gradient-to-br from-[#3F3F46] to-[#27272A] rounded-2xl p-12 max-w-lg mx-auto">
                        @if(request()->hasAny(['search', 'status', 'completion', 'genre']))
                            <svg class="w-20 h-20 text-[#3F3F46] mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No Games Found</h3>
                            <p class="text-[#A1A1AA] mb-6 text-lg font-['Inter']">
                                No games match your current search and filter criteria.<br>
                                Try adjusting your filters or search terms.
                            </p>
                            <a href="{{ route('dashboard.collection') }}" 
                               class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Show All Games
                            </a>
                        @else
                            <svg class="w-20 h-20 text-[#3F3F46] mx-auto mb-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                            <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Start Your Collection</h3>
                            <p class="text-[#A1A1AA] mb-6 text-lg font-['Inter']">
                                Begin tracking your games by visiting game pages and setting your status using the enhanced status buttons.
                            </p>
                            <a href="{{ route('games.index') }}" 
                               class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Browse Games
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</x-layouts.app> 