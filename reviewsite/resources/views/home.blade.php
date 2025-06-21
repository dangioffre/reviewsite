<x-layouts.app>
    <x-slot name="title">Home - Dan & Brian Reviews</x-slot>
    
    @if($featuredPosts->isNotEmpty())
        <section class="w-full h-[60vh] relative overflow-hidden">
            <div class="flex h-full transition-transform duration-500 ease-in-out">
                @foreach($featuredPosts as $post)
                    <div class="min-w-full h-full bg-cover bg-center relative flex items-center justify-center text-center" style="background-image: url('{{ $post->featured_image ?? 'https://via.placeholder.com/1200x600/1A1A1B/FFFFFF?text=Featured' }}');">
                        <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>
                        <div class="relative z-20 max-w-4xl px-8 text-white">
                            <span class="inline-block px-3 py-1 bg-red-600 text-xs font-mono uppercase tracking-wider rounded mb-4">{{ $post->type }}</span>
                            <h2 class="text-4xl font-mono font-bold mb-4">{{ $post->title }}</h2>
                            <p class="text-lg text-gray-200 mb-6 max-w-2xl mx-auto">{{ $post->excerpt }}</p>
                            <a href="#" class="inline-block px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-300">Read More</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
                <button class="w-3 h-3 bg-white bg-opacity-50 hover:bg-opacity-100 rounded-full transition-all duration-300"></button>
            </div>
        </section>
    @else
        <!-- Hero Section -->
        <section class="py-20 text-center">
            <div class="max-w-6xl mx-auto px-4">
                <h1 class="font-mono text-5xl font-bold mb-6 text-red-600" style="text-shadow: 0 0 20px #E53E3E;">WELCOME TO THE ULTIMATE GAMING HUB</h1>
                <p class="text-xl text-gray-400 mb-8 max-w-3xl mx-auto">Discover honest reviews, epic podcasts, live streams, and in-depth articles from Dan & Brian. Your go-to destination for everything gaming.</p>
                <div class="flex gap-4 justify-center flex-wrap">
                    <a href="/reviews" class="inline-block px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-300">Explore Reviews</a>
                    <a href="#" class="inline-block px-8 py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg border-2 border-red-600 transition-colors duration-300">Watch Streams</a>
                </div>
            </div>
        </section>
    @endif

    <!-- Features Section -->
    <section class="py-16 bg-zinc-900">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-3xl font-mono font-bold text-center mb-12 text-red-600">WHAT WE OFFER</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-8 text-center hover:border-red-600 transition-colors duration-300">
                    <div class="text-4xl mb-4">🎮</div>
                    <h3 class="text-xl font-mono font-bold mb-4 text-red-600">GAME REVIEWS</h3>
                    <p class="text-gray-300">Honest, detailed reviews of the latest games. No BS, just real opinions from gamers who actually play.</p>
                </div>
                
                <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-8 text-center hover:border-red-600 transition-colors duration-300">
                    <div class="text-4xl mb-4">📝</div>
                    <h3 class="text-xl font-mono font-bold mb-4 text-red-600">ARTICLES</h3>
                    <p class="text-gray-300">In-depth analysis, gaming news, and thought-provoking content about the industry we love.</p>
                </div>
                
                <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-8 text-center hover:border-red-600 transition-colors duration-300">
                    <div class="text-4xl mb-4">👥</div>
                    <h3 class="text-xl font-mono font-bold mb-4 text-red-600">COMMUNITY</h3>
                    <p class="text-gray-300">Connect with fellow gamers, share your thoughts, and be part of our growing community.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-zinc-800 text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-3xl font-mono font-bold mb-6 text-red-600">READY TO DIVE IN?</h2>
            <p class="text-gray-400 mb-8 text-lg">
                Join thousands of gamers who trust Dan & Brian for their gaming decisions.
            </p>
            <a href="/reviews" class="inline-block px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-300">Get Started</a>
        </div>
    </section>
</x-layouts.app> 