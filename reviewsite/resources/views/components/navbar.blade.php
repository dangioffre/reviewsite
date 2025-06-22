<nav class="bg-[#151515] border-b border-[#3F3F46]" x-data="{ open: false }">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="text-xl font-bold text-white hover:text-[#A1A1AA] transition font-['Share_Tech_Mono']">
                DAN & BRIAN <span class="text-[#E53E3E]">REVIEWS</span>
            </a>
            
            <!-- Search Bar (hidden on mobile) -->
            <div class="hidden md:flex items-center bg-[#1A1A1B] border border-[#3F3F46] rounded-lg px-3 py-2 max-w-md flex-1 mx-8 focus-within:border-[#2563EB] focus-within:ring-1 focus-within:ring-[#2563EB]">
                <svg class="w-5 h-5 text-[#A1A1AA] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    type="text" 
                    placeholder="Search games, tech, articles..." 
                    class="bg-transparent text-white placeholder-[#A1A1AA] focus:outline-none flex-1 text-base"
                >
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" 
                   class="text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] font-medium
                          {{ request()->routeIs('home') ? 'text-white border-b-2 border-[#E53E3E] pb-1' : '' }}">
                    Home
                </a>
                <a href="{{ route('games.index') }}" 
                   class="text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] font-medium
                          {{ request()->routeIs('games.*') ? 'text-white border-b-2 border-[#E53E3E] pb-1' : '' }}">
                    Games
                </a>
                <a href="{{ route('tech.index') }}" 
                   class="text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] font-medium
                          {{ request()->routeIs('tech.*') ? 'text-white border-b-2 border-[#E53E3E] pb-1' : '' }}">
                    Tech
                </a>
                <a href="{{ route('posts.index') }}" 
                   class="text-[#A1A1AA] hover:text-white transition-colors font-['Inter'] font-medium
                          {{ request()->routeIs('posts.*') ? 'text-white border-b-2 border-[#E53E3E] pb-1' : '' }}">
                    News
                </a>
            </div>

            <!-- Auth Actions -->
            <div class="flex items-center space-x-4">
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="/admin" 
                           class="bg-[#E53E3E] hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors font-['Inter'] text-base">
                            Admin Dashboard
                        </a>
                    @endif
                    <span class="text-[#A1A1AA] font-['Inter']">Welcome, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-[#27272A] text-white px-4 py-2 rounded-lg border border-[#E53E3E] hover:bg-red-900/50 transition-colors font-medium font-['Inter'] text-base">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" 
                       class="bg-[#27272A] text-white px-4 py-2 rounded-lg border border-[#E53E3E] hover:bg-red-900/50 transition-colors font-medium font-['Inter'] text-base">
                        Login
                    </a>
                    <a href="{{ route('register') }}" 
                       class="bg-[#E53E3E] hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors font-['Inter'] text-base">
                        Register
                    </a>
                @endauth
                
                <!-- Mobile menu button -->
                <button @click="open = !open" class="md:hidden text-white p-2 hover:text-[#A1A1AA] transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-transition class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-[#151515] border-t border-[#3F3F46]">
                <a href="{{ route('home') }}" 
                   class="block px-3 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter']
                          {{ request()->routeIs('home') ? 'text-white bg-[#E53E3E] bg-opacity-20 rounded-md' : '' }}">
                    Home
                </a>
                <a href="{{ route('games.index') }}" 
                   class="block px-3 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter']
                          {{ request()->routeIs('games.*') ? 'text-white bg-[#E53E3E] bg-opacity-20 rounded-md' : '' }}">
                    Games
                </a>
                <a href="{{ route('tech.index') }}" 
                   class="block px-3 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter']
                          {{ request()->routeIs('tech.*') ? 'text-white bg-[#E53E3E] bg-opacity-20 rounded-md' : '' }}">
                    Tech
                </a>
                <a href="{{ route('posts.index') }}" 
                   class="block px-3 py-2 text-[#A1A1AA] hover:text-white transition-colors font-['Inter']
                          {{ request()->routeIs('posts.*') ? 'text-white bg-[#E53E3E] bg-opacity-20 rounded-md' : '' }}">
                    News
                </a>
            </div>
        </div>
    </div>
</nav> 