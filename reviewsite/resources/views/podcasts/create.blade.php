@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4 text-white font-['Share_Tech_Mono']">
                Submit Your Podcast
            </h1>
            <p class="text-xl text-[#A1A1AA] max-w-2xl mx-auto font-['Inter']">
                Share your podcast with our community. Once verified and approved, you'll be able to post reviews as your podcast and engage with our gaming community.
            </p>
        </div>

        <!-- Existing Pending Podcast Warning -->
        @if($existingPendingPodcast)
            <div class="bg-[#FEF3C7] border border-[#F59E0B] rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-[#F59E0B] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-[#92400E] font-medium">
                        You already have a pending podcast submission: <strong>{{ $existingPendingPodcast->name }}</strong>
                    </span>
                </div>
                <p class="text-[#92400E] text-sm mt-2">
                    <a href="{{ route('podcasts.verify', $existingPendingPodcast) }}" class="underline hover:no-underline">
                        Continue with verification →
                    </a>
                </p>
            </div>
        @endif

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-[#D1FAE5] border border-[#10B981] rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-[#10B981] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-[#065F46] font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Submission Form -->
        <form action="{{ route('podcasts.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Podcast Details</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Podcast Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            Podcast Name *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                               placeholder="Enter your podcast name..."
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RSS Feed URL -->
                    <div class="md:col-span-2">
                        <label for="rss_url" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            RSS Feed URL *
                        </label>
                        <input type="url" 
                               id="rss_url" 
                               name="rss_url" 
                               value="{{ old('rss_url') }}"
                               class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                               placeholder="https://example.com/podcast-rss.xml"
                               required>
                        @error('rss_url')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-[#A1A1AA] font-['Inter']">
                            The RSS feed URL for your podcast. This is typically provided by your podcast hosting platform.
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                  placeholder="Describe your podcast... (optional - will be auto-populated from RSS if not provided)">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website URL -->
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            Website URL
                        </label>
                        <input type="url" 
                               id="website_url" 
                               name="website_url" 
                               value="{{ old('website_url') }}"
                               class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                               placeholder="https://yourpodcast.com">
                        @error('website_url')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hosts -->
                    <div>
                        <label for="hosts" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            Hosts
                        </label>
                        <input type="text" 
                               id="hosts" 
                               name="hosts" 
                               value="{{ old('hosts') }}"
                               class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                               placeholder="John Doe, Jane Smith">
                        @error('hosts')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-[#A1A1AA] font-['Inter']">
                            Comma-separated list of host names (optional)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Information Section -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Next Steps</h2>
                
                <div class="space-y-4 text-[#A1A1AA] font-['Inter']">
                    <div class="flex items-start space-x-3">
                        <span class="bg-[#E53E3E] text-white text-sm font-bold px-2 py-1 rounded-full mt-0.5">1</span>
                        <div>
                            <h3 class="text-white font-semibold">RSS Validation</h3>
                            <p class="text-sm">We'll validate your RSS feed to ensure it's accessible and properly formatted.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <span class="bg-[#E53E3E] text-white text-sm font-bold px-2 py-1 rounded-full mt-0.5">2</span>
                        <div>
                            <h3 class="text-white font-semibold">Verification</h3>
                            <p class="text-sm">You'll receive a unique verification token to add to your podcast RSS feed.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <span class="bg-[#E53E3E] text-white text-sm font-bold px-2 py-1 rounded-full mt-0.5">3</span>
                        <div>
                            <h3 class="text-white font-semibold">Admin Approval</h3>
                            <p class="text-sm">Once verified, your podcast will be reviewed by our team for final approval.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <span class="bg-[#E53E3E] text-white text-sm font-bold px-2 py-1 rounded-full mt-0.5">4</span>
                        <div>
                            <h3 class="text-white font-semibold">Go Live</h3>
                            <p class="text-sm">Your podcast will be live on our site and you can start posting reviews as your podcast!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" 
                        class="bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-4 px-8 rounded-full text-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Submit Podcast for Review
                </button>
                
                <p class="text-[#A1A1AA] text-sm mt-4 font-['Inter']">
                    By submitting, you confirm that you own or have permission to submit this podcast.
                </p>
            </div>
        </form>
    </div>
</div>
@endsection 