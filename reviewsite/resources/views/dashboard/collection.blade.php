<x-layouts.app>
<div class="min-h-screen bg-gradient-to-br from-[#0F0F0F] via-[#1A1A1A] to-[#0F0F0F] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header Section with Better Spacing -->
        <div class="mb-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-3">
                    <h1 class="text-4xl lg:text-5xl font-bold text-white font-['Share_Tech_Mono'] bg-gradient-to-r from-white via-blue-200 to-purple-200 bg-clip-text text-transparent">
                        Your Games
                    </h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter'] max-w-2xl">
                        Your personal game library and detailed tracking. Manage your collection, track progress, and discover your gaming patterns.
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gradient-to-r from-[#3F3F46] to-[#27272A] hover:from-[#52525B] hover:to-[#3F3F46] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center gap-2 font-['Inter']">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Navigation with Better Spacing -->
        <div class="mb-10">
            <x-dashboard.navigation />
        </div>

        <!-- Enhanced Stats Cards with Better Spacing -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div class="bg-gradient-to-br from-[#22C55E]/20 to-[#16A34A]/20 rounded-2xl border border-[#22C55E]/30 p-8 hover:border-[#22C55E]/50 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="text-sm text-[#22C55E] font-semibold font-['Inter'] uppercase tracking-wide">Total Games</p>
                        <p class="text-4xl font-bold text-white font-['Share_Tech_Mono']">{{ $totalGames }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-r from-[#22C55E] to-[#16A34A] rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-[#3B82F6]/20 to-[#2563EB]/20 rounded-2xl border border-[#3B82F6]/30 p-8 hover:border-[#3B82F6]/50 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="text-sm text-[#3B82F6] font-semibold font-['Inter'] uppercase tracking-wide">Completed</p>
                        <p class="text-4xl font-bold text-white font-['Share_Tech_Mono']">{{ $completedGames }}</p>
                        <p class="text-sm text-[#A1A1AA] font-['Inter']">{{ $totalGames > 0 ? round(($completedGames / $totalGames) * 100) : 0 }}% completion rate</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-r from-[#3B82F6] to-[#2563EB] rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-[#F59E0B]/20 to-[#D97706]/20 rounded-2xl border border-[#F59E0B]/30 p-8 hover:border-[#F59E0B]/50 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="text-sm text-[#F59E0B] font-semibold font-['Inter'] uppercase tracking-wide">Favorites</p>
                        <p class="text-4xl font-bold text-white font-['Share_Tech_Mono']">{{ $favoriteGames }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-r from-[#F59E0B] to-[#D97706] rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-[#8B5CF6]/20 to-[#7C3AED]/20 rounded-2xl border border-[#8B5CF6]/30 p-8 hover:border-[#8B5CF6]/50 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <p class="text-sm text-[#8B5CF6] font-semibold font-['Inter'] uppercase tracking-wide">Total Playtime</p>
                        <p class="text-4xl font-bold text-white font-['Share_Tech_Mono']">{{ $totalPlaytime }}h</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-r from-[#8B5CF6] to-[#7C3AED] rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section with Better Spacing -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-8 mb-12 shadow-xl">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-white font-['Share_Tech_Mono'] mb-2">Search & Filter</h2>
                <p class="text-[#A1A1AA] font-['Inter']">Find and organize your games with powerful search and filtering options</p>
            </div>
            
            <form method="GET" action="{{ route('dashboard.collection') }}" class="space-y-6">
                <!-- Search Bar with Better Styling -->
                <div class="relative">
                    <label for="search" class="block text-sm font-medium text-white mb-3 font-['Inter']">Search Games</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search by game name, genre, or platform..."
                               class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-6 py-4 pl-14 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter'] transition-all duration-200">
                        <div class="absolute left-5 top-1/2 transform -translate-y-1/2">
                            <svg class="w-5 h-5 text-[#71717A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filter Row with Better Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-white mb-3 font-['Inter']">Status</label>
                        <select name="status" id="status" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter'] transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="have" {{ request('status') === 'have' ? 'selected' : '' }}>Owned</option>
                            <option value="want" {{ request('status') === 'want' ? 'selected' : '' }}>Wishlist</option>
                            <option value="played" {{ request('status') === 'played' ? 'selected' : '' }}>Played</option>
                            <option value="favorites" {{ request('status') === 'favorites' ? 'selected' : '' }}>Favorites</option>
                            <option value="dropped" {{ request('status') === 'dropped' ? 'selected' : '' }}>Dropped</option>
                        </select>
                    </div>

                    <!-- Completion Filter -->
                    <div>
                        <label for="completion" class="block text-sm font-medium text-white mb-3 font-['Inter']">Completion</label>
                        <select name="completion" id="completion" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter'] transition-all duration-200">
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
                    </div>

                    <!-- Genre Filter -->
                    <div>
                        <label for="genre" class="block text-sm font-medium text-white mb-3 font-['Inter']">Genre</label>
                        <select name="genre" id="genre" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter'] transition-all duration-200">
                            <option value="">All Genres</option>
                            @foreach($genres ?? [] as $genre)
                                <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-white mb-3 font-['Inter']">Sort By</label>
                        <select name="sort" id="sort" class="w-full bg-[#18181B] border border-[#3F3F46] rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-['Inter'] transition-all duration-200">
                            <option value="updated_desc" {{ request('sort') === 'updated_desc' ? 'selected' : '' }}>Recently Updated</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                            <option value="rating_desc" {{ request('sort') === 'rating_desc' ? 'selected' : '' }}>Rating High-Low</option>
                            <option value="rating_asc" {{ request('sort') === 'rating_asc' ? 'selected' : '' }}>Rating Low-High</option>
                            <option value="playtime_desc" {{ request('sort') === 'playtime_desc' ? 'selected' : '' }}>Most Playtime</option>
                            <option value="playtime_asc" {{ request('sort') === 'playtime_asc' ? 'selected' : '' }}>Least Playtime</option>
                            <option value="completion_desc" {{ request('sort') === 'completion_desc' ? 'selected' : '' }}>Completion High-Low</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons with Better Spacing -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white font-bold py-4 px-8 rounded-xl font-['Inter'] transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                    
                    @if(request()->hasAny(['search', 'status', 'completion', 'genre', 'sort']))
                        <a href="{{ route('dashboard.collection') }}" 
                           class="flex-1 bg-gradient-to-r from-[#3F3F46] to-[#27272A] hover:from-[#52525B] hover:to-[#3F3F46] text-white font-bold py-4 px-8 rounded-xl font-['Inter'] transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Clear All Filters
                        </a>
                    @endif
                </div>

                <!-- Active Filters Display -->
                @if(request()->hasAny(['search', 'status', 'completion', 'genre', 'sort']))
                    <div class="pt-6 border-t border-[#3F3F46]">
                        <div class="flex flex-wrap gap-3">
                            <span class="text-sm text-[#A1A1AA] font-['Inter'] font-medium">Active filters:</span>
                            
                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-[#2563EB]/20 text-[#2563EB] font-['Inter']">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Search: "{{ request('search') }}"
                                </span>
                            @endif
                            
                            @if(request('status'))
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-500/20 text-green-400 font-['Inter']">
                                    Status: {{ ucfirst(request('status')) }}
                                </span>
                            @endif
                            
                            @if(request('completion'))
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-500/20 text-blue-400 font-['Inter']">
                                    Completion: {{ ucwords(str_replace('_', ' ', request('completion'))) }}
                                </span>
                            @endif
                            
                            @if(request('genre'))
                                @php $selectedGenre = $genres->find(request('genre')); @endphp
                                @if($selectedGenre)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-purple-500/20 text-purple-400 font-['Inter']">
                                        Genre: {{ $selectedGenre->name }}
                                    </span>
                                @endif
                            @endif
                            
                            @if(request('sort') && request('sort') !== 'updated_desc')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-500/20 text-yellow-400 font-['Inter']">
                                    Sorted: {{ ucwords(str_replace('_', ' ', request('sort'))) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Games Collection with Better Spacing -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl border border-[#3F3F46] p-8 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
                <div class="space-y-2">
                    <h2 class="text-3xl font-bold text-white font-['Share_Tech_Mono']">Your Games</h2>
                    <p class="text-[#A1A1AA] font-['Inter']">
                        {{ $gameStatuses->total() ?? $gameStatuses->count() }} games
                        @if(request()->hasAny(['search', 'status', 'completion', 'genre']))
                            found with current filters
                        @else
                            in your collection
                        @endif
                    </p>
                </div>
                
                @if($gameStatuses->isNotEmpty())
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-[#A1A1AA] font-['Inter']">
                            Showing {{ $gameStatuses->firstItem() }}-{{ $gameStatuses->lastItem() }} of {{ $gameStatuses->total() }}
                        </div>
                    </div>
                @endif
            </div>
            
            @if($gameStatuses->isNotEmpty())
                <!-- 3-Column Grid Layout with Better Spacing -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($gameStatuses as $gameStatus)
                        <x-enhanced-game-card :gameStatus="$gameStatus" />
                    @endforeach
                </div>
                
                <!-- Pagination with Better Spacing -->
                @if($gameStatuses->hasPages())
                    <div class="flex justify-center mt-12">
                        {{ $gameStatuses->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State with Better Design -->
                <div class="text-center py-20">
                    <div class="bg-gradient-to-br from-[#3F3F46] to-[#27272A] rounded-2xl p-16 max-w-2xl mx-auto border border-[#3F3F46]">
                        @if(request()->hasAny(['search', 'status', 'completion', 'genre']))
                            <div class="w-24 h-24 mx-auto mb-8 bg-[#3F3F46] rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-white mb-6 font-['Share_Tech_Mono']">No Games Found</h3>
                            <p class="text-[#A1A1AA] mb-8 text-lg font-['Inter'] leading-relaxed">
                                No games match your current search and filter criteria.<br>
                                Try adjusting your filters or search terms to find what you're looking for.
                            </p>
                            <a href="{{ route('dashboard.collection') }}" 
                               class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-3 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Show All Games
                            </a>
                        @else
                            <div class="w-24 h-24 mx-auto mb-8 bg-[#3F3F46] rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-[#A1A1AA]" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Start Your Collection</h3>
                            <p class="text-[#A1A1AA] mb-8 text-lg font-['Inter'] leading-relaxed">
                                Begin tracking your games by visiting game pages and setting your status using the enhanced status buttons. 
                                Build your personal gaming library and track your progress.
                            </p>
                            <a href="{{ route('games.index') }}" 
                               class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 inline-flex items-center gap-3 shadow-lg hover:shadow-xl">
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
