import Sortable from 'sortablejs';

class StreamerProfile {
    constructor() {
        this.isCustomizeMode = false;
        this.sortable = null;
        this.initializeEventListeners();
        this.initializeTimezoneDisplay();
        this.initializeTwitchModal();
        this.initializeSimpleCustomization();
        this.loadSavedSettings();
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

    initializeSimpleCustomization() {
        const customizeBtn = document.getElementById('customize-layout-btn');
        
        if (customizeBtn) {
            customizeBtn.addEventListener('click', () => {
                if (this.isCustomizeMode) {
                    this.exitCustomizeMode();
                } else {
                    this.enterCustomizeMode();
                }
            });
        }
        
        // Initialize modal close functionality
        this.initializeModal();
        
        // Initialize simple section toggles
        this.initializeSectionToggles();
    }

    enterCustomizeMode() {
        this.isCustomizeMode = true;
        const customizeBtn = document.getElementById('customize-layout-btn');
        const container = document.getElementById('customizable-sections');
        
        // Update button
        if (customizeBtn) {
            customizeBtn.textContent = 'Exit Customize';
            customizeBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
            customizeBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        }
        
        // Add customize styling
        if (container) {
            container.classList.add('customize-active');
        }
        
        // Enable drag and drop
        this.enableSortable();
        
        // Add visual indicators
        const sections = document.querySelectorAll('.draggable-section');
        sections.forEach(section => {
            section.classList.add('customize-mode');
        });
        
        // Show instructions
        this.showInstructions();
    }

    exitCustomizeMode() {
        this.isCustomizeMode = false;
        const customizeBtn = document.getElementById('customize-layout-btn');
        const container = document.getElementById('customizable-sections');
        
        // Update button
        if (customizeBtn) {
            customizeBtn.textContent = 'Customize Layout';
            customizeBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
            customizeBtn.classList.add('bg-gray-700', 'hover:bg-gray-800');
        }
        
        // Remove customize styling
        if (container) {
            container.classList.remove('customize-active');
        }
        
        // Disable drag and drop
        this.disableSortable();
        
        // Remove visual indicators
        const sections = document.querySelectorAll('.draggable-section');
        sections.forEach(section => {
            section.classList.remove('customize-mode');
        });
        
        // Hide instructions
        this.hideInstructions();
    }

    enableSortable() {
        const container = document.getElementById('customizable-sections');
        if (!container) return;
        
        this.sortable = Sortable.create(container, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: () => {
                this.saveOrder();
            }
        });
    }

    disableSortable() {
        if (this.sortable) {
            this.sortable.destroy();
            this.sortable = null;
        }
    }

    showInstructions() {
        const customizeBtn = document.getElementById('customize-layout-btn');
        if (!customizeBtn) return;
        
        // Check if instructions already exist
        const existingInstructions = document.getElementById('customize-instructions');
        if (existingInstructions) return;
        
        // Create instructions
        const instructions = document.createElement('div');
        instructions.id = 'customize-instructions';
        instructions.className = 'mb-4 p-3 bg-blue-600/20 border border-blue-500/30 rounded-lg text-blue-100 text-sm';
        instructions.innerHTML = `
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Drag sections to reorder them. Use the settings button to show/hide sections.</span>
                <button onclick="document.getElementById('customize-layout-modal').classList.remove('hidden')" class="ml-2 px-2 py-1 bg-blue-600 hover:bg-blue-700 rounded text-xs">Settings</button>
            </div>
        `;
        
        customizeBtn.parentNode.insertBefore(instructions, customizeBtn.nextSibling);
    }

    hideInstructions() {
        const instructions = document.getElementById('customize-instructions');
        if (instructions) {
            instructions.remove();
        }
    }

    saveOrder() {
        const sections = document.querySelectorAll('#customizable-sections .draggable-section');
        const order = Array.from(sections).map(section => section.getAttribute('data-section'));
        
        localStorage.setItem('streamerPageLayout', JSON.stringify(order));
        this.showNotification('Layout saved!', 'success');
    }

