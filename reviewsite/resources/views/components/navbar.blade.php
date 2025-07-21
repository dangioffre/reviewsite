<nav class="bg-[#121212] border-b border-[#292929] sticky top-0 z-50" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main navbar -->
        <div class="flex items-center justify-between h-20">
            <!-- Left section: Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-white hover:text-[#A0A0A0] transition-colors font-['Poppins'] tracking-wide">
                DAN & BRIAN <span class="text-[#DC2626]">REVIEWS</span>
            </a>
            </div>

            <!-- Center section: Navigation Links -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}" 
                   class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium px-3 py-2 rounded-md text-sm
                          {{ request()->routeIs('home') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    Home
                </a>
                <a href="{{ route('games.index') }}" 
                   class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium px-3 py-2 rounded-md text-sm
                          {{ request()->routeIs('games.*') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    Games
                </a>
                <a href="{{ route('tech.index') }}" 
                   class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium px-3 py-2 rounded-md text-sm
                          {{ request()->routeIs('tech.*') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    Tech
                </a>
                <a href="{{ route('posts.index') }}" 
                   class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium px-3 py-2 rounded-md text-sm
                          {{ request()->routeIs('posts.*') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    News
                </a>
                <a href="{{ route('lists.index') }}" 
                   class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium px-3 py-2 rounded-md text-sm
                          {{ request()->routeIs('lists.*') ? 'text-white bg-[#FFC107] bg-opacity-20' : '' }}">
                    Lists
                </a>
                <a href="{{ route('podcasts.index') }}" 
                   class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium px-3 py-2 rounded-md text-sm
                          {{ request()->routeIs('podcasts.*') ? 'text-white bg-[#4CAF50] bg-opacity-20' : '' }}">
                    Podcasts
                </a>
                <a href="{{ route('streamer.profiles.index') }}" 
                   class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium px-3 py-2 rounded-md text-sm
                          {{ request()->routeIs('streamer.*') ? 'text-white bg-[#FFC107] bg-opacity-20' : '' }}">
                    Streamers
                </a>
            </div>

            <!-- Right section: Search + Auth -->
            <div class="flex items-center space-x-4">
                <!-- Search Bar -->
                <div class="hidden lg:flex items-center bg-[#1E1E1E] border border-[#292929] rounded-lg px-3 py-2 w-80 focus-within:border-[#03A9F4] focus-within:ring-1 focus-within:ring-[#03A9F4] relative" x-data="searchBar()">
                    <svg class="w-4 h-4 text-[#A0A0A0] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        placeholder="Search..." 
                        class="bg-transparent text-white placeholder-[#A0A0A0] focus:outline-none flex-1 text-sm"
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @keydown.enter="goToSearch()"
                        @focus="showSuggestions = true"
                        @click.away="showSuggestions = false"
                    >
                    
                    <!-- Search Suggestions Dropdown -->
                    <div x-show="showSuggestions && suggestions.length > 0" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute top-full left-0 right-0 mt-1 bg-[#1E1E1E] border border-[#292929] rounded-lg shadow-lg py-2 z-50 max-h-80 overflow-y-auto">
                        <template x-for="suggestion in suggestions" :key="suggestion.url">
                            <a :href="suggestion.url" 
                               class="block px-4 py-2 text-sm text-white hover:bg-[#292929] transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium" x-text="suggestion.title"></div>
                                        <div class="text-xs text-[#A0A0A0]" x-text="suggestion.category"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <template x-if="suggestion.platform">
                                            <span class="text-xs bg-[#292929] px-2 py-1 rounded" x-text="suggestion.platform"></span>
                                        </template>
                                        <template x-if="suggestion.is_live">
                                            <span class="text-xs bg-[#DC2626] px-2 py-1 rounded">LIVE</span>
                                        </template>
                                    </div>
                                </div>
                            </a>
                        </template>
                        <div class="border-t border-[#292929] mt-2 pt-2">
                            <button @click="goToSearch()" 
                                    class="block w-full px-4 py-2 text-sm text-[#A0A0A0] hover:text-white hover:bg-[#292929] transition-colors text-left">
                                <span>See all results for "</span><span x-text="query"></span><span>"</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Auth Section -->
                @auth
                    <div class="flex items-center space-x-4">
                        @livewire('notifications-bell')
                        
                        <!-- User Menu Dropdown -->
                        <div class="relative" x-data="{ userMenu: false }">
                            <button @click="userMenu = !userMenu" 
                                    class="flex items-center space-x-2 text-white hover:text-[#A0A0A0] transition-colors">
                            <span class="hidden md:block text-sm">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="userMenu" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             @click.away="userMenu = false"
                             class="absolute right-0 mt-2 w-56 bg-[#1E1E1E] border border-[#292929] rounded-lg shadow-lg py-2 z-50">
                            
                    @if(auth()->user()->is_admin)
                        <a href="/admin" 
                                   class="block px-4 py-2 text-sm text-white hover:bg-[#DC2626] hover:bg-opacity-20 transition-colors">
                                    🛠️ Admin Dashboard
                        </a>
                    @endif
                            
                            <a href="{{ route('podcasts.invitations') }}" 
                               class="block px-4 py-2 text-sm text-white hover:bg-[#4CAF50] hover:bg-opacity-20 transition-colors relative">
                                🎧 Team Invites
                                @if(auth()->user()->pendingPodcastInvitations()->count() > 0)
                                    <span class="absolute right-2 top-2 bg-[#DC2626] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        {{ auth()->user()->pendingPodcastInvitations()->count() }}
                                    </span>
                                @endif
                            </a>
                            
                            <a href="{{ route('dashboard') }}" 
                               class="block px-4 py-2 text-sm text-white hover:bg-[#292929] transition-colors">
                                📊 Dashboard
                            </a>
                            
                            <hr class="my-2 border-[#292929]">
                            
                            <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-[#DC2626] hover:bg-opacity-50 transition-colors">
                                    🚪 Logout
                        </button>
                    </form>
                        </div>
                    </div>
                </div>
                @else
                    <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}" 
                           class="text-white hover:text-[#A0A0A0] font-medium text-sm transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" 
                           class="bg-[#DC2626] hover:bg-[#B91C1C] text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                        Register
                    </a>
                    </div>
                @endauth
                
                <!-- Mobile menu button -->
                <button @click="open = !open" class="lg:hidden text-white p-2 hover:text-[#A0A0A0] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="lg:hidden border-t border-[#292929]">
            <div class="px-4 pt-4 pb-6 space-y-2 bg-[#121212]">
                <!-- Search Bar Mobile -->
                <div class="mb-4" x-data="searchBar()">
                    <div class="flex items-center bg-[#1E1E1E] border border-[#292929] rounded-lg px-3 py-2 focus-within:border-[#03A9F4] focus-within:ring-1 focus-within:ring-[#03A9F4]">
                        <svg class="w-4 h-4 text-[#A0A0A0] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            placeholder="Search..." 
                            class="bg-transparent text-white placeholder-[#A0A0A0] focus:outline-none flex-1 text-sm"
                            x-model="query"
                            @keydown.enter="goToSearch()"
                        >
                    </div>
                </div>

                <!-- Navigation Links -->
                <a href="{{ route('home') }}" 
                   class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium rounded-md
                          {{ request()->routeIs('home') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    🏠 Home
                </a>
                <a href="{{ route('games.index') }}" 
                   class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium rounded-md
                          {{ request()->routeIs('games.*') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    🎮 Games
                </a>
                <a href="{{ route('tech.index') }}" 
                   class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium rounded-md
                          {{ request()->routeIs('tech.*') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    💻 Tech
                </a>
                <a href="{{ route('posts.index') }}" 
                   class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium rounded-md
                          {{ request()->routeIs('posts.*') ? 'text-white bg-[#DC2626] bg-opacity-20' : '' }}">
                    📰 News
                </a>
                <a href="{{ route('lists.index') }}" 
                   class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium rounded-md
                          {{ request()->routeIs('lists.*') ? 'text-white bg-[#FFC107] bg-opacity-20' : '' }}">
                    📝 Lists
                </a>
                <a href="{{ route('podcasts.index') }}" 
                   class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium rounded-md
                          {{ request()->routeIs('podcasts.*') ? 'text-white bg-[#4CAF50] bg-opacity-20' : '' }}">
                    🎧 Podcasts
                </a>
                <a href="{{ route('streamer.profiles.index') }}" 
                   class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] font-medium rounded-md
                          {{ request()->routeIs('streamer.*') ? 'text-white bg-[#FFC107] bg-opacity-20' : '' }}">
                    📺 Streamers
                </a>

                <!-- Mobile Auth Section -->
                @auth
                    <hr class="my-4 border-[#292929]">
                    <div class="space-y-2">
                        <div class="px-3 py-2 text-white text-sm font-medium">
                            Welcome, {{ auth()->user()->name }}
                        </div>
                        
                        @if(auth()->user()->is_admin)
                            <a href="/admin" 
                               class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] rounded-md">
                                🛠️ Admin Dashboard
                            </a>
                        @endif
                        
                        <a href="{{ route('podcasts.invitations') }}" 
                           class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] rounded-md relative">
                            🎧 Team Invites
                            @if(auth()->user()->pendingPodcastInvitations()->count() > 0)
                                <span class="absolute right-3 top-2 bg-[#DC2626] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->pendingPodcastInvitations()->count() }}
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('dashboard') }}" 
                           class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] rounded-md">
                            📊 Dashboard
                        </a>
                        
                        <form action="{{ route('logout') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" 
                                    class="block w-full text-left px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] rounded-md">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                @else
                    <hr class="my-4 border-[#292929]">
                    <div class="space-y-2">
                        <a href="{{ route('login') }}" 
                           class="block px-3 py-2 text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] rounded-md">
                            🔑 Login
                        </a>
                        <a href="{{ route('register') }}" 
                           class="block px-3 py-2 bg-[#DC2626] text-white hover:bg-[#B91C1C] transition-colors font-['Inter'] rounded-md text-center">
                            ✨ Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav> 
