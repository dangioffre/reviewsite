class StreamerProfile {
    constructor() {
        this.initializeEventListeners();
        this.initializeTimezoneDisplay();
        this.initializeTwitchModal();
    }

    initializeEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeFollowButton();
        });
    }

    initializeTimezoneDisplay() {
        const userTimezoneEl = document.getElementById('user-timezone');
        if (userTimezoneEl) {
            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            userTimezoneEl.textContent = `Your timezone: ${userTimezone}`;
        }
    }

    initializeFollowButton() {
        const followBtn = document.getElementById('followBtn');
        if (!followBtn) return;

        this.loadFollowStatus(followBtn);
        
        followBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const profileId = followBtn.dataset.profileId;
            const isFollowing = followBtn.classList.contains('bg-red-600');
            
            if (isFollowing) {
                this.unfollowStreamer(profileId, followBtn);
            } else {
                this.followStreamer(profileId, followBtn);
            }
        });
    }

    async loadFollowStatus(followBtn) {
        const profileId = followBtn.dataset.profileId;
        
        try {
            const response = await fetch(`/streamer/follow/${profileId}/status`);
            const data = await response.json();
            this.updateFollowButton(followBtn, data.following, data.follower_count);
        } catch (error) {
            console.error('Error loading follow status:', error);
        }
    }

    async followStreamer(profileId, followBtn) {
        followBtn.disabled = true;
        
        try {
            const response = await fetch(`/streamer/follow/${profileId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.error) {
                this.showNotification(data.error, 'error');
            } else {
                this.updateFollowButton(followBtn, true, data.follower_count);
                this.showNotification(data.message || 'Successfully followed!', 'success');
            }
        } catch (error) {
            console.error('Error following streamer:', error);
            this.showNotification('An error occurred while following the streamer.', 'error');
        } finally {
            followBtn.disabled = false;
        }
    }

    async unfollowStreamer(profileId, followBtn) {
        if (!confirm('Are you sure you want to unfollow this streamer?')) {
            return;
        }
        
        followBtn.disabled = true;
        
        try {
            const response = await fetch(`/streamer/follow/${profileId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.error) {
                this.showNotification(data.error, 'error');
            } else {
                this.updateFollowButton(followBtn, false, data.follower_count);
                this.showNotification(data.message || 'Successfully unfollowed!', 'success');
            }
        } catch (error) {
            console.error('Error unfollowing streamer:', error);
            this.showNotification('An error occurred while unfollowing the streamer.', 'error');
        } finally {
            followBtn.disabled = false;
        }
    }

    updateFollowButton(followBtn, isFollowing, followerCount) {
        if (isFollowing) {
            followBtn.className = 'inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold shadow-lg';
            followBtn.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Unfollow
            `;
        } else {
            followBtn.className = 'inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-semibold shadow-lg';
            followBtn.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Follow
            `;
        }
        
        followBtn.title = `${followerCount} followers`;
    }

    initializeTwitchModal() {
        const twitchModal = document.getElementById('twitchEmbedModal');
        if (!twitchModal) return;

        const twitchEmbed = document.getElementById('twitchEmbed');
        const vodTitleDisplay = document.getElementById('vodTitleDisplay');
        const openTwitchLink = document.getElementById('openTwitchLink');
        const watchEmbedButtons = document.querySelectorAll('.watch-embed-btn');
        
        // Open modal handlers
        watchEmbedButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.openTwitchModal(button, twitchModal, twitchEmbed, vodTitleDisplay, openTwitchLink);
            });
        });
        
        // Close modal handlers
        const closeButtons = twitchModal.querySelectorAll('[data-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', () => this.closeTwitchModal(twitchModal, twitchEmbed));
        });
        
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && twitchModal.classList.contains('show')) {
                this.closeTwitchModal(twitchModal, twitchEmbed);
            }
        });
    }

    openTwitchModal(button, modal, embed, titleDisplay, linkElement) {
        const embedType = button.dataset.embedType;
        const vodId = button.dataset.vodId;
        const clipId = button.dataset.clipId;
        const vodTitle = button.dataset.vodTitle;
        const originalUrl = button.dataset.originalUrl;
        
        let embedUrl = '';
        let twitchUrl = originalUrl;
        
        if (embedType === 'video' && vodId) {
            embedUrl = `https://player.twitch.tv/?video=${vodId}&parent=${window.location.hostname}&autoplay=false`;
            twitchUrl = `https://www.twitch.tv/videos/${vodId}`;
        } else if (embedType === 'clip' && clipId) {
            embedUrl = `https://clips.twitch.tv/embed?clip=${clipId}&parent=${window.location.hostname}&autoplay=false`;
            twitchUrl = originalUrl;
        }
        
        embed.src = embedUrl;
        titleDisplay.textContent = vodTitle;
        linkElement.href = twitchUrl;
        
        const modalTitle = modal.querySelector('#twitchEmbedModalLabel');
        modalTitle.textContent = embedType === 'clip' ? 'Watch Clip' : 'Watch VOD';
        
        // Show modal
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        
        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'twitch-modal-backdrop';
        backdrop.addEventListener('click', () => this.closeTwitchModal(modal, embed));
        document.body.appendChild(backdrop);
    }

    closeTwitchModal(modal, embed) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        embed.src = '';
        
        const backdrop = document.getElementById('twitch-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-2xl border max-w-sm transform transition-all duration-300 translate-x-full opacity-0 ${
            type === 'success' 
                ? 'bg-green-600/20 border-green-500/30 text-green-100' 
                : 'bg-red-600/20 border-red-500/30 text-red-100'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span class="font-medium">${message}</span>
                <button type="button" class="ml-4 text-current hover:opacity-75 transition-opacity" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
        }, 100);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    // Global function for timezone conversion
    convertToLocalTime() {
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        
        // Find all schedule cards and convert times
        const scheduleCards = document.querySelectorAll('.bg-zinc-800\\/50');
        scheduleCards.forEach(card => {
            const timezoneText = card.querySelector('.text-zinc-400');
            if (timezoneText) {
                timezoneText.textContent = userTimezone;
            }
        });
        
        // Update converter button
        const converterBtn = document.querySelector('[onclick="convertToLocalTime()"]');
        if (converterBtn) {
            converterBtn.textContent = 'Show original times';
            converterBtn.onclick = () => location.reload();
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.streamerProfile = new StreamerProfile();
        // Make convertToLocalTime available globally for inline onclick handlers
        window.convertToLocalTime = () => window.streamerProfile.convertToLocalTime();
    });
} else {
    window.streamerProfile = new StreamerProfile();
    window.convertToLocalTime = () => window.streamerProfile.convertToLocalTime();
} 