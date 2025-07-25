@extends('layouts.app')

@push('styles')
<style>
    /* Ensure navbar styling is not overridden */
    nav {
        background-color: #121212 !important;
        border-bottom: 1px solid #292929 !important;
    }
    
    /* Normal mode styling - ensure sections display properly with spacing */
    .draggable-section {
        margin-bottom: 1.5rem;
    }
    
    .draggable-section:last-child {
        margin-bottom: 0;
    }
    
    /* Simple drag and drop styling */
    .draggable-section.customize-mode {
        border: 2px dashed #6366f1;
        background: rgba(99, 102, 241, 0.05);
        cursor: move;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        position: relative;
        transition: all 0.2s ease;
    }
    
    .draggable-section.customize-mode:hover {
        border-color: #4f46e5;
        background: rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    
    /* SortableJS classes */
    .sortable-ghost {
        opacity: 0.4;
        background: rgba(99, 102, 241, 0.2) !important;
    }
    
    .sortable-chosen {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
    }
    
    .sortable-drag {
        transform: rotate(2deg);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    /* Section labels */
    .draggable-section.customize-mode::before {
        content: attr(data-section);
        position: absolute;
        top: -12px;
        left: 12px;
        background: #6366f1;
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    /* Clean section names */
    .draggable-section.customize-mode[data-section="profile-header"]::before {
        content: "Profile Header";
    }
    .draggable-section.customize-mode[data-section="streaming-schedule"]::before {
        content: "Streaming Schedule";
    }
    .draggable-section.customize-mode[data-section="showcased-games"]::before {
        content: "Showcased Games";
    }
    .draggable-section.customize-mode[data-section="recent-reviews"]::before {
        content: "Recent Reviews";
    }
    .draggable-section.customize-mode[data-section="recent-vods"]::before {
        content: "Recent VODs";
    }
    
    /* Container styling in customize mode */
    #customizable-sections.customize-active {
        background: rgba(0, 0, 0, 0.02);
        border-radius: 0.5rem;
        padding: 2rem;
        border: 1px dashed #374151;
        position: relative;
        min-height: 400px;
    }
    
    /* Clean container styling */
    #customizable-sections {
        width: 100%;
        max-width: 100%;
    }
    
    /* Modal Styling for Twitch Embed */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }
    
    .modal.show {
        display: block !important;
    }
    
    .modal-dialog {
        position: relative;
        width: auto;
        margin: 1.75rem;
        pointer-events: none;
    }
    
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background: linear-gradient(135deg, #3f3f46 0%, #27272a 100%);
        border: 1px solid #52525b;
        border-radius: 0.75rem;
        outline: 0;
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 1.5rem;
        border-bottom: none;
        border-top-left-radius: calc(0.75rem - 1px);
        border-top-right-radius: calc(0.75rem - 1px);
    }
    
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 0;
    }
    
    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-top: 1px solid #52525b;
        border-bottom-right-radius: calc(0.75rem - 1px);
        border-bottom-left-radius: calc(0.75rem - 1px);
    }
    
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.8);
    }
    
    .modal-backdrop.fade {
        opacity: 0;
    }
    
    .modal-backdrop.show {
        opacity: 1;
    }
    
    body.modal-open {
        overflow: hidden;
    }
    
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
    }
    
    @media (min-width: 992px) {
        .modal-xl {
            max-width: 1200px;
        }
    }
    
    .aspect-video {
        aspect-ratio: 16 / 9;
    }
    
    /* Theme styles - only apply to content area */
    #customizable-sections.theme-dark {
        /* Default dark theme - no changes needed */
    }
    
    #customizable-sections.theme-light {
        background-color: #f8fafc;
        color: #1a202c;
    }
    
    #customizable-sections.theme-light .bg-zinc-800,
    #customizable-sections.theme-light .bg-zinc-900 {
        background-color: #ffffff !important;
        color: #1a202c !important;
    }
    
    #customizable-sections.theme-neon {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: #00ff88;
    }
    
    #customizable-sections.theme-neon .bg-zinc-800,
    #customizable-sections.theme-neon .bg-zinc-900 {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;
        border: 1px solid #00ff88 !important;
        color: #00ff88 !important;
    }
    
    /* Improve button contrast and readability */
    .draggable-section button,
    .draggable-section .btn,
    .draggable-section a[class*="bg-"] {
        color: white !important;
        font-weight: 500 !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5) !important;
    }
    
    /* Specific button styling fixes */
    .draggable-section .bg-blue-600,
    .draggable-section .bg-blue-700,
    .draggable-section .bg-purple-600,
    .draggable-section .bg-purple-700,
    .draggable-section .bg-red-600,
    .draggable-section .bg-red-700,
    .draggable-section .bg-green-600,
    .draggable-section .bg-green-700 {
        color: white !important;
    }
    
    /* Fix gray buttons that might have poor contrast */
    .draggable-section .bg-gray-600,
    .draggable-section .bg-gray-700,
    .draggable-section .bg-gray-800 {
        color: white !important;
        background-color: #374151 !important;
    }
    
    .draggable-section .bg-gray-600:hover,
    .draggable-section .bg-gray-700:hover,
    .draggable-section .bg-gray-800:hover {
        background-color: #1f2937 !important;
    }
    
    /* Ensure all buttons in sections have good contrast */
    .draggable-section button {
        color: white !important;
        font-weight: 500 !important;
    }
    
    /* Fix any buttons that might be hard to read */
    .draggable-section .text-gray-400,
    .draggable-section .text-gray-500,
    .draggable-section .text-gray-600 {
        color: #d1d5db !important;
    }
    
    /* Ensure proper spacing for all sections */
    .grid-stack-item > * {
        margin-bottom: 0 !important;
    }
    
    /* Fix any potential z-index issues */
    #customizable-sections:not(.grid-stack) .grid-stack-item {
        z-index: auto !important;
        position: relative !important;
    }
    
    /* Ensure Tailwind space-y-6 works properly */
    #customizable-sections.space-y-6 > .grid-stack-item + .grid-stack-item {
        margin-top: 1.5rem !important;
    }
    
    /* Override any conflicting GridStack CSS */
    #customizable-sections:not(.grid-stack) {
        display: block !important;
    }
    
    #customizable-sections:not(.grid-stack) .grid-stack-item {
        display: block !important;
        float: none !important;
        clear: both !important;
    }
