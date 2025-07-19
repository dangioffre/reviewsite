<nav class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-4">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] {{ request()->routeIs('dashboard') ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
            </svg>
            <span class="font-medium">Overview</span>
            @if(request()->routeIs('dashboard'))
                <div class="ml-2 w-2 h-2 bg-[#2563EB] rounded-full"></div>
            @endif
        </a>
        
        <a href="{{ route('dashboard.reviews-and-likes') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] {{ request()->routeIs('dashboard.reviews-and-likes') ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Reviews & Likes</span>
             @if(request()->routeIs('dashboard.reviews-and-likes'))
                <div class="ml-2 w-2 h-2 bg-[#2563EB] rounded-full"></div>
            @endif
        </a>
        
        <a href="{{ route('dashboard.lists') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] {{ request()->routeIs('dashboard.lists') ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            <span class="font-medium">Lists</span>
             @if(request()->routeIs('dashboard.lists'))
                <div class="ml-2 w-2 h-2 bg-[#2563EB] rounded-full"></div>
            @endif
        </a>
        
        <a href="{{ route('dashboard.collection') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] {{ request()->routeIs('dashboard.collection') ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">Game Collection</span>
             @if(request()->routeIs('dashboard.collection'))
                <div class="ml-2 w-2 h-2 bg-[#2563EB] rounded-full"></div>
            @endif
        </a>

        <a href="{{ route('podcasts.dashboard') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] {{ request()->routeIs('podcasts.dashboard') ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
            </svg>
            <span class="font-medium">Podcast Management</span>
            @if(request()->routeIs('podcasts.dashboard'))
                <div class="ml-2 w-2 h-2 bg-[#2563EB] rounded-full"></div>
            @endif
        </a>

        @if(auth()->user()->streamerProfile)
            <a href="{{ route('streamer.profile.show', auth()->user()->streamerProfile) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] {{ request()->routeIs('streamer.profile.*') ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span class="font-medium">Streamer Profile</span>
                @if(request()->routeIs('streamer.profile.*'))
                    <div class="ml-2 w-2 h-2 bg-[#2563EB] rounded-full"></div>
                @endif
            </a>
        @else
            <a href="{{ route('streamer.profiles.create') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-['Inter'] bg-gradient-to-r from-[#7C3AED] to-[#A855F7] text-white hover:from-[#8B5CF6] hover:to-[#C084FC] border border-[#7C3AED]/30">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span class="font-medium">Create Streamer Profile</span>
                <div class="ml-2 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            </a>
        @endif
    </div>
</nav> 