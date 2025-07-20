@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#151515] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-2 font-['Share_Tech_Mono'] leading-tight">
                        Edit Profile
                    </h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">{{ $streamerProfile->channel_name }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('streamer.profile.manage-vods', $streamerProfile) }}" 
                       class="px-4 py-2 bg-[#2563EB] text-white rounded-lg hover:bg-[#1D4ED8] transition-colors font-['Inter'] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Manage VODs
                    </a>
                    <a href="{{ route('streamer.profile.manage-showcase', $streamerProfile) }}" 
                       class="px-4 py-2 bg-[#8B5CF6] text-white rounded-lg hover:bg-[#7C3AED] transition-colors font-['Inter'] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        Manage Games
                    </a>
                    <a href="{{ route('streamer.profile.show', $streamerProfile) }}" 
                       class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter'] flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Profile
                    </a>
                    <form id="delete-profile-form" action="{{ route('streamer.profile.destroy', $streamerProfile) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                onclick="confirmDelete()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-['Inter'] flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Profile Form -->
            <form method="POST" action="{{ route('streamer.profile.update', $streamerProfile) }}" class="space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Bio Section -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Profile Information</h2>
                    
                    <div>
                        <label for="bio" class="block text-sm font-medium text-white mb-2 font-['Inter']">Bio/Description</label>
                        <textarea name="bio" id="bio" rows="4" maxlength="1000" 
                                  placeholder="Tell viewers about yourself and your content..."
                                  class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-[#2563EB] font-['Inter']">{{ old('bio', $streamerProfile->bio) }}</textarea>
                        <p id="char-counter" class="text-xs text-[#A1A1AA] mt-1 font-['Inter']">{{ strlen($streamerProfile->bio ?? '') }}/1000 characters</p>
                        @error('bio')
                            <p class="text-red-400 text-sm mt-1 font-['Inter']">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            
            <!-- Live Status Control -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Live Status Control</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-4 font-['Inter']">Current Status</h3>
                        <div class="space-y-4">
                            @if($streamerProfile->isLive())
                                <div class="flex items-center">
                                    <span class="px-4 py-2 bg-red-500 text-white rounded-lg font-bold font-['Inter'] animate-pulse">
                                        <div class="w-2 h-2 bg-white rounded-full inline-block mr-2 animate-ping"></div>
                                        LIVE NOW
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <span class="px-4 py-2 bg-[#3F3F46] text-[#A1A1AA] rounded-lg font-bold font-['Inter']">
                                        <div class="w-2 h-2 bg-[#A1A1AA] rounded-full inline-block mr-2"></div>
                                        OFFLINE
                                    </span>
                                </div>
                            @endif
                            
                            @if($streamerProfile->manual_live_override !== null)
                                <div class="flex items-center text-blue-400 font-['Inter']">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Manual override active
                                </div>
                            @endif
                            
                            @if($streamerProfile->live_status_checked_at)
                                <div class="text-[#A1A1AA] text-sm font-['Inter']">
                                    Last checked: {{ $streamerProfile->live_status_checked_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold text-white mb-4 font-['Inter']">Manual Override</h3>
                        <p class="text-[#A1A1AA] text-sm mb-4 font-['Inter']">
                            Use manual override when automatic detection isn't working correctly.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" id="setLiveBtn" 
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-['Inter'] flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Set Live
                            </button>
                            <button type="button" id="setOfflineBtn" 
                                    class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter'] flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9l6 6m0-6l-6 6"></path>
                                </svg>
                                Set Offline
                            </button>
                            <button type="button" id="clearOverrideBtn" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-['Inter'] flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Auto Detect
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Streaming Schedule -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Streaming Schedule</h2>
                
                <div id="schedules-container" class="space-y-6">
                    @foreach($streamerProfile->schedules as $index => $schedule)
                        <div class="schedule-entry bg-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6">
                            <input type="hidden" name="schedules[{{ $index }}][id]" value="{{ $schedule->id }}">
                            
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Day</label>
                                    <select name="schedules[{{ $index }}][day_of_week]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                                        <option value="0" {{ $schedule->day_of_week == 0 ? 'selected' : '' }}>Sunday</option>
                                        <option value="1" {{ $schedule->day_of_week == 1 ? 'selected' : '' }}>Monday</option>
                                        <option value="2" {{ $schedule->day_of_week == 2 ? 'selected' : '' }}>Tuesday</option>
                                        <option value="3" {{ $schedule->day_of_week == 3 ? 'selected' : '' }}>Wednesday</option>
                                        <option value="4" {{ $schedule->day_of_week == 4 ? 'selected' : '' }}>Thursday</option>
                                        <option value="5" {{ $schedule->day_of_week == 5 ? 'selected' : '' }}>Friday</option>
                                        <option value="6" {{ $schedule->day_of_week == 6 ? 'selected' : '' }}>Saturday</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Start</label>
                                    <input type="time" name="schedules[{{ $index }}][start_time]" 
                                           value="{{ $schedule->start_time->format('H:i') }}"
                                           class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-2 font-['Inter']">End</label>
                                    <input type="time" name="schedules[{{ $index }}][end_time]" 
                                           value="{{ $schedule->end_time->format('H:i') }}"
                                           class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Timezone</label>
                                    <select name="schedules[{{ $index }}][timezone]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                                        <option value="America/New_York" {{ $schedule->timezone == 'America/New_York' ? 'selected' : '' }}>Eastern</option>
                                        <option value="America/Chicago" {{ $schedule->timezone == 'America/Chicago' ? 'selected' : '' }}>Central</option>
                                        <option value="America/Denver" {{ $schedule->timezone == 'America/Denver' ? 'selected' : '' }}>Mountain</option>
                                        <option value="America/Los_Angeles" {{ $schedule->timezone == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Notes</label>
                                    <input type="text" name="schedules[{{ $index }}][notes]" 
                                           value="{{ $schedule->notes }}" maxlength="255" placeholder="Optional"
                                           class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 placeholder-[#A1A1AA] font-['Inter']">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" class="remove-schedule w-full px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors font-['Inter']">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <button type="button" id="add-schedule" class="mt-4 px-4 py-2 bg-[#2563EB]/20 text-[#2563EB] rounded-lg hover:bg-[#2563EB]/30 transition-colors font-['Inter']">
                    + Add Schedule
                </button>
            </div>
            
            <!-- Social Links -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Social Links</h2>
                
                <div id="social-links-container" class="space-y-6">
                    @foreach($streamerProfile->socialLinks as $index => $link)
                        <div class="social-link-entry bg-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6">
                            <input type="hidden" name="social_links[{{ $index }}][id]" value="{{ $link->id }}">
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Platform</label>
                                    <input type="text" name="social_links[{{ $index }}][platform]" 
                                           value="{{ $link->platform }}" maxlength="50" placeholder="twitter"
                                           class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 placeholder-[#A1A1AA] font-['Inter']">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-white mb-2 font-['Inter']">URL</label>
                                    <input type="url" name="social_links[{{ $index }}][url]" 
                                           value="{{ $link->url }}" maxlength="500" placeholder="https://..."
                                           class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 placeholder-[#A1A1AA] font-['Inter']">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" class="remove-social-link w-full px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors font-['Inter']">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <button type="button" id="add-social-link" class="mt-4 px-4 py-2 bg-[#2563EB]/20 text-[#2563EB] rounded-lg hover:bg-[#2563EB]/30 transition-colors font-['Inter']">
                    + Add Social Link
                </button>
            </div>
            
            <!-- Form Actions -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-white font-bold rounded-lg hover:from-[#1D4ED8] hover:to-[#2563EB] transition-all duration-300 font-['Inter']">
                        Update Profile
                    </button>
                    <a href="{{ route('streamer.profile.show', $streamerProfile) }}" 
                       class="px-8 py-3 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors text-center font-['Inter']">
                        Cancel
                    </a>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Make confirmDelete globally available
window.confirmDelete = function() {
    Swal.fire({
        title: 'Delete Streamer Profile',
        text: 'Are you sure you want to delete your streamer profile? This action cannot be undone. Your reviews will be reassigned to your main account.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff',
        customClass: {
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg',
            cancelButton: 'bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete your streamer profile.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#1f2937',
                color: '#fff',
            });
            
            // Submit the form
            document.getElementById('delete-profile-form').submit();
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const setLiveBtn = document.getElementById('setLiveBtn');
    const setOfflineBtn = document.getElementById('setOfflineBtn');
    const clearOverrideBtn = document.getElementById('clearOverrideBtn');
    const profileId = {{ $streamerProfile->id }};
    

    // Set Live Status
    setLiveBtn.addEventListener('click', function() {
        setLiveStatus(true);
    });

    // Set Offline Status
    setOfflineBtn.addEventListener('click', function() {
        setLiveStatus(false);
    });

    // Clear Override
    clearOverrideBtn.addEventListener('click', function() {
        clearLiveStatusOverride();
    });

    function setLiveStatus(isLive) {
        const button = isLive ? setLiveBtn : setOfflineBtn;
        const originalText = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';

        fetch(`/streamers/${profileId}/live-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                is_live: isLive
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateLiveStatusDisplay(data.is_live, data.manual_override);
            } else {
                showNotification(data.error || 'Failed to update live status', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating live status:', error);
            showNotification('An error occurred while updating live status', 'error');
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }

    function clearLiveStatusOverride() {
        const originalText = clearOverrideBtn.innerHTML;
        
        clearOverrideBtn.disabled = true;
        clearOverrideBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Clearing...';

        fetch(`/streamers/${profileId}/live-status`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateLiveStatusDisplay(data.is_live, data.manual_override);
            } else {
                showNotification(data.error || 'Failed to clear override', 'error');
            }
        })
        .catch(error => {
            console.error('Error clearing override:', error);
            showNotification('An error occurred while clearing override', 'error');
        })
        .finally(() => {
            clearOverrideBtn.disabled = false;
            clearOverrideBtn.innerHTML = originalText;
        });
    }

    function updateLiveStatusDisplay(isLive, manualOverride) {
        const statusContainer = document.querySelector('.flex.items-center span');
        const overrideInfo = document.querySelector('.text-blue-400');
        
        if (isLive) {
            statusContainer.className = 'px-4 py-2 bg-red-500 text-white rounded-lg font-bold font-[\'Inter\'] animate-pulse';
            statusContainer.innerHTML = '<div class="w-2 h-2 bg-white rounded-full inline-block mr-2 animate-ping"></div>LIVE NOW';
        } else {
            statusContainer.className = 'px-4 py-2 bg-[#3F3F46] text-[#A1A1AA] rounded-lg font-bold font-[\'Inter\']';
            statusContainer.innerHTML = '<div class="w-2 h-2 bg-[#A1A1AA] rounded-full inline-block mr-2"></div>OFFLINE';
        }

        if (manualOverride !== null) {
            if (!overrideInfo) {
                const overrideDiv = document.createElement('div');
                overrideDiv.className = 'flex items-center text-blue-400 font-[\'Inter\']';
                overrideDiv.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>Manual override active';
                statusContainer.parentNode.parentNode.appendChild(overrideDiv);
            }
        } else {
            if (overrideInfo) {
                overrideInfo.remove();
            }
        }
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-2xl border max-w-sm transform transition-all duration-300 translate-x-full opacity-0 ${
            type === 'success' 
                ? 'bg-green-500/20 border-green-500/30 text-green-100' 
                : 'bg-red-500/20 border-red-500/30 text-red-100'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span class="font-['Inter']">${message}</span>
                <button type="button" class="ml-4 text-current hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
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
    
    // Add/Remove Schedule functionality
    let scheduleIndex = {{ count($streamerProfile->schedules) }};
    
    document.getElementById('add-schedule')?.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent any form submission
        e.stopPropagation(); // Stop event bubbling
        
        try {
            const container = document.getElementById('schedules-container');
            if (!container) {
                console.error('Schedules container not found');
                return;
            }
            
            const newSchedule = document.createElement('div');
            newSchedule.className = 'schedule-entry bg-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6';
            newSchedule.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Day</label>
                        <select name="schedules[${scheduleIndex}][day_of_week]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                            <option value="">Select Day</option>
                            <option value="0">Sunday</option>
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Start</label>
                        <input type="time" name="schedules[${scheduleIndex}][start_time]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">End</label>
                        <input type="time" name="schedules[${scheduleIndex}][end_time]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Timezone</label>
                        <select name="schedules[${scheduleIndex}][timezone]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 font-['Inter']">
                            <option value="America/New_York">Eastern</option>
                            <option value="America/Chicago">Central</option>
                            <option value="America/Denver">Mountain</option>
                            <option value="America/Los_Angeles">Pacific</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Notes</label>
                        <input type="text" name="schedules[${scheduleIndex}][notes]" maxlength="255" placeholder="Optional" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 placeholder-[#A1A1AA] font-['Inter']">
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-schedule w-full px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors font-['Inter']">Remove</button>
                    </div>
                </div>
            `;
            container.appendChild(newSchedule);
            scheduleIndex++;
            
            console.log('Schedule added successfully, new index:', scheduleIndex);
        } catch (error) {
            console.error('Error adding schedule:', error);
            showNotification('Failed to add schedule. Please try again.', 'error');
        }
    });

    // Add/Remove Social Link functionality
    let socialLinkIndex = {{ count($streamerProfile->socialLinks) }};
    
    document.getElementById('add-social-link')?.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent any form submission
        e.stopPropagation(); // Stop event bubbling
        
        try {
            const container = document.getElementById('social-links-container');
            if (!container) {
                console.error('Social links container not found');
                return;
            }
            
            const newLink = document.createElement('div');
            newLink.className = 'social-link-entry bg-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6';
            newLink.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">Platform</label>
                        <input type="text" name="social_links[${socialLinkIndex}][platform]" maxlength="50" placeholder="twitter" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 placeholder-[#A1A1AA] font-['Inter']">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-white mb-2 font-['Inter']">URL</label>
                        <input type="url" name="social_links[${socialLinkIndex}][url]" maxlength="500" placeholder="https://..." class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 placeholder-[#A1A1AA] font-['Inter']">
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-social-link w-full px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors font-['Inter']">Remove</button>
                    </div>
                </div>
            `;
            container.appendChild(newLink);
            socialLinkIndex++;
            
            console.log('Social link added successfully, new index:', socialLinkIndex);
        } catch (error) {
            console.error('Error adding social link:', error);
            showNotification('Failed to add social link. Please try again.', 'error');
        }
    });

    // Remove functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-schedule')) {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                const scheduleEntry = e.target.closest('.schedule-entry');
                if (scheduleEntry) {
                    scheduleEntry.remove();
                    console.log('Schedule removed successfully');
                }
            } catch (error) {
                console.error('Error removing schedule:', error);
                showNotification('Failed to remove schedule. Please try again.', 'error');
            }
        }
        
        if (e.target.classList.contains('remove-social-link')) {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                const socialLinkEntry = e.target.closest('.social-link-entry');
                if (socialLinkEntry) {
                    socialLinkEntry.remove();
                    console.log('Social link removed successfully');
                }
            } catch (error) {
                console.error('Error removing social link:', error);
                showNotification('Failed to remove social link. Please try again.', 'error');
            }
        }
    });
    
    // Delete profile functionality is now handled by the global confirmDelete function
});
</script>
@endpush
@endsection