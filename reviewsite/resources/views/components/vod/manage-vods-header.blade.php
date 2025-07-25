<!-- Header Section -->
<div class="glass-card rounded-2xl shadow-2xl p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white font-['Share_Tech_Mono'] mb-2 flex items-center">
                <svg class="w-6 h-6 mr-3 text-[#E53E3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Manage VODs
            </h1>
            <p class="text-[#A1A1AA] font-['Inter']">Manage your video content and import from {{ ucfirst($streamerProfile->platform) }}</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <button type="button" class="action-button px-4 py-2 text-white rounded-lg font-['Inter']" 
                    data-toggle="modal" data-target="#addVodModal">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Manual VOD
            </button>
            <form method="POST" action="{{ route('streamer.profile.import-vods', $streamerProfile) }}" class="inline">
                @csrf
                <button type="submit" class="action-button secondary px-4 py-2 text-white rounded-lg font-['Inter']">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Import from {{ ucfirst($streamerProfile->platform) }}
                </button>
            </form>
        </div>
    </div>
</div> 
