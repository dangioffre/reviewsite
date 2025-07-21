document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modal = document.getElementById('addVodModal');
    const modalButtons = document.querySelectorAll('[data-target="#addVodModal"]');
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    
    console.log('DOM loaded - Modal setup starting');
    console.log('Modal element found:', modal ? 'YES' : 'NO');
    console.log('Number of modal buttons found:', modalButtons.length);
    
    // Open modal
    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Modal button clicked!');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modal-backdrop';
            document.body.appendChild(backdrop);
            
            // Close on backdrop click
            backdrop.addEventListener('click', closeModal);
        });
    });
    
    // Close modal
    function closeModal() {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        const backdrop = document.getElementById('modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });
    
    // Auto-fill functionality for Twitch URLs
    const vodUrlInput = document.getElementById('vod_url');
    const titleInput = document.getElementById('title');
    const thumbnailInput = document.getElementById('thumbnail_url');
    
    if (vodUrlInput && titleInput) {
        vodUrlInput.addEventListener('blur', function() {
            const url = this.value.trim();
            if (url && !titleInput.value.trim()) {
                extractTwitchInfo(url);
            }
        });
        
        vodUrlInput.addEventListener('paste', function() {
            // Small delay to let the paste complete
            setTimeout(() => {
                const url = this.value.trim();
                if (url && !titleInput.value.trim()) {
                    extractTwitchInfo(url);
                }
            }, 100);
        });
    }
    
    function extractTwitchInfo(url) {
        // Show loading indicator in form
        showFormLoadingState();
        
        // Extract clip information from URL
        let clipId = null;
        let videoId = null;
        let channelName = null;
        let platform = null;
        
        // Handle different Twitch URL formats
        if (url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/([^\/]+)\/clip\/([^\/\?]+)/)) {
            const matches = url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/([^\/]+)\/clip\/([^\/\?]+)/);
            channelName = matches[1];
            clipId = matches[2];
            platform = 'twitch';
        } else if (url.match(/clips\.twitch\.tv\/([^\/\?]+)/)) {
            const matches = url.match(/clips\.twitch\.tv\/([^\/\?]+)/);
            clipId = matches[1];
            platform = 'twitch';
        } else if (url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/videos\/(\d+)/)) {
            const matches = url.match(/(?:www\.|m\.|go\.)?twitch\.tv\/videos\/(\d+)/);
            videoId = matches[1];
            platform = 'twitch';
        }
        // Handle Kick URL formats
        else if (url.match(/kick\.com\/([^\/]+)\/clips\/([^\/\?]+)/)) {
            const matches = url.match(/kick\.com\/([^\/]+)\/clips\/([^\/\?]+)/);
            channelName = matches[1];
            clipId = matches[2];
            platform = 'kick';
        } else if (url.match(/kick\.com\/([^\/]+)\/videos\/(\d+)/)) {
            const matches = url.match(/kick\.com\/([^\/]+)\/videos\/(\d+)/);
            channelName = matches[1];
            videoId = matches[1];
            platform = 'kick';
        }
        
        let success = false;
        
        if (clipId && platform === 'twitch') {
            // Extract title from clip slug (clips often have descriptive names)
            const clipTitle = extractClipTitle(clipId);
            if (clipTitle) {
                titleInput.value = clipTitle;
                titleInput.dispatchEvent(new Event('input')); // Trigger any validation
                success = true;
                
                // Show success message
                showAutoFillMessage(`Auto-filled title from clip: "${clipTitle}"`);
            }
            
            // Show helpful guidance for getting thumbnails manually
            if (!thumbnailInput.value.trim()) {
                showThumbnailHint(clipId);
            }
        } else if (clipId && platform === 'kick') {
            // For Kick clips, create a basic title from the clip ID
            const clipTitle = `${channelName} - Kick Clip`;
            titleInput.value = clipTitle;
            titleInput.dispatchEvent(new Event('input'));
            success = true;
            showAutoFillMessage(`Auto-filled title: "${clipTitle}"`);
        } else if (videoId && platform === 'twitch') {
            // For Twitch VODs, create a basic title
            const vodTitle = channelName ? `${channelName} - VOD ${videoId}` : `Twitch VOD ${videoId}`;
            titleInput.value = vodTitle;
            success = true;
            showAutoFillMessage(`Auto-filled title: "${vodTitle}"`);
        } else if (videoId && platform === 'kick') {
            // For Kick VODs, create a basic title
            const vodTitle = `${channelName} - Kick VOD`;
            titleInput.value = vodTitle;
            success = true;
            showAutoFillMessage(`Auto-filled title: "${vodTitle}"`);
        }
        
        // Hide loading indicator and show result
        hideFormLoadingState(success);
    }
    
    function showFormLoadingState() {
        const statusIcon = document.getElementById('url-status-icon');
        if (statusIcon) {
            statusIcon.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        }
    }
    
    function hideFormLoadingState(success) {
        const statusIcon = document.getElementById('url-status-icon');
        if (statusIcon) {
            if (success) {
                statusIcon.innerHTML = `
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                `;
                // Reset to normal after 3 seconds
                setTimeout(() => {
                    statusIcon.innerHTML = `
                        <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    `;
                }, 3000);
            } else {
                statusIcon.innerHTML = `
                    <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                `;
            }
        }
    }
    
    function showAutoFillMessage(message) {
        // Create a small success message near the title field
        const titleField = titleInput.parentElement;
        const existingMessage = titleField.querySelector('.auto-fill-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageEl = document.createElement('div');
        messageEl.className = 'auto-fill-message mt-2 p-2 bg-green-500/10 border border-green-500/20 rounded text-green-300 text-xs font-[\'Inter\'] flex items-center';
        messageEl.innerHTML = `
            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            ${message}
        `;
        
        titleField.appendChild(messageEl);
        
        // Remove message after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.parentNode.removeChild(messageEl);
            }
        }, 5000);
    }
    
    function showThumbnailHint(clipId) {
        // Show a helpful hint about getting the thumbnail
        const thumbnailField = thumbnailInput.parentElement.parentElement;
        const existingHint = thumbnailField.querySelector('.thumbnail-hint');
        if (existingHint) {
            existingHint.remove();
        }
        
        const hintEl = document.createElement('div');
        hintEl.className = 'thumbnail-hint mt-2 p-3 bg-purple-500/10 border border-purple-500/20 rounded text-purple-300 text-xs font-[\'Inter\']';
        hintEl.innerHTML = `
            <div class="flex items-start">
                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                </svg>
                <div>
                    <div class="font-semibold mb-1">💡 Tip: Get the clip thumbnail</div>
                    <div class="mb-2">Visit the clip on Twitch, right-click the video thumbnail, and copy the image URL.</div>
                    <div class="mt-2">
                        <button type="button" class="text-purple-400 hover:text-purple-300 underline text-xs" onclick="window.open('https://www.twitch.tv/clip/${clipId}', '_blank')">
                            Open clip on Twitch →
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-purple-200">
                        <strong>How to get thumbnail:</strong><br>
                        1. Click the link above to open the clip<br>
                        2. Right-click on the video thumbnail<br>
                        3. Select "Copy image address" or "Copy image URL"<br>
                        4. Paste it in the thumbnail field above
                    </div>
                </div>
            </div>
        `;
        
        thumbnailField.appendChild(hintEl);
        
        // Remove hint after 10 seconds
        setTimeout(() => {
            if (hintEl.parentNode) {
                hintEl.parentNode.removeChild(hintEl);
            }
        }, 10000);
    }
    
    function extractClipTitle(clipSlug) {
        // Clip slugs often contain descriptive information
        // Format is usually: DescriptiveWords-RandomString
        const parts = clipSlug.split('-');
        if (parts.length > 1) {
            // Take everything except the last part (which is usually random)
            const titlePart = parts.slice(0, -1).join('-');
            // Convert camelCase/PascalCase to readable format
            return titlePart
                .replace(/([A-Z])/g, ' $1') // Add space before capital letters
                .replace(/^./, str => str.toUpperCase()) // Capitalize first letter
                .trim();
        }
        return clipSlug;
    }
    
    // Twitch Embed Modal functionality
    const twitchModal = document.getElementById('twitchEmbedModal');
    const twitchEmbed = document.getElementById('twitchEmbed');
    const vodTitleDisplay = document.getElementById('vodTitleDisplay');
    const openTwitchLink = document.getElementById('openTwitchLink');
    const watchEmbedButtons = document.querySelectorAll('.watch-embed-btn');
    
    // Kick embed modal elements
    const kickModal = document.getElementById('kickEmbedModal');
    const kickEmbed = document.getElementById('kickEmbed');
    const kickVodTitleDisplay = document.getElementById('kickVodTitleDisplay');
    const openKickLink = document.getElementById('openKickLink');
    
    // Open embed modal (handles both Twitch and Kick)
    watchEmbedButtons.forEach(button => {
        button.addEventListener('click', function() {
            const platform = this.dataset.platform || 'twitch'; // Default to twitch for backward compatibility
            const embedType = this.dataset.embedType;
            const vodId = this.dataset.vodId;
            const clipId = this.dataset.clipId;
            const channel = this.dataset.channel;
            const username = this.dataset.username;
            const vodTitle = this.dataset.vodTitle;
            const originalUrl = this.dataset.originalUrl;
            
            if (platform === 'twitch') {
                openTwitchEmbed(embedType, vodId, clipId, channel, vodTitle, originalUrl);
            } else if (platform === 'kick') {
                // For now, open Kick clips/videos in new tab since embedding is problematic
                window.open(originalUrl, '_blank');
            }
        });
    });
    
    function openTwitchEmbed(embedType, vodId, clipId, channel, vodTitle, originalUrl) {
        let embedUrl = '';
        let twitchUrl = originalUrl;
        
        // Set up the embed URL based on type
        if (embedType === 'video' && vodId) {
            embedUrl = `https://player.twitch.tv/?video=${vodId}&parent=${window.location.hostname}&autoplay=false`;
            twitchUrl = `https://www.twitch.tv/videos/${vodId}`;
        } else if (embedType === 'clip' && clipId) {
            embedUrl = `https://clips.twitch.tv/embed?clip=${clipId}&parent=${window.location.hostname}&autoplay=false`;
            twitchUrl = originalUrl;
        }
        
        // Set up the embed
        twitchEmbed.src = embedUrl;
        
        // Update modal content
        vodTitleDisplay.textContent = vodTitle;
        openTwitchLink.href = twitchUrl;
        
        // Update modal title based on type
        const modalTitle = document.querySelector('#twitchEmbedModalLabel');
        modalTitle.textContent = embedType === 'clip' ? 'Watch Clip' : 'Watch VOD';
        
        // Show modal
        twitchModal.style.display = 'block';
        twitchModal.classList.add('show');
        document.body.classList.add('modal-open');
        
        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'twitch-modal-backdrop';
        document.body.appendChild(backdrop);
        
        // Close on backdrop click
        backdrop.addEventListener('click', closeTwitchModal);
    }
    
    // Close Twitch modal
    function closeTwitchModal() {
        twitchModal.style.display = 'none';
        twitchModal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Clear the iframe to stop playback
        twitchEmbed.src = '';
        
        const backdrop = document.getElementById('twitch-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    // Close buttons for Twitch modal
    if (twitchModal) {
        const twitchCloseButtons = twitchModal.querySelectorAll('[data-dismiss="modal"]');
        twitchCloseButtons.forEach(button => {
            button.addEventListener('click', closeTwitchModal);
        });
    }
    
    // Close Kick modal
    function closeKickModal() {
        kickModal.style.display = 'none';
        kickModal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Clear the iframe to stop playback
        kickEmbed.src = '';
        
        const backdrop = document.getElementById('kick-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    // Close buttons for Kick modal
    if (kickModal) {
        const kickCloseButtons = kickModal.querySelectorAll('[data-dismiss="modal"]');
        kickCloseButtons.forEach(button => {
            button.addEventListener('click', closeKickModal);
        });
    }
    
    // Close modals on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (twitchModal && twitchModal.classList.contains('show')) {
                closeTwitchModal();
            }
            if (kickModal && kickModal.classList.contains('show')) {
                closeKickModal();
            }
            if (deleteModal && deleteModal.classList.contains('show')) {
                closeDeleteModal();
            }
        }
    });
    
    // Delete VOD Modal functionality
    const deleteModal = document.getElementById('deleteVodModal');
    const deleteVodForm = document.getElementById('deleteVodForm');
    const deleteVodTitle = document.getElementById('deleteVodTitle');
    const deleteVodButtons = document.querySelectorAll('.delete-vod-btn');
    
    // Open delete modal
    deleteVodButtons.forEach(button => {
        button.addEventListener('click', function() {
            const vodTitle = this.dataset.vodTitle;
            const deleteUrl = this.dataset.deleteUrl;
            
            // Update modal content
            deleteVodTitle.textContent = `"${vodTitle}" will be permanently removed from your collection.`;
            deleteVodForm.action = deleteUrl;
            
            // Show modal
            deleteModal.style.display = 'block';
            deleteModal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'delete-modal-backdrop';
            document.body.appendChild(backdrop);
            
            // Close on backdrop click
            backdrop.addEventListener('click', closeDeleteModal);
        });
    });
    
    // Close delete modal
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        deleteModal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        const backdrop = document.getElementById('delete-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    // Close buttons for delete modal
    if (deleteModal) {
        const deleteCloseButtons = deleteModal.querySelectorAll('[data-dismiss="modal"]');
        deleteCloseButtons.forEach(button => {
            button.addEventListener('click', closeDeleteModal);
        });
    }
});

// Function to auto-show modal on validation errors (called from blade template)
window.showModalOnErrors = function() {
    const modalButtons = document.querySelectorAll('[data-target="#addVodModal"]');
    if (modalButtons.length > 0) {
        modalButtons[0].click();
    }
}; 