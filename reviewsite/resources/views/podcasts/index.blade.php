@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
        <!-- New Combined Header -->
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-bold text-white font-['Share_Tech_Mono']">
                    Gaming Podcasts
                </h1>
                <p class="text-xl text-[#A1A1AA] mt-2 font-['Inter']">
                    Discover gaming podcasts from our community and their reviews
                </p>
            </div>
            <div>
                <a href="{{ route('podcasts.create') }}" 
                   class="bg-white text-[#E53E3E] font-bold py-4 px-8 rounded-lg font-['Inter'] hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-110 inline-block">
                    Submit Your Podcast
                </a>
            </div>
        </div>

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
                   class="bg-white text-[#E53E3E] font-bold py-3 px-8 rounded-lg font-['Inter'] hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-110 inline-block">
                    Submit Your Podcast
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 