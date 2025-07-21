<x-layouts.app>
    <div class="min-h-screen bg-[#151515] py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    @if($product->type === 'game')
                        <a href="{{ route('games.show', $product) }}" 
                           class="inline-flex items-center text-[#A1A1AA] hover:text-[#E53E3E] transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to {{ $product->name }}
                        </a>
                    @else
                        <a href="{{ route('tech.show', $product) }}" 
                           class="inline-flex items-center text-[#A1A1AA] hover:text-[#E53E3E] transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to {{ $product->name }}
                        </a>
                    @endif
                </div>
                <h1 class="text-4xl font-bold text-white mb-4 font-['Share_Tech_Mono']">
                    @if(isset($existingReview))
                        Upgrade Your Rating to Full Review
                    @else
                        Write a Review
                    @endif
                </h1>
                <p class="text-[#A1A1AA] font-['Inter']">
                    @if(isset($existingReview))
                        You've already rated this {{ $product->type }} with stars. Now add your detailed thoughts to create a full review!
                    @else
                        Share your thoughts about {{ $product->name }}
                    @endif
                </p>
            </div>

            <!-- Product Info -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6 mb-8">
                <div class="flex items-center gap-4">
                    <img src="{{ $product->image ?? 'https://via.placeholder.com/80x60/27272A/A1A1AA?text=No+Image' }}" 
                         alt="{{ $product->name }}" 
                         class="w-16 h-12 object-cover rounded-lg">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white font-['Inter']">{{ $product->name }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm text-[#A1A1AA] font-['Inter']">{{ ucfirst($product->type) }}</span>
                            @if($product->platform)
                                <span class="text-sm text-[#A1A1AA]">•</span>
                                <span class="text-sm text-[#A1A1AA] font-['Inter']">{{ $product->platform->name }}</span>
                            @endif
                            @if($product->genre)
                                <span class="text-sm text-[#A1A1AA]">•</span>
                                <span class="text-sm text-[#A1A1AA] font-['Inter']">{{ $product->genre->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <form action="{{ route($product->type === 'game' ? 'games.reviews.store' : 'tech.reviews.store', $product) }}" method="POST" class="space-y-8">
                @csrf

                <!-- Basic Info -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Review Details</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-white mb-2 font-['Inter']">Review Title *</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                   placeholder="Give your review a catchy title..."
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rating -->
                        <div>
                            <label for="rating" class="block text-sm font-medium text-white mb-2 font-['Inter']">Rating *</label>
                            @if(isset($existingReview))
                                <div class="mb-2 p-3 bg-blue-600/20 border border-blue-500/30 rounded-lg">
                                    <p class="text-sm text-blue-200">You previously rated this {{ $product->type }} {{ $existingReview->rating }}/10 with stars. You can change this rating below.</p>
                                </div>
                            @endif
                            <select id="rating" 
                                    name="rating" 
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                    required>
                                <option value="">Select a rating</option>
                                @for($i = 10; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ (old('rating') == $i) || (isset($existingReview) && $existingReview->rating == $i && !old('rating')) ? 'selected' : '' }}>
                                        {{ $i }}/10 - {{ $i >= 9 ? 'Masterpiece' : ($i >= 8 ? 'Excellent' : ($i >= 7 ? 'Great' : ($i >= 6 ? 'Good' : ($i >= 5 ? 'Average' : ($i >= 4 ? 'Below Average' : ($i >= 3 ? 'Poor' : ($i >= 2 ? 'Bad' : 'Terrible'))))))) }}
                                    </option>
                                @endfor
                            </select>
                            @error('rating')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Platform Played On -->
                        <div>
                            <label for="platform_played_on" class="block text-sm font-medium text-white mb-2 font-['Inter']">Platform Played On</label>
                            <select id="platform_played_on" 
                                    name="platform_played_on" 
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']">
                                <option value="">Select platform</option>
                                @foreach($hardware as $hw)
                                    <option value="{{ $hw->slug }}" {{ old('platform_played_on') === $hw->slug ? 'selected' : '' }}>
                                        {{ $hw->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('platform_played_on')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Identity Selection -->
                    @if($availablePodcasts->count() > 0 || $availableStreamerProfiles->count() > 0)
                    <div class="mt-6 p-4 border-t border-[#3F3F46]">
                        <h3 class="text-lg font-semibold text-white mb-4 font-['Share_Tech_Mono']">Review Identity</h3>
                        
                        <!-- Podcast Selection -->
                        @if($availablePodcasts->count() > 0)
                        <div class="mb-4">
                            <label for="podcast_id" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                                Review as Podcast (Optional)
                            </label>
                            <select id="podcast_id" 
                                    name="podcast_id" 
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']">
                                <option value="">Select a podcast (optional)</option>
                                @foreach($availablePodcasts as $podcast)
                                    <option value="{{ $podcast->id }}" {{ old('podcast_id') == $podcast->id ? 'selected' : '' }}>
                                        {{ $podcast->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-[#A1A1AA] font-['Inter']">
                                Choose a podcast to post this review as. Your name will still be shown as the author.
                            </p>
                            @error('podcast_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <!-- Streamer Profile Selection -->
                        @if($availableStreamerProfiles->count() > 0)
                        <div class="mb-4">
                            <label for="streamer_profile_id" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                                Review as Streamer (Optional)
                            </label>
                            <select id="streamer_profile_id" 
                                    name="streamer_profile_id" 
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']">
                                <option value="">Select a streamer profile (optional)</option>
                                @foreach($availableStreamerProfiles as $profile)
                                    <option value="{{ $profile->id }}" {{ old('streamer_profile_id') == $profile->id ? 'selected' : '' }}>
                                        {{ $profile->channel_name }} ({{ ucfirst($profile->platform) }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-[#A1A1AA] font-['Inter']">
                                Choose a streamer profile to post this review as. Your review will show as "{{ Auth::user()->name }} (StreamerChannel)".
                            </p>
                            @error('streamer_profile_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        @if($availablePodcasts->count() == 0 && $availableStreamerProfiles->count() == 0)
                        <p class="text-sm text-[#A1A1AA] font-['Inter']">
                            This review will be posted as {{ Auth::user()->name }} (Personal Review).
                        </p>
                        @else
                        <p class="text-sm text-[#A1A1AA] font-['Inter']">
                            Leave both options unselected to post as {{ Auth::user()->name }} (Personal Review).
                        </p>
                        @endif
                    </div>
                    @endif
                    </div>
                </div>

                <!-- Review Content -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Your Review</h2>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-white mb-2 font-['Inter']">Review Content *</label>
                        <div class="mb-2">
                            <p class="text-xs text-[#A1A1AA] font-['Inter'] mb-2">
                                Write your detailed review here. <strong>Markdown is supported</strong> - you can use **bold**, *italic*, `code`, lists, and more. Minimum 50 characters.
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-[#A1A1AA] font-['Inter']">
                                    Markdown examples: **bold** | *italic* | `code` | # Heading | - List item
                                </div>
                                <div class="text-xs font-['Inter']" id="char-counter">
                                    <span id="char-count" class="text-[#A1A1AA]">0</span>
                                    <span class="text-[#A1A1AA]"> / </span>
                                    <span class="text-[#A1A1AA]">50 min</span>
                                </div>
                            </div>
                        </div>
                        <textarea id="content" 
                                  name="content" 
                                  rows="12"
                                  class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-4 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter'] resize-y"
                                  placeholder="Share your detailed thoughts about this {{ $product->type }}. You can use **markdown** formatting for *emphasis*, `code snippets`, and more!"
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Positive & Negative Points -->
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Positive Points -->
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">What's Great</h3>
                        </div>
                        <div>
                            <label for="positive_points" class="block text-sm font-medium text-white mb-2 font-['Inter']">Positive Points</label>
                            <textarea id="positive_points" 
                                      name="positive_points" 
                                      rows="6"
                                      class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                      placeholder="List the things you loved (one per line)&#10;Great graphics&#10;Smooth gameplay&#10;Engaging story">{{ old('positive_points') }}</textarea>
                            <p class="mt-1 text-xs text-[#A1A1AA] font-['Inter']">Enter one point per line</p>
                            @error('positive_points')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Negative Points -->
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">Areas for Improvement</h3>
                        </div>
                        <div>
                            <label for="negative_points" class="block text-sm font-medium text-white mb-2 font-['Inter']">Negative Points</label>
                            <textarea id="negative_points" 
                                      name="negative_points" 
                                      rows="6"
                                      class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                      placeholder="List areas that could be improved (one per line)&#10;Long loading times&#10;Some bugs&#10;Repetitive missions">{{ old('negative_points') }}</textarea>
                            <p class="mt-1 text-xs text-[#A1A1AA] font-['Inter']">Enter one point per line</p>
                            @error('negative_points')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4">
                    @if($product->type === 'game')
                        <a href="{{ route('games.show', $product) }}" 
                           class="px-6 py-3 bg-[#27272A] text-white rounded-lg border border-[#3F3F46] hover:bg-[#3F3F46] transition-colors font-['Inter']">
                            Cancel
                        </a>
                    @else
                        <a href="{{ route('tech.show', $product) }}" 
                           class="px-6 py-3 bg-[#27272A] text-white rounded-lg border border-[#3F3F46] hover:bg-[#3F3F46] transition-colors font-['Inter']">
                            Cancel
                        </a>
                    @endif
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-[#E53E3E] to-[#DC2626] text-white rounded-lg hover:from-[#DC2626] hover:to-[#B91C1C] transition-all duration-300 font-bold font-['Inter'] shadow-lg">
                        Publish Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentTextarea = document.getElementById('content');
            const charCountSpan = document.getElementById('char-count');
            const charCounter = document.getElementById('char-counter');
            
            function updateCharCount() {
                const count = contentTextarea.value.length;
                charCountSpan.textContent = count;
                
                // Update color based on character count
                if (count < 50) {
                    charCountSpan.className = 'text-red-400';
                } else if (count < 100) {
                    charCountSpan.className = 'text-yellow-400';
                } else {
                    charCountSpan.className = 'text-green-400';
                }
                
                // Update minimum indicator
                const minIndicator = charCounter.querySelector('span:last-child');
                if (count >= 50) {
                    minIndicator.textContent = '✓ minimum reached';
                    minIndicator.className = 'text-green-400';
                } else {
                    minIndicator.textContent = '50 min';
                    minIndicator.className = 'text-[#A1A1AA]';
                }
            }
            
            // Update on input
            contentTextarea.addEventListener('input', updateCharCount);
            
            // Initial update for pre-filled content
            updateCharCount();
        });
    </script>
</x-layouts.app> 
