@extends('layouts.app')

@push('styles')
<style>
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-zinc-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-8">
            <!-- Profile Header -->
            <x-streamer.profile-header :streamer-profile="$streamerProfile" />
                
            <!-- Streaming Schedule -->
            <x-streamer.streaming-schedule :streamer-profile="$streamerProfile" />

            <!-- Recent Reviews -->
            <x-streamer.recent-reviews :streamer-profile="$streamerProfile" />
            
            <!-- Sidebar with Recent VODs -->
            <div class="max-w-md mx-auto">
                <x-streamer.recent-vods :streamer-profile="$streamerProfile" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/streamer-profile.js') }}"></script>
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