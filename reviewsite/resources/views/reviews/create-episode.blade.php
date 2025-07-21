<x-layouts.app>
    <div class="min-h-screen bg-[#151515] py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" 
                       class="inline-flex items-center text-[#A1A1AA] hover:text-[#E53E3E] transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Episode
                    </a>
                </div>
                <h1 class="text-4xl font-bold text-white mb-4 font-['Share_Tech_Mono']">
                    Write a Review for "{{ $episode->title }}"
                </h1>
            </div>

            <!-- Review Form -->
            <form action="{{ route('podcasts.episodes.reviews.store', [$podcast, $episode]) }}" method="POST" class="space-y-8">
                @csrf

                <!-- Review Details -->
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Your Review</h2>
                    
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
                        <div class="md:col-span-2">
                            <label for="rating" class="block text-sm font-medium text-white mb-2 font-['Inter']">Rating *</label>
                            <select id="rating" 
                                    name="rating" 
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                    required>
                                <option value="">Select a rating</option>
                                @for($i = 10; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
                                        {{ $i }}/10
                                    </option>
                                @endfor
                            </select>
                            @error('rating')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="md:col-span-2">
                            <label for="content" class="block text-sm font-medium text-white mb-2 font-['Inter']">Review Content *</label>
                            <textarea id="content" 
                                    name="content" 
                                    rows="10"
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-4 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter'] resize-y"
                                    placeholder="Share your detailed thoughts about the episode..."
                                    required>{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" 
                       class="px-6 py-3 bg-[#27272A] text-white rounded-lg border border-[#3F3F46] hover:bg-[#3F3F46] transition-colors font-['Inter']">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold rounded-lg hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200">
                        Publish Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app> 
