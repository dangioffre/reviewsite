<!-- Streamer Management Navigation -->
<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-4 mb-6">
    <div class="flex flex-wrap items-center justify-center gap-2">
        <a href="{{ route('streamer.profile.edit', $streamerProfile) }}" 
           class="px-4 py-2 rounded-lg transition-all duration-200 font-['Inter'] text-sm font-medium flex items-center gap-2 {{ request()->routeIs('streamer.profile.edit') ? 'bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white shadow-lg' : 'bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Profile
        </a>
        
        <a href="{{ route('streamer.profile.manage-vods', $streamerProfile) }}" 
           class="px-4 py-2 rounded-lg transition-all duration-200 font-['Inter'] text-sm font-medium flex items-center gap-2 {{ request()->routeIs('streamer.profile.manage-vods') ? 'bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white shadow-lg' : 'bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Manage VODs
        </a>
        
        <a href="{{ route('streamer.profile.edit', $streamerProfile) }}#reviews-section" 
           class="px-4 py-2 rounded-lg transition-all duration-200 font-['Inter'] text-sm font-medium flex items-center gap-2 {{ request()->routeIs('streamer.profile.edit') ? 'bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white shadow-lg' : 'bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white' }}"
           id="manage-reviews-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Manage Reviews
        </a>
        
        <a href="{{ route('streamer.profile.show', $streamerProfile) }}" 
           class="px-4 py-2 rounded-lg transition-all duration-200 font-['Inter'] text-sm font-medium flex items-center gap-2 bg-[#2563EB] text-white hover:bg-[#1D4ED8]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            View Profile
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the edit page with reviews-section fragment
    if (window.location.hash === '#reviews-section') {
        const manageReviewsLink = document.getElementById('manage-reviews-link');
        if (manageReviewsLink) {
            // Remove the default active state from Edit Profile link
            const editProfileLink = manageReviewsLink.parentElement.querySelector('a[href*="edit"]:not([href*="#"])');
            if (editProfileLink) {
                editProfileLink.className = editProfileLink.className.replace('bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white shadow-lg', 'bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white');
            }
            
            // Add active state to Manage Reviews link
            manageReviewsLink.className = manageReviewsLink.className.replace('bg-[#3F3F46] text-[#A1A1AA] hover:bg-[#52525B] hover:text-white', 'bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white shadow-lg');
        }
    }
});
</script> 