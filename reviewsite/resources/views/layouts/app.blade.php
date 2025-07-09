<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dan & Brian Reviews')</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono:wght@400&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Styles -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body {
                    background: #1A1A1B;
                    font-family: 'Inter', sans-serif;
                    color: #FFFFFF;
                }
                
                /* Audio Player Styles */
                .audio-player {
                    background: #27272A;
                    border: 1px solid #3F3F46;
                    border-radius: 8px;
                    padding: 1rem;
                    margin: 1rem 0;
                }
                
                .audio-player audio {
                    width: 100%;
                    background: #1A1A1B;
                    border-radius: 8px;
                }
                
                .audio-player audio::-webkit-media-controls-panel {
                    background-color: #1A1A1B;
                }
                
                .audio-player audio::-webkit-media-controls-play-button,
                .audio-player audio::-webkit-media-controls-pause-button {
                    background-color: #E53E3E;
                    border-radius: 50%;
                }
                
                .audio-player audio::-webkit-media-controls-timeline {
                    background-color: #3F3F46;
                    border-radius: 4px;
                }
                
                .audio-player audio::-webkit-media-controls-current-time-display,
                .audio-player audio::-webkit-media-controls-time-remaining-display {
                    color: #FFFFFF;
                }
            </style>
        @endif
        
        @stack('styles')
    </head>
    <body>
        <!-- Header -->
        <x-navbar />
        
        <!-- Main Content -->
        <main class="bg-[#1A1A1B] min-h-screen">
            @yield('content')
        </main>
        
        <!-- Livewire Scripts -->
        @livewireScripts
        
        @stack('scripts')
    </body>
</html> 