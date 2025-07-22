<nav class="bg-[#121212] border-b border-[#292929] w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <!-- Brand -->
        <div class="flex items-center flex-shrink-0">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-white font-['Poppins'] tracking-wide flex items-center">
                DAN & BRIAN <span class="ml-1 text-[#DC2626]">REVIEWS</span>
            </a>
        </div>

        <!-- Nav Links -->
        <div class="hidden md:flex items-center space-x-2 ml-10">
            @php
                $navLinks = [
                    ['label' => 'Home', 'route' => 'home'],
                    ['label' => 'Games', 'route' => 'games.index'],
                    ['label' => 'Tech', 'route' => 'tech.index'],
                    ['label' => 'News', 'route' => 'posts.index'],
                    ['label' => 'Lists', 'route' => 'lists.index'],
                    ['label' => 'Podcasts', 'route' => 'podcasts.index'],
                    ['label' => 'Streamers', 'route' => 'streamer.profiles.index'],
                ];
            @endphp
            @foreach ($navLinks as $link)
                @php $isActive = request()->routeIs($link['route']) || (isset($link['routes']) && collect($link['routes'])->contains(fn($r) => request()->routeIs($r))); @endphp
                <a href="{{ route($link['route']) }}"
                   class="relative px-3 py-2 font-medium text-sm font-['Inter'] transition-colors duration-200
                   {{ $isActive ? 'text-white' : 'text-[#A0A0A0] hover:text-white' }}">
                    {{ $link['label'] }}
                    @if($isActive)
                        <span class="absolute left-1/2 -bottom-1.5 -translate-x-1/2 w-6 h-0.5 bg-[#DC2626] rounded"></span>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- User Menu -->
        <div class="flex items-center space-x-4 ml-auto">
            @auth
                <div class="relative" tabindex="0">
                    <button class="flex items-center text-white font-['Inter'] text-sm focus:outline-none" tabindex="-1">
                        {{ Auth::user()->name }}
                        <svg class="ml-1 w-4 h-4 text-[#A0A0A0] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-[#18181B] border border-[#292929] rounded-lg shadow-lg py-2 z-50 invisible opacity-0 group-hover:visible group-hover:opacity-100 focus-within:visible focus-within:opacity-100 hover:visible hover:opacity-100 transition-all duration-150"
                        style="min-width: 10rem;"
                        onmouseover="this.classList.add('!visible','!opacity-100')" onmouseout="this.classList.remove('!visible','!opacity-100')">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-[#A0A0A0] hover:text-white hover:bg-[#232326] transition-colors">Dashboard</a>
                        <a href="{{ route('profile.show', Auth::user()) }}" class="block px-4 py-2 text-sm text-[#A0A0A0] hover:text-white hover:bg-[#232326] transition-colors">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-[#A0A0A0] hover:text-[#DC2626] hover:bg-[#232326] transition-colors">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-[#A0A0A0] hover:text-white font-['Inter'] text-sm px-3 py-2 transition-colors">Sign In</a>
                <a href="{{ route('register') }}" class="bg-[#DC2626] hover:bg-[#B91C1C] text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">Register</a>
            @endauth
        </div>
    </div>
    <style>
        /* User dropdown fix: keep open on hover/focus */
        [tabindex="0"]:hover > div,
        [tabindex="0"]:focus-within > div {
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
</nav> 
