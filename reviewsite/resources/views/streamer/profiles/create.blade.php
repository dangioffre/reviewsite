@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#151515] py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">
                Create a Streamer Page
            </h1>
            <p class="text-[#A1A1AA] text-lg font-['Inter']">
                Connect your streaming platform to create your streamer profile
            </p>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-500/20 to-green-600/20 border border-green-500/30 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-100 font-['Inter']">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-500/20 to-red-600/20 border border-red-500/30 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-100 font-['Inter']">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(!auth()->user()->streamerProfile)
            <!-- Step 1: Platform Connection -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white mb-2 font-['Share_Tech_Mono']">
                        Step 1: Connect Your Platform
                    </h2>
                    <p class="text-[#A1A1AA] font-['Inter']">
                        Choose your primary streaming platform to get started. You can add additional platforms later.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Twitch -->
                    <div class="bg-gradient-to-br from-[#1A1A1B] to-[#27272A] rounded-xl border border-[#3F3F46] p-6 text-center hover:border-[#9146FF] transition-all duration-300 group">
                        <div class="mb-4">
                            <div class="w-16 h-16 bg-[#9146FF] rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Twitch</h3>
                            <p class="text-[#A1A1AA] text-sm font-['Inter']">Connect your Twitch channel</p>
                        </div>
                        <a href="{{ route('streamer.oauth.redirect', 'twitch') }}" 
                           class="inline-flex items-center px-6 py-3 bg-[#9146FF] text-white font-bold rounded-lg hover:bg-[#7C3AED] transition-colors font-['Inter']">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                            </svg>
                            Connect Twitch
                        </a>
                    </div>

                    <!-- YouTube -->
                    <div class="bg-gradient-to-br from-[#1A1A1B] to-[#27272A] rounded-xl border border-[#3F3F46] p-6 text-center hover:border-[#FF0000] transition-all duration-300 group">
                        <div class="mb-4">
                            <div class="w-16 h-16 bg-[#FF0000] rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">YouTube</h3>
                            <p class="text-[#A1A1AA] text-sm font-['Inter']">Connect your YouTube channel</p>
                        </div>
                        <a href="{{ route('streamer.oauth.redirect', 'youtube') }}" 
                           class="inline-flex items-center px-6 py-3 bg-[#FF0000] text-white font-bold rounded-lg hover:bg-[#CC0000] transition-colors font-['Inter']">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            Connect YouTube
                        </a>
                    </div>

                    <!-- Kick -->
                    <div class="bg-gradient-to-br from-[#1A1A1B] to-[#27272A] rounded-xl border border-[#3F3F46] p-6 text-center hover:border-[#53FC18] transition-all duration-300 group">
                        <div class="mb-4">
                            <div class="w-16 h-16 bg-[#53FC18] rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Kick</h3>
                            <p class="text-[#A1A1AA] text-sm font-['Inter']">Connect your Kick channel</p>
                        </div>
                        <a href="{{ route('streamer.oauth.redirect', 'kick') }}" 
                           class="inline-flex items-center px-6 py-3 bg-[#53FC18] text-black font-bold rounded-lg hover:bg-[#45D615] transition-colors font-['Inter']">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            Connect Kick
                        </a>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-gradient-to-r from-blue-500/20 to-blue-600/20 border border-blue-500/30 rounded-xl p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-400 mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-lg font-bold text-blue-100 mb-2 font-['Share_Tech_Mono']">What happens next?</h4>
                            <ul class="text-blue-100 space-y-1 font-['Inter']">
                                <li>• You'll be redirected to your platform to authorize the connection</li>
                                <li>• We'll import your basic channel information (name, bio, avatar)</li>
                                <li>• Your profile will be created and submitted for approval</li>
                                <li>• Once approved, you can post reviews and interact with followers</li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        @else
            <!-- Platform Connected -->
            <div class="bg-gradient-to-r from-green-500/20 to-green-600/20 border border-green-500/30 rounded-xl p-6 mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-bold text-green-100 font-['Share_Tech_Mono']">Platform Connected!</h3>
                        <p class="text-green-200 font-['Inter']">Your {{ ucfirst(auth()->user()->streamerProfile->platform) }} account is connected. Complete your profile setup below.</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Profile Setup -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white mb-2 font-['Share_Tech_Mono']">
                        Step 2: Complete Your Profile
                    </h2>
                </div>

                <form method="POST" action="{{ route('streamer.profiles.store') }}" class="space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Bio Section -->
                        <div class="space-y-4">
                            <div>
                                <label for="bio" class="block text-sm font-medium text-white mb-2 font-['Inter']">Bio/Description</label>
                                <textarea name="bio" id="bio" rows="4" maxlength="1000" 
                                          placeholder="Tell viewers about yourself and your content..."
                                          class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-[#2563EB] font-['Inter']">{{ old('bio', auth()->user()->streamerProfile->bio) }}</textarea>
                                <p class="text-xs text-[#A1A1AA] mt-1 font-['Inter']">Optional - Describe your streaming content and personality</p>
                                @error('bio')
                                    <p class="text-red-400 text-sm mt-1 font-['Inter']">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Profile Info -->
                        <div class="bg-gradient-to-br from-[#1A1A1B] to-[#27272A] rounded-xl border border-[#3F3F46] p-6">
                            <h3 class="text-lg font-bold text-white mb-4 font-['Share_Tech_Mono']">Current Profile Info</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-[#A1A1AA] font-['Inter']">Channel:</span>
                                    <span class="text-white font-['Inter']">{{ auth()->user()->streamerProfile->channel_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#A1A1AA] font-['Inter']">Platform:</span>
                                    <span class="text-white font-['Inter']">{{ ucfirst(auth()->user()->streamerProfile->platform) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#A1A1AA] font-['Inter']">Status:</span>
                                    @if(auth()->user()->streamerProfile->is_approved)
                                        <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-['Inter']">Approved</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-['Inter']">Pending Approval</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Streaming Schedule -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Streaming Schedule (Optional)</h3>
                        <p class="text-[#A1A1AA] mb-4 font-['Inter']">Help viewers know when to find you live</p>
                        
                        <div id="schedule-container" class="space-y-4">
                            <div class="schedule-item bg-[#1A1A1B] rounded-lg border border-[#3F3F46] p-4">
                                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                                    <div>
                                        <label class="block text-xs text-[#A1A1AA] mb-1 font-['Inter']">Day</label>
                                        <select name="schedules[0][day_of_week]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 text-sm font-['Inter']">
                                            <option value="">Select Day</option>
                                            <option value="1">Monday</option>
                                            <option value="2">Tuesday</option>
                                            <option value="3">Wednesday</option>
                                            <option value="4">Thursday</option>
                                            <option value="5">Friday</option>
                                            <option value="6">Saturday</option>
                                            <option value="0">Sunday</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-[#A1A1AA] mb-1 font-['Inter']">Start</label>
                                        <input type="time" name="schedules[0][start_time]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 text-sm font-['Inter']">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-[#A1A1AA] mb-1 font-['Inter']">End</label>
                                        <input type="time" name="schedules[0][end_time]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 text-sm font-['Inter']">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-[#A1A1AA] mb-1 font-['Inter']">Timezone</label>
                                        <select name="schedules[0][timezone]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 text-sm font-['Inter']">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern</option>
                                            <option value="America/Chicago">Central</option>
                                            <option value="America/Denver">Mountain</option>
                                            <option value="America/Los_Angeles">Pacific</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-[#A1A1AA] mb-1 font-['Inter']">Notes</label>
                                        <input type="text" name="schedules[0][notes]" placeholder="Optional" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 text-sm placeholder-[#A1A1AA] font-['Inter']">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="remove-schedule w-full px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors text-sm font-['Inter']">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="add-schedule" class="mt-4 px-4 py-2 bg-[#2563EB]/20 text-[#2563EB] rounded-lg hover:bg-[#2563EB]/30 transition-colors font-['Inter']">+ Add Schedule</button>
                    </div>

                    <!-- Social Links -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Social Links (Optional)</h3>
                        
                        <div id="social-links-container" class="space-y-4">
                            <div class="social-link-item bg-[#1A1A1B] rounded-lg border border-[#3F3F46] p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs text-[#A1A1AA] mb-1 font-['Inter']">Platform</label>
                                        <select name="social_links[0][platform]" class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 text-sm font-['Inter']">
                                            <option value="">Select Platform</option>
                                            <option value="twitter">Twitter</option>
                                            <option value="instagram">Instagram</option>
                                            <option value="discord">Discord</option>
                                            <option value="tiktok">TikTok</option>
                                            <option value="website">Website</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs text-[#A1A1AA] mb-1 font-['Inter']">URL</label>
                                        <input type="url" name="social_links[0][url]" placeholder="https://..." class="w-full rounded-lg border-[#3F3F46] bg-[#27272A] text-white p-2 text-sm placeholder-[#A1A1AA] font-['Inter']">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="remove-social-link w-full px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors text-sm font-['Inter']">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="add-social-link" class="mt-4 px-4 py-2 bg-[#2563EB]/20 text-[#2563EB] rounded-lg hover:bg-[#2563EB]/30 transition-colors font-['Inter']">+ Add Social Link</button>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-[#3F3F46]">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-white font-bold rounded-lg hover:from-[#1D4ED8] hover:to-[#2563EB] transition-all duration-300 font-['Inter']">
                            Complete Profile Setup
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors text-center font-['Inter']">
                            Skip for Now
                        </a>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let scheduleIndex = 1;
    let socialLinkIndex = 1;

    // Add schedule functionality
    document.getElementById('add-schedule')?.addEventListener('click', function() {
        const container = document.getElementById('schedule-container');
        const newSchedule = container.querySelector('.schedule-item').cloneNode(true);
        
        // Update input names
        newSchedule.querySelectorAll('input, select').forEach(input => {
            const name = input.name.replace(/\[\d+\]/, `[${scheduleIndex}]`);
            input.name = name;
            input.value = '';
        });
        
        container.appendChild(newSchedule);
        scheduleIndex++;
    });

    // Add social link functionality
    document.getElementById('add-social-link')?.addEventListener('click', function() {
        const container = document.getElementById('social-links-container');
        const newLink = container.querySelector('.social-link-item').cloneNode(true);
        
        // Update input names
        newLink.querySelectorAll('input, select').forEach(input => {
            const name = input.name.replace(/\[\d+\]/, `[${socialLinkIndex}]`);
            input.name = name;
            input.value = '';
        });
        
        container.appendChild(newLink);
        socialLinkIndex++;
    });

    // Remove schedule/social link functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-schedule')) {
            if (document.querySelectorAll('.schedule-item').length > 1) {
                e.target.closest('.schedule-item').remove();
            }
        }
        
        if (e.target.classList.contains('remove-social-link')) {
            if (document.querySelectorAll('.social-link-item').length > 1) {
                e.target.closest('.social-link-item').remove();
            }
        }
    });


});


</script>
@endsection
