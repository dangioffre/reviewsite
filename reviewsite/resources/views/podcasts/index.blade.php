<x-layouts.app>
<div class="min-h-screen bg-[#151515]">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 via-blue-600/20 to-red-600/20"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-blue-500 rounded-2xl mb-4 shadow-2xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                </div>
                
                <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4 font-['Share_Tech_Mono'] bg-gradient-to-r from-white via-green-200 to-blue-200 bg-clip-text text-transparent">
                    Gaming Podcasts
                </h1>
                
                <p class="text-lg lg:text-xl text-zinc-400 mb-6 max-w-2xl mx-auto font-['Inter'] leading-relaxed">
                    Dive deep into gaming discussions and insights. Discover podcasts from our community and their reviews.
                </p>
                
                <div class="flex items-center justify-center gap-8 text-zinc-400 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="font-medium">{{ $podcasts->total() }} Podcasts</span>
                    </div>
                    <div class="w-px h-6 bg-zinc-600"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                        <span class="font-medium">Community Reviews</span>
                    </div>
                    <div class="w-px h-6 bg-zinc-600"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
                        <span class="font-medium">All Genres</span>
                    </div>
                </div>

                <a href="{{ route('podcasts.create') }}" 
                   class="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-['Inter'] flex items-center justify-center gap-2 inline-flex">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Submit Your Podcast
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 mt-8">

            @if($podcasts->count() > 0)
                <!-- Podcasts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($podcasts as $podcast)
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6 hover:border-[#E53E3E] transition-all duration-300 hover:shadow-[0_0_30px_rgba(229,62,62,0.1)]">
                            <!-- Podcast Logo -->
                            <div class="mb-4">
                                @if($podcast->logo_url)
                                    <img src="{{ $podcast->logo_url }}" 
                                         alt="{{ $podcast->name }}"
                                         class="w-full h-48 object-cover rounded-lg">
                                @else
                                    <div class="w-full h-48 bg-[#3F3F46] rounded-lg flex items-center justify-center">
                                        <svg class="w-16 h-16 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Podcast Info -->
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">
                                    <a href="{{ route('podcasts.show', $podcast) }}" 
                                       class="hover:text-[#E53E3E] transition-colors">
                                        {{ $podcast->name }}
                                    </a>
                                </h3>
                                
                                @if($podcast->description)
                                    <p class="text-[#A1A1AA] text-sm mb-3 font-['Inter'] line-clamp-3">
                                        {{ Str::limit($podcast->description, 120) }}
                                    </p>
                                @endif

                                <div class="flex items-center text-[#A1A1AA] text-sm mb-3 font-['Inter']">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $podcast->owner->name }}</span>
                                </div>

                                @if($podcast->hosts && count($podcast->hosts) > 0)
                                    <div class="flex items-center text-[#A1A1AA] text-sm mb-3 font-['Inter']">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                        </svg>
                                        <span>{{ implode(', ', $podcast->hosts) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Stats -->
                            <div class="flex justify-between items-center text-[#A1A1AA] text-sm mb-4 font-['Inter']">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                    </svg>
                                    <span>{{ $podcast->episodes_count }} episodes</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                    <span>{{ $podcast->reviews_count }} reviews</span>
                                </div>
                            </div>

                            <!-- View Button -->
                            <a href="{{ route('podcasts.show', $podcast) }}" 
                               class="block w-full bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200 text-center">
                                View Podcast
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $podcasts->links() }}
                </div>

            @else
                <!-- No Podcasts -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-8 bg-[#3F3F46] rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">No Podcasts Yet</h3>
                    <p class="text-[#A1A1AA] mb-8 font-['Inter']">
                        Be the first to submit a gaming podcast to our community!
                    </p>
                    <a href="{{ route('podcasts.create') }}" 
                       class="bg-[#E53E3E] text-white font-bold py-3 px-8 rounded-lg font-['Inter'] hover:bg-[#DC2626] transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-110 inline-block">
                        Submit Your Podcast
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app> 