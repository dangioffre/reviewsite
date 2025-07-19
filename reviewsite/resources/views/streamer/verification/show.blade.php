@extends('layouts.app')

@section('title', 'Verification Status - ' . $profile->channel_name)

@section('content')
<div class="min-h-screen bg-[#151515] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center space-x-6">
                    @if($profile->profile_photo_url)
                        <img src="{{ $profile->profile_photo_url }}" 
                             alt="{{ $profile->channel_name }}" 
                             class="w-20 h-20 rounded-full border-4 border-[#3F3F46]">
                    @else
                        <div class="w-20 h-20 bg-[#3F3F46] rounded-full flex items-center justify-center border-4 border-[#52525B]">
                            <svg class="w-10 h-10 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono']">{{ $profile->channel_name }}</h1>
                        <p class="text-[#A1A1AA] font-['Inter'] flex items-center mt-2">
                            <span class="px-3 py-1 rounded-full text-sm font-bold 
                                {{ $profile->platform === 'twitch' ? 'bg-[#9146FF] text-white' : 
                                   ($profile->platform === 'youtube' ? 'bg-[#FF0000] text-white' : 'bg-[#53FC18] text-black') }}">
                                {{ ucfirst($profile->platform) }}
                            </span>
                            <span class="ml-3">Channel</span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold font-['Inter']
                        @if($profile->getVerificationBadgeColor() === 'green') bg-green-500/20 text-green-400
                        @elseif($profile->getVerificationBadgeColor() === 'blue') bg-blue-500/20 text-blue-400
                        @elseif($profile->getVerificationBadgeColor() === 'yellow') bg-yellow-500/20 text-yellow-400
                        @elseif($profile->getVerificationBadgeColor() === 'red') bg-red-500/20 text-red-400
                        @else bg-[#3F3F46] text-[#A1A1AA]
                        @endif">
                        {{ $profile->getVerificationStatusText() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Verification Status Card -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Verification Status</h2>
            
            @if($profile->verification_status === 'pending')
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-[#A1A1AA] mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold text-white font-['Inter']">Verification Not Requested</h3>
                            <p class="text-[#A1A1AA] mt-2 font-['Inter']">You haven't requested verification for your channel yet.</p>
                        </div>
                    </div>
                </div>
                
                @if($profile->canRequestVerification())
                    <form action="{{ route('streamer.verification.request', $profile) }}" method="POST" class="mb-6">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-white font-bold rounded-lg hover:from-[#1D4ED8] hover:to-[#2563EB] transition-all duration-300 font-['Inter']">
                            Request Verification
                        </button>
                    </form>
                @else
                    <div class="bg-yellow-500/20 border border-yellow-500/30 rounded-xl p-6 mb-6">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-yellow-400 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-yellow-100 font-['Inter']">Your profile must be approved before you can request verification.</p>
                        </div>
                    </div>
                @endif

            @elseif($profile->verification_status === 'requested')
                <div class="bg-yellow-500/20 border border-yellow-500/30 rounded-xl p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold text-yellow-100 font-['Inter']">Verification Requested</h3>
                            <p class="text-yellow-200 mt-2 font-['Inter']">
                                Your verification request was submitted on {{ $profile->verification_requested_at->format('M j, Y \a\t g:i A') }}.
                                Our team will review your profile soon.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('streamer.verification.cancel', $profile) }}" method="POST" class="mb-6">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-[#3F3F46] text-white font-bold rounded-lg hover:bg-[#52525B] transition-colors font-['Inter']"
                            onclick="return confirm('Are you sure you want to cancel your verification request?')">
                        Cancel Request
                    </button>
                </form>

            @elseif($profile->verification_status === 'in_review')
                <div class="bg-blue-500/20 border border-blue-500/30 rounded-xl p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold text-blue-100 font-['Inter']">Under Review</h3>
                            <p class="text-blue-200 mt-2 font-['Inter']">
                                Your verification request is currently being reviewed by our team.
                                You'll receive an email notification once the review is complete.
                            </p>
                        </div>
                    </div>
                </div>

            @elseif($profile->verification_status === 'verified')
                <div class="bg-green-500/20 border border-green-500/30 rounded-xl p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold text-green-100 font-['Inter']">Verified Channel</h3>
                            <p class="text-green-200 mt-2 font-['Inter']">
                                Your channel has been verified on {{ $profile->verification_completed_at->format('M j, Y \a\t g:i A') }}.
                                You can now post reviews with your streamer identity.
                            </p>
                        </div>
                    </div>
                </div>

            @elseif($profile->verification_status === 'rejected')
                <div class="bg-red-500/20 border border-red-500/30 rounded-xl p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-bold text-red-100 font-['Inter']">Verification Rejected</h3>
                            <p class="text-red-200 mt-2 font-['Inter']">
                                Your verification request was rejected on {{ $profile->verification_completed_at->format('M j, Y \a\t g:i A') }}.
                            </p>
                            @if($profile->verification_notes)
                                <p class="text-red-200 mt-3 font-bold font-['Inter']">Reason:</p>
                                <p class="text-red-200 font-['Inter']">{{ $profile->verification_notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if($profile->canRequestVerification())
                    <form action="{{ route('streamer.verification.request', $profile) }}" method="POST" class="mb-6">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-white font-bold rounded-lg hover:from-[#1D4ED8] hover:to-[#2563EB] transition-all duration-300 font-['Inter']">
                            Request Verification Again
                        </button>
                    </form>
                @endif
            @endif
        </div>

        <!-- Verification Benefits -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Verification Benefits</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white font-['Inter']">Verified Badge</h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mt-1">Display a verification badge on your profile and reviews</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white font-['Inter']">Dual Identity Reviews</h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mt-1">Post reviews with your streamer channel identity</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white font-['Inter']">Enhanced Visibility</h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mt-1">Verified profiles appear higher in search results</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white font-['Inter']">Trust & Credibility</h3>
                        <p class="text-[#A1A1AA] font-['Inter'] mt-1">Build trust with your audience through verified status</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Profile -->
        <div class="mt-8 text-center">
            <a href="{{ route('streamer.profile.show', $profile) }}" 
               class="inline-flex items-center px-6 py-3 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors font-['Inter']">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Profile
            </a>
        </div>
    </div>
</div>
@endsection