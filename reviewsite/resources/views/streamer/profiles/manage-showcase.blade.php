@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#151515] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-2 font-['Share_Tech_Mono'] leading-tight">
                        Game Showcase
                    </h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">
                        Manage games displayed on your streamer profile for {{ $streamerProfile->channel_name }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('streamer.profile.show', $streamerProfile) }}" 
                       class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter'] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        View Profile
                    </a>
                    <a href="{{ route('streamer.profile.edit', $streamerProfile) }}" 
                       class="px-4 py-2 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors font-['Inter'] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Livewire Component -->
        @livewire('showcase-game-manager', ['streamerProfile' => $streamerProfile])
    </div>
</div>
@endsection 