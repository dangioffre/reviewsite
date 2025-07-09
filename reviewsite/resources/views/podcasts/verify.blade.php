@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4 text-white font-['Share_Tech_Mono']">
                Verify Your Podcast
            </h1>
            <p class="text-xl text-[#A1A1AA] max-w-2xl mx-auto font-['Inter']">
                Complete the verification process to confirm ownership of your podcast.
            </p>
        </div>

        <!-- Podcast Info -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $podcast->name }}</h2>
                    <p class="text-[#A1A1AA] font-['Inter'] mt-1">{{ $podcast->rss_url }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if($podcast->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($podcast->status === 'verified') bg-blue-100 text-blue-800
                        @elseif($podcast->status === 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($podcast->status) }}
                    </span>
                </div>
            </div>
        </div>

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

        @if($podcast->status === 'pending')
            <!-- Verification Steps -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Verification Token</h2>
                
                <div class="bg-[#1A1A1B] rounded-lg p-6 mb-6 border border-[#3F3F46]">
                    <p class="text-[#A1A1AA] text-sm mb-3 font-['Inter']">Your unique verification token:</p>
                    <div class="flex items-center space-x-3">
                        <code class="bg-[#0F0F0F] text-[#E53E3E] p-3 rounded-lg flex-1 font-mono text-sm break-all">{{ $podcast->verification_token }}</code>
                        <button onclick="copyToClipboard('{{ $podcast->verification_token }}')" 
                                class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-[#DC2626] transition-colors text-sm font-['Inter']">
                            Copy
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="border-l-4 border-[#E53E3E] pl-6">
                        <h3 class="text-xl font-semibold text-white mb-3 font-['Share_Tech_Mono']">Step 1: Add Token to RSS Feed</h3>
                        <p class="text-[#A1A1AA] mb-4 font-['Inter']">
                            Add the verification token above to your podcast RSS feed. You can place it in either:
                        </p>
                        <ul class="text-[#A1A1AA] space-y-2 font-['Inter']">
                            <li class="flex items-start space-x-2">
                                <span class="text-[#E53E3E] mt-1">•</span>
                                <span><strong>Podcast Description:</strong> Add the token anywhere in your podcast's main description</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-[#E53E3E] mt-1">•</span>
                                <span><strong>Latest Episode Description:</strong> Add the token to your most recent episode's description</span>
                            </li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-[#E53E3E] pl-6">
                        <h3 class="text-xl font-semibold text-white mb-3 font-['Share_Tech_Mono']">Step 2: Check Verification</h3>
                        <p class="text-[#A1A1AA] mb-4 font-['Inter']">
                            After adding the token to your RSS feed, click the button below to verify ownership.
                        </p>
                        <button onclick="checkVerification()" 
                                id="verifyButton"
                                class="bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200">
                            Check Verification
                        </button>
                    </div>
                </div>
            </div>

            <!-- Verification Result -->
            <div id="verificationResult" class="hidden mb-8">
                <!-- This will be populated by JavaScript -->
            </div>

        @elseif($podcast->status === 'verified')
            <!-- Verification Complete -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#10B981] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Verification Complete!</h2>
                    <p class="text-[#A1A1AA] mb-6 font-['Inter']">
                        Your podcast has been successfully verified. It's now pending admin approval.
                    </p>
                    <a href="{{ route('podcasts.show', $podcast) }}" 
                       class="bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200 inline-block">
                        View Podcast Profile
                    </a>
                </div>
            </div>

        @elseif($podcast->status === 'approved')
            <!-- Approved -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#10B981] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Podcast Approved!</h2>
                    <p class="text-[#A1A1AA] mb-6 font-['Inter']">
                        Congratulations! Your podcast has been approved and is now live on our platform.
                    </p>
                    <div class="space-x-4">
                        <a href="{{ route('podcasts.show', $podcast) }}" 
                           class="bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200 inline-block">
                            View Podcast Profile
                        </a>
                        <a href="{{ route('podcasts.dashboard') }}" 
                           class="bg-gradient-to-r from-[#3F3F46] to-[#27272A] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#374151] hover:to-[#1F2937] transition-all duration-200 inline-block">
                            Podcast Dashboard
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- Rejected -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#EF4444] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Submission Rejected</h2>
                    <p class="text-[#A1A1AA] mb-6 font-['Inter']">
                        Unfortunately, your podcast submission was not approved.
                    </p>
                    @if($podcast->admin_notes)
                        <div class="bg-[#1A1A1B] rounded-lg p-4 mb-6 border border-[#3F3F46]">
                            <p class="text-[#A1A1AA] text-sm mb-2 font-['Inter']">Admin notes:</p>
                            <p class="text-white font-['Inter']">{{ $podcast->admin_notes }}</p>
                        </div>
                    @endif
                    <a href="{{ route('podcasts.create') }}" 
                       class="bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200 inline-block">
                        Submit New Podcast
                    </a>
                </div>
            </div>
        @endif

        <!-- RSS Error -->
        @if($podcast->rss_error)
            <div class="bg-[#FEF2F2] border border-[#EF4444] rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-[#EF4444] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-[#991B1B] font-medium">RSS Error</span>
                </div>
                <p class="text-[#991B1B] text-sm mt-2">{{ $podcast->rss_error }}</p>
            </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-green-500');
        button.classList.remove('bg-[#E53E3E]');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-500');
            button.classList.add('bg-[#E53E3E]');
        }, 2000);
    });
}

function checkVerification() {
    const button = document.getElementById('verifyButton');
    const resultDiv = document.getElementById('verificationResult');
    
    // Show loading state
    button.disabled = true;
    button.textContent = 'Checking...';
    
    fetch('{{ route('podcasts.check-verification', $podcast) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('hidden');
        
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="bg-[#D1FAE5] border border-[#10B981] rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-[#10B981] mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-[#065F46] font-medium">${data.message}</span>
                    </div>
                </div>
            `;
            
            // Redirect after a short delay
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            }, 2000);
        } else {
            resultDiv.innerHTML = `
                <div class="bg-[#FEF2F2] border border-[#EF4444] rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-[#EF4444] mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-[#991B1B] font-medium">${data.message}</span>
                    </div>
                </div>
            `;
        }
        
        // Reset button
        button.disabled = false;
        button.textContent = 'Check Verification';
    })
    .catch(error => {
        console.error('Error:', error);
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = `
            <div class="bg-[#FEF2F2] border border-[#EF4444] rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-[#EF4444] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-[#991B1B] font-medium">An error occurred. Please try again.</span>
                </div>
            </div>
        `;
        
        // Reset button
        button.disabled = false;
        button.textContent = 'Check Verification';
    });
}
</script>
@endsection 