</style>
@endpush

@section('content')
@php
    // Hardcoded layout configuration for initial implementation
    $layout = [
        'profile-header',
        'streaming-schedule',
        'showcased-games',
        'recent-reviews',
        'recent-vods',
    ];
@endphp
<script>
    window.currentStreamerId = {{ $streamerProfile->id }};
    window.Laravel = {
        user: @json(auth()->user())
    };
</script>
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(auth()->check() && auth()->id() === $streamerProfile->user_id)
            <button id="customize-layout-btn" class="mb-6 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg transition-colors">Customize Layout</button>
        @endif
        <div class="space-y-6" id="customizable-sections">
            @foreach ($layout as $section)
                <div class="draggable-section" data-section="{{ $section }}" id="section-{{ $section }}">
                    @switch($section)
                        @case('profile-header')
                            <x-streamer.profile-header :streamer-profile="$streamerProfile" />
                            @break
                        @case('streaming-schedule')
                            <x-streamer.streaming-schedule :streamer-profile="$streamerProfile" />
                            @break
                        @case('showcased-games')
                            <x-streamer.showcased-games :streamer-profile="$streamerProfile" />
                            @break
                        @case('recent-reviews')
                            <x-streamer.recent-reviews :streamer-profile="$streamerProfile" />
                            @break
                        @case('recent-vods')
                            <x-streamer.recent-vods :streamer-profile="$streamerProfile" />
                            @break
                    @endswitch
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Simple Customize Modal -->
<div id="customize-layout-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden" role="dialog" aria-modal="true" aria-labelledby="customizeLayoutTitle">
    <div class="bg-zinc-900 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 border border-zinc-700">
        <h2 id="customizeLayoutTitle" class="text-xl font-bold mb-6 text-white">Customize Your Profile</h2>
        
        <!-- Section Controls -->
        <div class="mb-6">
            <h3 class="text-white font-semibold mb-3">Choose what to show on your profile</h3>
            <div class="space-y-2">
                <label class="flex items-center p-2 hover:bg-zinc-800 rounded">
                    <input type="checkbox" class="section-toggle mr-3" value="profile-header" checked>
                    <span class="text-white">Profile Header</span>
                </label>
                <label class="flex items-center p-2 hover:bg-zinc-800 rounded">
                    <input type="checkbox" class="section-toggle mr-3" value="streaming-schedule" checked>
                    <span class="text-white">Streaming Schedule</span>
                </label>
                <label class="flex items-center p-2 hover:bg-zinc-800 rounded">
                    <input type="checkbox" class="section-toggle mr-3" value="showcased-games" checked>
                    <span class="text-white">Showcased Games</span>
                </label>
                <label class="flex items-center p-2 hover:bg-zinc-800 rounded">
                    <input type="checkbox" class="section-toggle mr-3" value="recent-reviews" checked>
                    <span class="text-white">Recent Reviews</span>
                </label>
                <label class="flex items-center p-2 hover:bg-zinc-800 rounded">
                    <input type="checkbox" class="section-toggle mr-3" value="recent-vods" checked>
                    <span class="text-white">Recent VODs</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button id="close-customize-layout" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Done</button>
        </div>
    </div>
</div>

<!-- Notification region for screen readers -->
<div id="notification-region" aria-live="polite" class="sr-only"></div>

@push('scripts')
@vite('resources/js/streamer-profile.js')
@endpush

<!-- Twitch Embed Modal -->
<div class="modal fade" id="twitchEmbedModal" tabindex="-1" role="dialog" aria-labelledby="twitchEmbedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-2xl">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold mb-0" id="twitchEmbedModalLabel">
                            Watch VOD
                        </h5>
                        <p class="text-purple-100 text-sm mb-0" id="vodTitleDisplay">
                            Loading...
                        </p>
                    </div>
                </div>
                <button type="button" class="text-white hover:text-purple-200 transition-colors p-2 hover:bg-white/10 rounded-lg" data-dismiss="modal" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="aspect-video bg-black">
                    <iframe id="twitchEmbed" 
                            src="" 
                            height="100%" 
                            width="100%" 
                            allowfullscreen="true" 
                            scrolling="no" 
                            frameborder="0">
                    </iframe>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer bg-zinc-800 border-t border-zinc-600 px-6 py-4">
                <div class="flex items-center justify-between w-full">
                    <div class="text-zinc-300 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Embedded Twitch player - Full screen available
                    </div>
                    <div class="flex gap-3">
                        <button type="button" 
                                class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors" 
                                data-dismiss="modal">
                            Close
                        </button>
                        <a id="openTwitchLink" 
                           href="#" 
                           target="_blank" 
                           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Open on Twitch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
