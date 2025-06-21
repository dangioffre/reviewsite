<nav class="bg-gray-900 border-b border-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="text-xl font-bold text-white hover:text-gray-300 transition">
                DAN & BRIAN <span class="text-red-500">REVIEWS</span>
            </a>
            
            <!-- Search Bar (hidden on mobile) -->
            <div class="hidden md:flex items-center bg-gray-800 rounded-lg px-3 py-2 max-w-md flex-1 mx-8">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    type="text" 
                    placeholder="Search games, articles..." 
                    class="bg-transparent text-white placeholder-gray-400 focus:outline-none flex-1"
                >
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('reviews.index') }}" 
                   class="text-white hover:text-red-500 transition font-medium {{ request()->routeIs('reviews*') ? 'text-red-500' : '' }}">
                    Reviews
                </a>
                <a href="{{ route('posts.index') }}" 
                   class="text-white hover:text-red-500 transition font-medium {{ request()->routeIs('posts*') ? 'text-red-500' : '' }}">
                    Articles
                </a>
            </div>

            <!-- Auth Actions -->
            <div class="flex items-center space-x-4">
                @auth
                    <a href="/admin" 
                       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        Dashboard
                    </a>
                @else
                    <a href="/admin/login" 
                       class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition">
                        Login
                    </a>
                @endauth
                
                <!-- Mobile menu button -->
                <button class="md:hidden text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav> 