    applyOrder(order) {
        const container = document.getElementById('customizable-sections');
        if (!container) return;
        
        const sectionMap = {};
        Array.from(container.children).forEach(child => {
            sectionMap[child.getAttribute('data-section')] = child;
        });
        
        order.forEach(section => {
            if (sectionMap[section]) {
                container.appendChild(sectionMap[section]);
            }
        });
    }

    initializeModal() {
        const modal = document.getElementById('customize-layout-modal');
        const closeBtn = document.getElementById('close-customize-layout');
        
        if (closeBtn && modal) {
            closeBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        }
        
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }
    }

    loadSavedSettings() {
        // Load saved order
        const savedOrder = localStorage.getItem('streamerPageLayout');
        if (savedOrder) {
            try {
                const order = JSON.parse(savedOrder);
                this.applyOrder(order);
            } catch (e) {
                console.error('Error loading saved order:', e);
            }
        }
        
        // Load saved visibility
        this.loadSectionVisibility();
    }

    initializeSectionToggles() {
        const checkboxes = document.querySelectorAll('.section-toggle');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const section = e.target.value;
                const sectionDiv = document.getElementById(`section-${section}`);
                
                if (sectionDiv) {
                    sectionDiv.style.display = e.target.checked ? '' : 'none';
                }
                
                // Save to localStorage
                this.saveSectionVisibility();
            });
        });
        
        // Load saved visibility state
        this.loadSectionVisibility();
    }

    // Removed preset functionality - keeping it simple

    // Removed theme functionality - keeping it simple

    saveSectionVisibility() {
        const visibility = {};
        document.querySelectorAll('.section-toggle').forEach(checkbox => {
            visibility[checkbox.value] = checkbox.checked;
        });
        localStorage.setItem('streamerPageSectionVisibility', JSON.stringify(visibility));
    }

    loadSectionVisibility() {
        const saved = localStorage.getItem('streamerPageSectionVisibility');
        let visibility = saved ? JSON.parse(saved) : {
            'profile-header': true,
            'streaming-schedule': true,
            'showcased-games': true,
            'recent-reviews': true,
            'recent-vods': true,
        };
        
        // Apply visibility
        Object.entries(visibility).forEach(([section, visible]) => {
            const sectionDiv = document.getElementById(`section-${section}`);
            if (sectionDiv) {
                sectionDiv.style.display = visible ? '' : 'none';
            }
            
            // Set checkbox state
            const checkbox = document.querySelector(`.section-toggle[value="${section}"]`);
            if (checkbox) {
                checkbox.checked = !!visible;
            }
        });
    }

    // Removed theme functionality

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-2xl border max-w-sm transform transition-all duration-300 translate-x-20 opacity-0` +
            (type === 'success'
                ? ' bg-green-600/20 border-green-500/30 text-green-100'
                : ' bg-red-600/20 border-red-500/30 text-red-100');
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success'
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'}
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
            notification.classList.remove('translate-x-20', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 50);
        
        // Auto-remove after 4 seconds
        setTimeout(() => {
            notification.classList.remove('translate-x-0', 'opacity-100');
            notification.classList.add('translate-x-20', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }

    enableCustomizeMode() {
        this.isCustomizeMode = true;
        
        const container = document.getElementById('customizable-sections');
        if (container) {
            container.classList.add('customize-active');
        }
        
        // Initialize simple drag and drop
        this.initializeSimpleDragDrop();
        
        // Add visual indicators for customize mode
        const sections = document.querySelectorAll('.draggable-section');
        sections.forEach(section => {
            section.classList.add('customize-mode');
        });
        
        // Show save/reset buttons
        this.showCustomizeControls();
    }

    disableCustomizeMode() {
        this.isCustomizeMode = false;
        
        const container = document.getElementById('customizable-sections');
        if (container) {
            container.classList.remove('customize-active');
        }
        
        // Remove visual indicators and reset drag functionality
        const sections = document.querySelectorAll('.draggable-section');
        sections.forEach(section => {
            section.classList.remove('customize-mode');
            section.draggable = false;
            section.style.opacity = '';
            
            // Remove event listeners
            section.removeEventListener('dragstart', this.handleDragStart);
            section.removeEventListener('dragover', this.handleDragOver);
            section.removeEventListener('drop', this.handleDrop);
            section.removeEventListener('dragend', this.handleDragEnd);
        });
        
        // Clean up any remaining visual indicators
        document.querySelectorAll('.drag-over-top, .drag-over-bottom').forEach(el => {
            el.classList.remove('drag-over-top', 'drag-over-bottom');
        });
        
        // Hide save/reset buttons
        this.hideCustomizeControls();
    }

    showCustomizeControls() {
        const customizeBtn = document.getElementById('customize-layout-btn');
        if (!customizeBtn) return;
        
        // Remove any existing buttons first to prevent duplicates
        this.removeExistingButtons();
        
        // Create button container
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'flex gap-2 mb-4';
        buttonContainer.id = 'customize-controls';
        
        // Create save and reset buttons
        this.saveButton = this.createSaveButton();
        this.resetButton = this.createResetButton();
        
        // Add buttons to container
        buttonContainer.appendChild(this.saveButton);
        buttonContainer.appendChild(this.resetButton);
        
        // Insert container after customize button
        customizeBtn.parentNode.insertBefore(buttonContainer, customizeBtn.nextSibling);
        
        // Change customize button text and style
        customizeBtn.textContent = 'Exit Customize';
        customizeBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        customizeBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
    }

    removeExistingButtons() {
        // Remove any existing control buttons
        const existingControls = document.getElementById('customize-controls');
        if (existingControls) {
            existingControls.remove();
        }
        
        // Reset button references
        this.saveButton = null;
        this.resetButton = null;
    }

    hideCustomizeControls() {
        const customizeBtn = document.getElementById('customize-layout-btn');
        if (!customizeBtn) return;
        
        // Remove the entire control container
        this.removeExistingButtons();
        
        // Reset customize button text and style
        customizeBtn.textContent = 'Customize Layout';
        customizeBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        customizeBtn.classList.add('bg-gray-700', 'hover:bg-gray-800');
    }

    createSaveButton() {
        const btn = document.createElement('button');
        btn.textContent = 'Save Layout';
        btn.className = 'mb-4 mr-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors';
        btn.onclick = () => this.saveSectionOrder();
        return btn;
    }

    createResetButton() {
        const btn = document.createElement('button');
        btn.textContent = 'Reset Layout';
        btn.className = 'mb-4 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors';
        btn.onclick = () => this.resetSectionOrder();
        return btn;
    }

    async saveSectionOrder() {
        // Get current order from DOM
        const sections = document.querySelectorAll('#customizable-sections .draggable-section');
        const order = Array.from(sections).map(section => section.getAttribute('data-section'));
        
        const visibility = this.getCurrentVisibility();
        const theme = this.getCurrentTheme();
        const layout = { order, visibility, theme };
        
        if (this.saveButton) {
            this.saveButton.disabled = true;
            this.saveButton.textContent = 'Saving...';
            this.saveButton.style.opacity = '0.7';
            this.saveButton.style.cursor = 'not-allowed';
            this.saveButton.style.display = 'none';
        }
        
        if (this.resetButton) {
            this.resetButton.style.display = 'none';
        }
        
        let success = true;
        
        if (window.currentStreamerId && window.Laravel && window.Laravel.user) {
            // User is logged in, save to backend
            try {
                const resp = await fetch('/api/streamer-layout/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        streamer_id: window.currentStreamerId,
                        layout
                    })
                });
                
                if (!resp.ok) throw new Error('Failed to save');
                this.showNotification('Layout saved!', 'success');
            } catch (e) {
                success = false;
                this.showNotification('Error saving layout. Please try again.', 'error');
            }
        } else {
            // Fallback to local storage
            try {
                localStorage.setItem('streamerPageLayout', JSON.stringify(order));
                localStorage.setItem('streamerPageSectionVisibility', JSON.stringify(visibility));
                localStorage.setItem('streamerPageTheme', theme);
                this.showNotification('Layout saved locally!', 'success');
            } catch (e) {
                success = false;
                this.showNotification('Error saving layout locally.', 'error');
            }
        }
        
        if (this.saveButton) {
            this.saveButton.disabled = false;
            this.saveButton.textContent = 'Save Layout';
            this.saveButton.style.opacity = '';
            this.saveButton.style.cursor = '';
        }
        
        return success;
    }

    async resetSectionOrder() {
        let success = true;
        
        if (window.currentStreamerId && window.Laravel && window.Laravel.user) {
            // User is logged in, reset to default and save to backend
            const layout = {
                order: this.defaultLayout,
                visibility: {
                    'profile-header': true,
                    'streaming-schedule': true,
                    'showcased-games': true,
                    'recent-reviews': true,
                    'recent-vods': true,
                },
                theme: 'dark',
            };
            
            try {
                await fetch('/api/streamer-layout/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        streamer_id: window.currentStreamerId,
                        layout
                    })
                });
                this.showNotification('Layout reset!', 'success');
            } catch (e) {
                success = false;
                this.showNotification('Error resetting layout. Please try again.', 'error');
            }
            
            this.applyLayout(layout.order);
            this.applyVisibility(layout.visibility);
            this.applyTheme(layout.theme, {
                dark: 'theme-dark',
                light: 'theme-light',
                neon: 'theme-neon',
            });
        } else {
            // Fallback to local storage
            try {
                localStorage.removeItem('streamerPageLayout');
                localStorage.removeItem('streamerPageSectionVisibility');
                localStorage.removeItem('streamerPageTheme');
                this.showNotification('Layout reset locally!', 'success');
            } catch (e) {
                success = false;
                this.showNotification('Error resetting layout locally.', 'error');
            }
            
            this.applyLayout(this.defaultLayout);
            this.applyVisibility({
                'profile-header': true,
                'streaming-schedule': true,
                'showcased-games': true,
                'recent-reviews': true,
                'recent-vods': true,
            });
            this.applyTheme('dark', {
                dark: 'theme-dark',
                light: 'theme-light',
                neon: 'theme-neon',
            });
        }
        
        if (this.saveButton) {
            this.saveButton.style.display = 'none';
        }
        if (this.resetButton) {
            this.resetButton.style.display = 'none';
        }
        
        return success;
    }

    async applySavedLayout() {
        this.showSpinner();
        let layout = null;
        let error = false;
        
        if (window.currentStreamerId && window.Laravel && window.Laravel.user) {
            // User is logged in, fetch from backend
            try {
                const resp = await fetch('/api/streamer-layout/get', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ streamer_id: window.currentStreamerId })
                });
                layout = await resp.json();
            } catch (e) {
                layout = null;
                error = true;
            }
        }
        
        if (!layout) {
            // Fallback to local storage
            const order = JSON.parse(localStorage.getItem('streamerPageLayout') || 'null');
            const visibility = JSON.parse(localStorage.getItem('streamerPageSectionVisibility') || 'null');
            const theme = localStorage.getItem('streamerPageTheme') || 'dark';
            layout = { order, visibility, theme };
        }
        
        // Apply layout
        this.applyLayout(layout.order || this.defaultLayout);
        this.applyVisibility(layout.visibility || {
            'profile-header': true,
            'streaming-schedule': true,
            'showcased-games': true,
            'recent-reviews': true,
            'recent-vods': true,
        });
        this.applyTheme(layout.theme || 'dark', {
            dark: 'theme-dark',
            light: 'theme-light',
            neon: 'theme-neon',
        });
        
        // Store order for when customize mode is enabled
        if (layout.order) {
            this.savedOrder = layout.order;
        }
        
        this.hideSpinner();
        
        if (error) {
            this.showNotification('Could not load your layout from the server. Using local or default settings.', 'error');
        }
    }

    applyLayout(order) {
        const container = document.getElementById('customizable-sections');
        if (!container) return;
        
        const sectionMap = {};
        Array.from(container.children).forEach(child => {
            sectionMap[child.getAttribute('data-section')] = child;
        });
        
        order.forEach(section => {
            if (sectionMap[section]) {
                container.appendChild(sectionMap[section]);
            }
        });
    }

    initializeSectionVisibility() {
        const customizeBtn = document.getElementById('customize-layout-btn');
        const modal = document.getElementById('customize-layout-modal');
        const closeBtn = document.getElementById('close-customize-layout');
        const form = document.getElementById('section-visibility-form');
        
        if (!customizeBtn || !modal || !closeBtn || !form) return;
        
        const checkboxes = form.querySelectorAll('.section-toggle');
        const presetBtns = document.querySelectorAll('.preset-btn');
        const themeRadios = document.querySelectorAll('.theme-radio');
        const themeClassMap = {
            dark: 'theme-dark',
            light: 'theme-light',
            neon: 'theme-neon',
        };

        // Preset definitions
        const presets = {
            classic: {
                order: [
                    'profile-header',
                    'streaming-schedule',
                    'showcased-games',
                    'recent-reviews',
                    'recent-vods',
                ],
                visibility: {
                    'profile-header': true,
                    'streaming-schedule': true,
                    'showcased-games': true,
                    'recent-reviews': true,
                    'recent-vods': true,
                }
            },
            compact: {
                order: [
                    'profile-header',
                    'showcased-games',
                    'recent-reviews',
                ],
                visibility: {
                    'profile-header': true,
                    'streaming-schedule': false,
                    'showcased-games': true,
                    'recent-reviews': true,
                    'recent-vods': false,
                }
            },
            showcase: {
                order: [
                    'profile-header',
                    'showcased-games',
                    'streaming-schedule',
                    'recent-vods',
                ],
                visibility: {
                    'profile-header': true,
                    'streaming-schedule': true,
                    'showcased-games': true,
                    'recent-reviews': false,
                    'recent-vods': true,
                }
            }
        };

        // Preset button logic
        presetBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const preset = presets[btn.dataset.preset];
                if (!preset) return;
                
                // Update order
                this.applyLayout(preset.order);
                localStorage.setItem('streamerPageLayout', JSON.stringify(preset.order));
                
                // Update visibility
                Object.entries(preset.visibility).forEach(([section, visible]) => {
                    const sectionDiv = document.getElementById(`section-${section}`);
                    if (sectionDiv) {
                        sectionDiv.style.display = visible ? '' : 'none';
                    }
                    // Set checkbox state
                    const checkbox = form.querySelector(`.section-toggle[value="${section}"]`);
                    if (checkbox) {
                        checkbox.checked = !!visible;
                    }
                });
                localStorage.setItem('streamerPageSectionVisibility', JSON.stringify(preset.visibility));
            });
        });

        // Open modal or toggle customize mode
        customizeBtn.addEventListener('click', () => {
            console.log('Customize Layout button clicked');
            
            if (this.isCustomizeMode) {
                // Exit customize mode
                this.disableCustomizeMode();
            } else {
                // Enter customize mode
                this.enableCustomizeMode();
                // Also open the modal for settings
                modal.classList.remove('hidden');
            }
        });
        
        // Close modal
        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
        
        // Hide modal on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Load saved visibility state
        const saved = localStorage.getItem('streamerPageSectionVisibility');
        let visibility = saved ? JSON.parse(saved) : {
            'profile-header': true,
            'streaming-schedule': true,
            'showcased-games': true,
            'recent-reviews': true,
            'recent-vods': true,
        };
        
        // Apply visibility on page load
        Object.entries(visibility).forEach(([section, visible]) => {
            const sectionDiv = document.getElementById(`section-${section}`);
            if (sectionDiv) {
                sectionDiv.style.display = visible ? '' : 'none';
            }
            // Set checkbox state
            const checkbox = form.querySelector(`.section-toggle[value="${section}"]`);
            if (checkbox) {
                checkbox.checked = !!visible;
            }
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('streamerPageTheme') || 'dark';
        this.applyTheme(savedTheme, themeClassMap);
        // Set radio state
        themeRadios.forEach(radio => {
            radio.checked = radio.value === savedTheme;
        });

        // Listen for theme changes
        themeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.applyTheme(e.target.value, themeClassMap);
                    localStorage.setItem('streamerPageTheme', e.target.value);
                    this.showNotification('Theme changed!', 'success');
                    this.markUnsaved();
                }
            });
        });

        // Listen for checkbox changes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const section = e.target.value;
                const sectionDiv = document.getElementById(`section-${section}`);
                if (sectionDiv) {
                    sectionDiv.style.display = e.target.checked ? '' : 'none';
                }
                visibility[section] = e.target.checked;
                localStorage.setItem('streamerPageSectionVisibility', JSON.stringify(visibility));
                this.markUnsaved();
            });
        });
        
        // Mark unsaved on drag-and-drop
        if (this.grid) {
            this.grid.on('change', () => {
                this.markUnsaved();
            });
        }
    }

    applyTheme(theme, themeClassMap) {
        // Apply theme only to the streamer content area, not the entire page
        const contentArea = document.getElementById('customizable-sections');
        if (!contentArea) return;
        
        // Remove all theme classes from content area
        Object.values(themeClassMap).forEach(cls => contentArea.classList.remove(cls));
        // Add selected theme class to content area only
        contentArea.classList.add(themeClassMap[theme] || themeClassMap.dark);
    }

    applyVisibility(visibility) {
        Object.entries(visibility).forEach(([section, visible]) => {
            const sectionDiv = document.getElementById(`section-${section}`);
            if (sectionDiv) {
                sectionDiv.style.display = visible ? '' : 'none';
            }
            // Set checkbox state
            const checkbox = document.querySelector(`.section-toggle[value="${section}"]`);
            if (checkbox) {
                checkbox.checked = !!visible;
            }
        });
    }

    getCurrentVisibility() {
        const visibility = {};
        document.querySelectorAll('.section-toggle').forEach(checkbox => {
            visibility[checkbox.value] = checkbox.checked;
        });
        return visibility;
    }

    getCurrentTheme() {
        const checked = document.querySelector('.theme-radio:checked');
        return checked ? checked.value : 'dark';
    }

    markUnsaved() {
        if (this.saveButton) {
            this.saveButton.disabled = false;
            this.saveButton.style.opacity = '';
            this.saveButton.style.cursor = '';
        }
    }

    showSpinner() {
        if (document.getElementById('layout-spinner')) return;
        const spinner = document.createElement('div');
        spinner.id = 'layout-spinner';
        spinner.style.position = 'fixed';
        spinner.style.top = 0;
        spinner.style.left = 0;
        spinner.style.width = '100vw';
        spinner.style.height = '100vh';
        spinner.style.background = 'rgba(0,0,0,0.5)';
        spinner.style.zIndex = 9999;
        spinner.style.display = 'flex';
        spinner.style.alignItems = 'center';
        spinner.style.justifyContent = 'center';
        spinner.innerHTML = '<div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>';
        document.body.appendChild(spinner);
    }

    hideSpinner() {
        const spinner = document.getElementById('layout-spinner');
        if (spinner) spinner.remove();
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-2xl border max-w-sm transform transition-all duration-300 translate-x-20 opacity-0 notification-slide` +
            (type === 'success'
                ? ' bg-green-600/20 border-green-500/30 text-green-100'
                : ' bg-red-600/20 border-red-500/30 text-red-100');
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success'
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'}
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
            notification.classList.remove('translate-x-20', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 50);
        
        // Auto-remove after 4 seconds
        setTimeout(() => {
            notification.classList.remove('translate-x-0', 'opacity-100');
            notification.classList.add('translate-x-20', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('customizable-sections')) {
        new StreamerProfile();
    }
});

// Also initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
    // DOM is still loading
} else {
    // DOM is already loaded
    if (document.getElementById('customizable-sections')) {
        new StreamerProfile();
    }
}