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
                        <h5 class="text-lg font-bold font-['Share_Tech_Mono'] mb-0" id="twitchEmbedModalLabel">
                            Watch VOD
                        </h5>
                        <p class="text-purple-100 text-sm font-['Inter'] mb-0" id="vodTitleDisplay">
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
            <div class="modal-footer bg-[#1A1A1B] border-t border-[#3F3F46] px-6 py-4">
                <div class="flex items-center justify-between w-full">
                    <div class="text-[#A1A1AA] text-sm font-['Inter']">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Embedded Twitch player - Full screen available
                    </div>
                    <div class="flex gap-3">
                        <button type="button" 
                                class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter']" 
                                data-dismiss="modal">
                            Close
                        </button>
                        <a id="openTwitchLink" 
                           href="#" 
                           target="_blank" 
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-['Inter']">
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
