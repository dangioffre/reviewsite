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
                Enter your RSS feed URL below to automatically fetch your podcast's details.
            </p>
        </div>

        <!-- Step 1: Fetch RSS -->
        <div id="fetch-section" class="bg-[#27272A] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Step 1: Fetch from RSS</h2>
            <div class="flex items-center space-x-2">
                <input type="url" id="rss-url-input" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']" placeholder="https://example.com/podcast-rss.xml" required>
                <button id="fetch-rss-btn" class="bg-[#E53E3E] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:bg-red-700 transition-colors whitespace-nowrap">
                    Fetch Info
                </button>
            </div>
            <div id="rss-error" class="mt-2 text-sm text-red-500"></div>
        </div>

        <!-- Step 2: Review and Submit -->
        <form id="podcast-form" action="{{ route('podcasts.store') }}" method="POST" class="hidden mt-8 space-y-8">
            @csrf
            <input type="hidden" name="rss_url" id="rss_url_hidden">
            
            <div class="bg-[#27272A] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Step 2: Review & Submit</h2>
                
                <div class="flex flex-col md:flex-row gap-8 items-start">
                    <!-- Podcast Logo -->
                    <div class="flex-shrink-0">
                         <img id="podcast-logo" src="" alt="Podcast Logo" class="w-40 h-40 rounded-lg object-cover bg-[#3F3F46] border border-[#3F3F46]">
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 flex-grow">
                        <!-- Podcast Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-white mb-2">Podcast Name *</label>
                            <input type="text" id="name" name="name" class="w-full input-style" required>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-white mb-2">Description</label>
                            <textarea id="description" name="description" rows="4" class="w-full input-style"></textarea>
                        </div>

                        <!-- Website URL -->
                        <div>
                            <label for="website_url" class="block text-sm font-medium text-white mb-2">Website URL</label>
                            <input type="url" id="website_url" name="website_url" class="w-full input-style">
                        </div>

                        <!-- Hosts -->
                        <div>
                            <label for="hosts" class="block text-sm font-medium text-white mb-2">Hosts</label>
                            <input type="text" id="hosts" name="hosts" class="w-full input-style">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="bg-[#E53E3E] text-white font-bold py-4 px-8 rounded-full text-lg hover:bg-red-700 transition-colors">
                    Submit Podcast for Review
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .input-style {
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid #3F3F46;
        background-color: #1A1A1B;
        padding: 0.75rem;
        color: white;
    }
    .input-style:focus {
        border-color: #E53E3E;
        --tw-ring-color: #E53E3E;
        box-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const fetchBtn = document.getElementById('fetch-rss-btn');
    const rssUrlInput = document.getElementById('rss-url-input');
    const rssError = document.getElementById('rss-error');
    const podcastForm = document.getElementById('podcast-form');
    
    fetchBtn.addEventListener('click', async () => {
        const rssUrl = rssUrlInput.value;
        if (!rssUrl) {
            rssError.textContent = 'Please enter a valid RSS feed URL.';
            return;
        }

        fetchBtn.disabled = true;
        fetchBtn.textContent = 'Fetching...';
        rssError.textContent = '';

        try {
            const response = await fetch('{{ route('podcasts.fetch-info') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ rss_url: rssUrl })
            });

            const result = await response.json();

            if (result.success) {
                const data = result.data;
                document.getElementById('rss_url_hidden').value = rssUrl;
                document.getElementById('name').value = data.title || '';
                document.getElementById('description').value = data.description || '';
                document.getElementById('website_url').value = data.website_url || '';
                document.getElementById('hosts').value = data.hosts || '';
                
                const logoEl = document.getElementById('podcast-logo');
                logoEl.src = data.logo_url || '/images/default-podcast.png';

                // Show the form
                podcastForm.classList.remove('hidden');
                document.getElementById('fetch-section').classList.add('hidden');

            } else {
                rssError.textContent = 'Error: ' + result.error;
            }
        } catch (error) {
            rssError.textContent = 'An unexpected error occurred. Please check the URL and try again.';
        } finally {
            fetchBtn.disabled = false;
            fetchBtn.textContent = 'Fetch Info';
        }
    });
});
</script>
@endsection 