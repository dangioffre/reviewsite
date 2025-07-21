<!-- Add VOD Modal -->
<div class="modal fade" id="addVodModal" tabindex="-1" role="dialog" aria-labelledby="addVodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-2xl">
            <form method="POST" action="{{ route('streamer.profile.add-vod', $streamerProfile) }}">
                @csrf
                <!-- Modal Header -->
                <div class="modal-header bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] text-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-lg font-bold font-['Share_Tech_Mono'] mb-0" id="addVodModalLabel">
                                Add Manual VOD
                            </h5>
                            <p class="text-blue-100 text-sm font-['Inter'] mb-0">
                                Add a custom video to your collection
                            </p>
                        </div>
                    </div>
                    <button type="button" class="text-white hover:text-blue-200 transition-colors p-2 hover:bg-white/10 rounded-lg" data-dismiss="modal" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="modal-body p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Title Field -->
                        <div class="form-group">
                            <label for="title" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Title <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors @error('title') border-red-500 @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required maxlength="500"
                                       placeholder="e.g., Epic Gaming Session - Part 1">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('title')
                                <div class="flex items-center mt-2 text-red-400 text-sm font-['Inter']">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- VOD URL Field -->
                        <div class="form-group">
                            <label for="vod_url" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                VOD URL <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="url" 
                                       class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors @error('vod_url') border-red-500 @enderror" 
                                       id="vod_url" name="vod_url" value="{{ old('vod_url') }}" required maxlength="500"
                                       placeholder="https://kick.com/inourmomsbasement/clips/clip_01K0Q48QZSAKWPEWAN0Q9X31F8">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div id="url-status-icon">
                                        <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 p-3 bg-purple-500/10 border border-purple-500/20 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-purple-400 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                    </svg>
                                    <div class="text-purple-300 text-xs font-['Inter']">
                                        <div class="font-semibold mb-1">Supported Platforms:</div>
                                        <div class="space-y-2">
                                            <div>
                                                <div class="font-medium text-purple-200">Twitch (Embedded Viewing)</div>
                                                <div>VODs: <code class="bg-purple-900/30 px-1 rounded">https://www.twitch.tv/videos/1234567890</code></div>
                                                <div>Clips: <code class="bg-purple-900/30 px-1 rounded">https://www.twitch.tv/username/clip/ClipName-abc123</code></div>
                                            </div>
                                            <div>
                                                <div class="font-medium text-green-200">Kick (Embedded Viewing)</div>
                                                <div>Clips: <code class="bg-green-900/30 px-1 rounded">https://kick.com/username/clips/clip_01K0Q48QZSAKWPEWAN0Q9X31F8</code></div>
                                                <div>VODs: <code class="bg-green-900/30 px-1 rounded">https://kick.com/username/videos/12345</code></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('vod_url')
                                <div class="flex items-center mt-2 text-red-400 text-sm font-['Inter']">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Description Field -->
                        <div class="form-group">
                            <label for="description" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                Description
                                <span class="text-[#A1A1AA] text-sm ml-2 font-normal">(Optional)</span>
                            </label>
                            <textarea class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors resize-none @error('description') border-red-500 @enderror" 
                                      id="description" name="description" rows="4" maxlength="1000"
                                      placeholder="Describe what happens in this VOD, key moments, games played, etc.">{{ old('description') }}</textarea>
                            <div class="flex justify-between items-center mt-2">
                                @error('description')
                                    <div class="flex items-center text-red-400 text-sm font-['Inter']">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @else
                                    <div></div>
                                @enderror
                                <span class="text-[#A1A1AA] text-xs font-['Inter']">Max 1000 characters</span>
                            </div>
                        </div>

                        <!-- Thumbnail URL Field -->
                        <div class="form-group">
                            <label for="thumbnail_url" class="flex items-center text-white font-semibold mb-3 font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                </svg>
                                Thumbnail URL
                                <span class="text-[#A1A1AA] text-sm ml-2 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <input type="url" 
                                       class="w-full px-4 py-3 bg-[#1A1A1B] border-2 border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] font-['Inter'] focus:border-[#2563EB] focus:outline-none transition-colors @error('thumbnail_url') border-red-500 @enderror" 
                                       id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url') }}" maxlength="500"
                                       placeholder="https://example.com/thumbnail.jpg">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('thumbnail_url')
                                <div class="flex items-center mt-2 text-red-400 text-sm font-['Inter']">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="modal-footer bg-[#1A1A1B] border-t border-[#3F3F46] px-6 py-4">
                    <div class="flex items-center justify-between w-full">
                        <div class="text-[#A1A1AA] text-sm font-['Inter']">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Fields marked with * are required
                        </div>
                        <div class="flex gap-3">
                            <button type="button" 
                                    class="px-6 py-2.5 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-all font-['Inter'] font-medium border border-[#52525B] hover:border-[#6B7280]" 
                                    data-dismiss="modal">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] text-white rounded-lg hover:from-[#1D4ED8] hover:to-[#1E40AF] transition-all font-['Inter'] font-medium shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add VOD
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
