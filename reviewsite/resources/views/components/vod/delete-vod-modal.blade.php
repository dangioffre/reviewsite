<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteVodModal" tabindex="-1" role="dialog" aria-labelledby="deleteVodModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-2xl">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-to-r from-red-600 to-red-700 text-white">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold font-['Share_Tech_Mono'] mb-0" id="deleteVodModalLabel">
                            Delete VOD
                        </h5>
                        <p class="text-red-100 text-sm font-['Inter'] mb-0">
                            This action cannot be undone
                        </p>
                    </div>
                </div>
                <button type="button" class="text-white hover:text-red-200 transition-colors p-2 hover:bg-white/10 rounded-lg" data-dismiss="modal" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2 font-['Inter']">
                            Are you sure you want to delete this VOD?
                        </h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mb-4" id="deleteVodTitle">
                            This VOD will be permanently removed from your collection.
                        </p>
                        <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-red-300 text-sm font-['Inter']">
                                    This action cannot be undone. The VOD will be permanently deleted.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer bg-[#1A1A1B] border-t border-[#3F3F46] px-6 py-4">
                <div class="flex justify-end gap-3 w-full">
                    <button type="button" 
                            class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter']" 
                            data-dismiss="modal">
                        Cancel
                    </button>
                    <form id="deleteVodForm" method="POST" action="" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-['Inter']">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete VOD
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 
