<x-layouts.app>
    <style>
        /* Force pointer events to be enabled, overriding any global disabling styles. */
        body, .tab-button {
            pointer-events: auto !important;
        }
    </style>
    <div class="min-h-screen bg-[#151515]">
        <!-- Game Header Section -->
        <div class="relative bg-gradient-to-br from-[#27272A] via-[#1A1A1B] to-[#151515] py-16 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, #E53E3E 0%, transparent 50%), radial-gradient(circle at 75% 75%, #2563EB 0%, transparent 50%)"></div>
            </div>
            
            <div class="container mx-auto px-4 relative z-10">
                <!-- Breadcrumb -->
                <nav class="mb-8">
                    <div class="flex items-center gap-2 text-sm font-['Inter']">
                        <a href="{{ route('home') }}" class="text-[#A1A1AA] hover:text-white transition-colors">Home</a>
                        <svg class="w-4 h-4 text-[#3F3F46]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('games.index') }}" class="text-[#A1A1AA] hover:text-white transition-colors">Games</a>
                        <svg class="w-4 h-4 text-[#3F3F46]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-[#E53E3E] font-medium">{{ $product->name }}</span>
                    </div>
                </nav>

                <!-- Game Title -->
                <div class="mb-8">
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">
                        {{ $product->name }}
                    </h1>
                </div>

                <!-- Clean Media Section -->
                <div class="grid lg:grid-cols-12 gap-3 items-start mb-12">
                    <!-- Left Side: Photo and Video -->
                    <div class="lg:col-span-9">
                        <div class="flex flex-col lg:flex-row gap-2 items-start">
                            <!-- Game Poster -->
                            <div class="flex-shrink-0">
                                <div class="bg-[#1A1A1B] rounded-xl overflow-hidden border border-[#3F3F46]/20 max-w-[264px] mx-auto lg:mx-0">
                                    <img 
                                        src="{{ $product->image ?? 'https://placehold.co/264x352/1A1A1B/A1A1AA?text=No+Image' }}" 
                                        alt="{{ $product->name }}"
                                        class="w-full h-auto object-cover"
                                        style="aspect-ratio: 264/352;"
                                    >
                                </div>
                            </div>
                            
                            <!-- YouTube Video -->
                            <div class="flex-1">
                                @if($product->video_url)
                                    <div class="bg-gradient-to-br from-[#232326] to-[#18181B] rounded-2xl overflow-hidden border-4 border-[#2563EB]/30 shadow-2xl aspect-video">
                                        <iframe 
                                            src="{{ $product->video_url }}" 
                                            class="w-full h-full"
                                            frameborder="0" 
                                            allowfullscreen
                                            title="{{ $product->name }} - Gameplay Video"
                                        ></iframe>
                                    </div>
                                @else
                                    <div class="bg-[#1A1A1B] rounded-xl border border-[#3F3F46]/20 aspect-video flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-4xl mb-3 opacity-50">🎮</div>
                                            <h3 class="text-lg font-semibold text-white mb-1 font-['Inter']">No Video Available</h3>
                                            <p class="text-sm text-[#A1A1AA] font-['Inter']">Gameplay video coming soon</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side: Ratings Section -->
                    <div class="lg:col-span-3">
                        <div class="bg-[#18181B] border border-[#292929] rounded-2xl p-8 shadow-xl flex flex-col items-center max-w-xs mx-auto">
                            <!-- Ratings Row -->
                            <div class="flex items-center justify-center gap-4 w-full mb-6">
                                @if($product->staff_rating)
                                    <div class="flex flex-col items-center">
                                        <div class="bg-[#22C55E] text-white text-xl font-bold px-4 py-1 rounded-lg font-['Poppins'] mb-1 shadow-sm">
                                            {{ number_format($product->staff_rating, 1) }}
                                        </div>
                                        <div class="text-xs text-[#A0A0A0] font-['Inter']">Staff Rating</div>
                                        <div class="text-xs text-[#A0A0A0] font-['Inter']">{{ $product->staff_reviews_count }} staff {{ Str::plural('rating', $product->staff_reviews_count) }}</div>
                                    </div>
                                @endif
                                <div class="flex flex-col items-center">
                                    <div class="bg-[#2563EB] text-white text-xl font-bold px-4 py-1 rounded-lg font-['Poppins'] mb-1 shadow-sm">
                                        {{ number_format($product->community_rating ?? 0, 1) }}
                                    </div>
                                    <div class="text-xs text-[#A0A0A0] font-['Inter']">User Rating</div>
                                    <div class="text-xs text-[#A0A0A0] font-['Inter']">{{ $product->community_reviews_count ?? 0 }} user {{ Str::plural('rating', $product->community_reviews_count ?? 0) }}</div>
                                </div>
                            </div>

                            <!-- Star Rating Component -->
                            <div class="w-full flex flex-col items-center mb-6">
                                <x-star-rating :product="$product" :userRating="$userRating" :showLabel="false" />
                            </div>

                            <!-- Status Buttons (Own, Want, Play) -->
                            <div class="flex flex-wrap gap-3 justify-center w-full mb-6">
                                <livewire:enhanced-game-status-buttons :product="$product" />
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-3 w-full">
                                <!-- Add to Lists Button -->
                                <x-add-to-list-button :product-id="$product->id" class="w-full" />
                                <!-- Write Review Button -->
                                @auth
                                    <a href="{{ route('games.reviews.create', $product) }}" class="w-full bg-[#292929] hover:bg-[#232326] text-white py-3 px-4 rounded-lg font-semibold text-base transition-colors duration-200 flex items-center justify-center gap-2 shadow-sm font-['Inter']">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Write a review
                                    </a>
                                @else
                                    <button onclick="showLoginPrompt()" class="w-full bg-[#292929] hover:bg-[#232326] text-white py-3 px-4 rounded-lg font-semibold text-base transition-colors duration-200 flex items-center justify-center gap-2 shadow-sm font-['Inter']">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Write a review
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FINAL ALPINE.JS TABS -->
                <div x-data="{ activeTab: 'about' }" class="w-full">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-700 mb-6">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button @click="activeTab = 'about'"
                                    :class="{ 'border-red-500 text-red-400': activeTab === 'about', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': activeTab !== 'about' }"
                                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 focus:outline-none">
                                About
                            </button>
                            <button @click="activeTab = 'reviews'"
                                    :class="{ 'border-red-500 text-red-400': activeTab === 'reviews', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': activeTab !== 'reviews' }"
                                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 focus:outline-none">
                                Reviews
                                @if(($reviewCount = $staffReviews->count() + $streamerReviews->count() + $userReviews->count()) > 0)
                                    <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ $reviewCount }}</span>
                                @endif
                            </button>
                            <button @click="activeTab = 'media'"
                                    :class="{ 'border-red-500 text-red-400': activeTab === 'media', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': activeTab !== 'media' }"
                                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 focus:outline-none">
                                Media
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <div x-show="activeTab === 'about'" class="prose prose-invert max-w-none">
                            <section class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl mb-8">
                                <h2 class="text-3xl font-bold text-white mb-6 font-['Share_Tech_Mono']">About {{ $product->name }}</h2>
                                <div class="mb-8">
                                    <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Description</h3>
                                    <p class="text-[#A1A1AA] text-lg leading-relaxed font-['Inter']">{{ $product->description ?? 'No description available for this game.' }}</p>
                                </div>
                                <div class="bg-[#18181B] rounded-xl p-6 border border-[#3F3F46] mb-8">
                                    <div class="grid md:grid-cols-2 gap-x-12 gap-y-4">
                                        <div><span class="font-bold text-white">Name:</span> <span class="text-[#A1A1AA]">{{ $product->name }}</span></div>
                                        @if($product->genre)
                                            <div><span class="font-bold text-white">Primary Genre:</span>
                                                <a href="{{ route('games.by-genre', $product->genre->slug) }}" class="inline-block bg-green-600/20 text-green-400 px-3 py-1 rounded-full text-sm hover:bg-green-600/40 transition-colors ml-1">
                                                    {{ $product->genre->name }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($product->platform)
                                            <div><span class="font-bold text-white">Primary Platform:</span>
                                                <a href="{{ route('games.by-platform', $product->platform->slug) }}" class="inline-block bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full text-sm hover:bg-blue-600/40 transition-colors ml-1">
                                                    {{ $product->platform->name }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($product->platforms && $product->platforms->count() > 1)
                                            <div><span class="font-bold text-white">Other Platforms:</span>
                                                @foreach($product->platforms as $platform)
                                                    @if(!$product->platform || $platform->id !== $product->platform->id)
                                                        <a href="{{ route('games.by-platform', $platform->slug) }}" class="inline-block bg-blue-600/10 text-blue-300 px-3 py-1 rounded-full text-xs hover:bg-blue-600/20 transition-colors ml-1">
                                                            {{ $platform->name }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($product->release_date)
                                            <div><span class="font-bold text-white">Release Date:</span> <span class="text-[#A1A1AA]">{{ $product->release_date->format('F d, Y') }}</span></div>
                                        @endif
                                        @if($product->themes && $product->themes->count())
                                            <div><span class="font-bold text-white">Theme:</span>
                                                @foreach($product->themes as $theme)
                                                    <a href="{{ route('games.by-theme', $theme->slug) }}" class="inline-block bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-sm hover:bg-yellow-500/40 transition-colors ml-1">
                                                        {{ $theme->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($product->developers && $product->developers->count())
                                            <div><span class="font-bold text-white">Developer:</span>
                                                @foreach($product->developers as $developer)
                                                    <a href="{{ route('games.by-developer', $developer->slug) }}" class="inline-block bg-purple-500/20 text-purple-400 px-3 py-1 rounded-full text-sm hover:bg-purple-500/40 transition-colors ml-1">
                                                        {{ $developer->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($product->publishers && $product->publishers->count())
                                            <div><span class="font-bold text-white">Publisher:</span>
                                                @foreach($product->publishers as $publisher)
                                                    <a href="{{ route('games.by-publisher', $publisher->slug) }}" class="inline-block bg-pink-500/20 text-pink-400 px-3 py-1 rounded-full text-sm hover:bg-pink-500/40 transition-colors ml-1">
                                                        {{ $publisher->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($product->gameModes && $product->gameModes->count())
                                            <div><span class="font-bold text-white">Game Mode:</span>
                                                @foreach($product->gameModes as $mode)
                                                    <a href="{{ route('games.by-mode', $mode->slug) }}" class="inline-block bg-cyan-500/20 text-cyan-400 px-3 py-1 rounded-full text-sm hover:bg-cyan-500/40 transition-colors ml-1">
                                                        {{ $mode->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($product->playerPerspectives && $product->playerPerspectives->count())
                                            <div><span class="font-bold text-white">Player Perspectives:</span>
                                                @foreach($product->playerPerspectives as $perspective)
                                                    <a href="{{ route('games.by-perspective', $perspective->slug) }}" class="inline-block bg-gray-600/20 text-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-600/40 transition-colors ml-1">
                                                        {{ $perspective->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($product->esrbRating)
                                            <div><span class="font-bold text-white">ESRB Rating:</span>
                                                <a href="{{ route('games.by-esrb', $product->esrbRating->slug) }}" class="inline-block bg-blue-800/20 text-blue-300 px-3 py-1 rounded-full text-sm hover:bg-blue-800/40 transition-colors ml-1">
                                                    {{ $product->esrbRating->name }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($product->pegiRating)
                                            <div><span class="font-bold text-white">PEGI Rating:</span>
                                                <a href="{{ route('games.by-pegi', $product->pegiRating->slug) }}" class="inline-block bg-green-800/20 text-green-300 px-3 py-1 rounded-full text-sm hover:bg-green-800/40 transition-colors ml-1">
                                                    {{ $product->pegiRating->name }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($product->official_website)
                                            <div><span class="font-bold text-white">Official Website:</span>
                                                <a href="{{ $product->official_website }}" target="_blank" rel="noopener" class="inline-block bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full text-sm hover:bg-blue-600/40 transition-colors ml-1">
                                                    Visit Official Site
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($product->story)
                                <div class="bg-[#18181B] rounded-xl p-6 border border-[#3F3F46]">
                                    <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">Game Story</h3>
                                    <div class="text-[#A1A1AA] leading-relaxed font-['Inter'] prose prose-invert max-w-none">
                                        {!! $product->story !!}
                                    </div>
                                </div>
                                @endif
                            </section>
                        </div>
                        <div x-show="activeTab === 'reviews'" style="display: none;" class="prose prose-invert max-w-none">
                            <div class="space-y-12">
                                @if($staffReviews->count() > 0)
                                <section x-data="{ view: 'grid', page: 1, perPage: 4 }" class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-12 h-12 bg-[#E53E3E] bg-opacity-20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-[#E53E3E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        </div>
                                        <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Staff Reviews</h2>
                                        <div class="ml-auto flex gap-2">
                                            <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-[#E53E3E] text-white' : 'bg-[#232326] text-[#A1A1AA]'" class="px-3 py-1 rounded font-bold text-xs transition">Grid</button>
                                            <button @click="view = 'list'" :class="view === 'list' ? 'bg-[#E53E3E] text-white' : 'bg-[#232326] text-[#A1A1AA]'" class="px-3 py-1 rounded font-bold text-xs transition">List</button>
                                        </div>
                                    </div>
                                    <template x-if="view === 'grid'">
                                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                                            @foreach($staffReviews as $i => $review)
                                                <template x-if="(page - 1) * perPage <= {{ $i }} && {{ $i }} < page * perPage">
                                                    @include('partials.review_card', ['review' => $review, 'type' => 'staff'])
                                                </template>
                                            @endforeach
                                        </div>
                                    </template>
                                    <template x-if="view === 'list'">
                                        <div class="space-y-6">
                                            @foreach($staffReviews as $i => $review)
                                                <template x-if="(page - 1) * perPage <= {{ $i }} && {{ $i }} < page * perPage">
                                                    @include('partials.review_card', ['review' => $review, 'type' => 'staff'])
                                                </template>
                                            @endforeach
                                        </div>
                                    </template>
                                    <div class="flex justify-center mt-6 gap-2">
                                        <button @click="if(page > 1) page--" :disabled="page === 1" class="px-3 py-1 rounded bg-[#232326] text-[#A1A1AA] font-bold text-xs disabled:opacity-50">Prev</button>
                                        <span class="text-[#A1A1AA] font-bold text-xs">Page <span x-text="page"></span></span>
                                        <button @click="if(page < Math.ceil({{ $staffReviews->count() }} / perPage)) page++" :disabled="page === Math.ceil({{ $staffReviews->count() }} / perPage)" class="px-3 py-1 rounded bg-[#232326] text-[#A1A1AA] font-bold text-xs disabled:opacity-50">Next</button>
                                    </div>
                                </section>
                                @endif
                                @if($streamerReviews->count() > 0)
                                <section x-data="{ view: 'grid', page: 1, perPage: 8 }" class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-12 h-12 bg-purple-600 bg-opacity-20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <h2 class="text-2xl font-bold text-white font-['Share_Tech_Mono']">Reviews by Streamers</h2>
                                        <div class="ml-auto flex gap-2">
                                            <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-purple-600 text-white' : 'bg-[#232326] text-[#A1A1AA]'" class="px-3 py-1 rounded font-bold text-xs transition">Grid</button>
                                            <button @click="view = 'list'" :class="view === 'list' ? 'bg-purple-600 text-white' : 'bg-[#232326] text-[#A1A1AA]'" class="px-3 py-1 rounded font-bold text-xs transition">List</button>
                                        </div>
                                    </div>
                                    <template x-if="view === 'grid'">
                                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                                            @foreach($streamerReviews as $i => $review)
                                                <template x-if="(page - 1) * perPage <= {{ $i }} && {{ $i }} < page * perPage">
                                                    @include('partials.review_card', ['review' => $review, 'type' => 'streamer'])
                                                </template>
                                            @endforeach
                                        </div>
                                    </template>
                                    <template x-if="view === 'list'">
                                        <div class="space-y-6">
                                            @foreach($streamerReviews as $i => $review)
                                                <template x-if="(page - 1) * perPage <= {{ $i }} && {{ $i }} < page * perPage">
                                                    @include('partials.review_card', ['review' => $review, 'type' => 'streamer'])
                                                </template>
                                            @endforeach
                                        </div>
                                    </template>
                                    <div class="flex justify-center mt-6 gap-2">
                                        <button @click="if(page > 1) page--" :disabled="page === 1" class="px-3 py-1 rounded bg-[#232326] text-[#A1A1AA] font-bold text-xs disabled:opacity-50">Prev</button>
                                        <span class="text-[#A1A1AA] font-bold text-xs">Page <span x-text="page"></span></span>
                                        <button @click="if(page < Math.ceil({{ $streamerReviews->count() }} / perPage)) page++" :disabled="page === Math.ceil({{ $streamerReviews->count() }} / perPage)" class="px-3 py-1 rounded bg-[#232326] text-[#A1A1AA] font-bold text-xs disabled:opacity-50">Next</button>
                                    </div>
                                </section>
                                @endif
                                <section x-data="{ view: 'grid', page: 1, perPage: 16 }" class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                                    <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Community Reviews</h2>
                                    <div class="ml-auto flex gap-2 mb-4">
                                        <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-[#2563EB] text-white' : 'bg-[#232326] text-[#A1A1AA]'" class="px-3 py-1 rounded font-bold text-xs transition">Grid</button>
                                        <button @click="view = 'list'" :class="view === 'list' ? 'bg-[#2563EB] text-white' : 'bg-[#232326] text-[#A1A1AA]'" class="px-3 py-1 rounded font-bold text-xs transition">List</button>
                                    </div>
                                    <template x-if="view === 'grid'">
                                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                                            @foreach($userReviews as $i => $review)
                                                <template x-if="(page - 1) * perPage <= {{ $i }} && {{ $i }} < page * perPage">
                                                    @include('partials.review_card', ['review' => $review, 'type' => 'community'])
                                                </template>
                                            @endforeach
                                        </div>
                                    </template>
                                    <template x-if="view === 'list'">
                                        <div class="space-y-6">
                                            @foreach($userReviews as $i => $review)
                                                <template x-if="(page - 1) * perPage <= {{ $i }} && {{ $i }} < page * perPage">
                                                    @include('partials.review_card', ['review' => $review, 'type' => 'community'])
                                                </template>
                                            @endforeach
                                        </div>
                                    </template>
                                    <div class="flex justify-center mt-6 gap-2">
                                        <button @click="if(page > 1) page--" :disabled="page === 1" class="px-3 py-1 rounded bg-[#232326] text-[#A1A1AA] font-bold text-xs disabled:opacity-50">Prev</button>
                                        <span class="text-[#A1A1AA] font-bold text-xs">Page <span x-text="page"></span></span>
                                        <button @click="if(page < Math.ceil({{ $userReviews->count() }} / perPage)) page++" :disabled="page === Math.ceil({{ $userReviews->count() }} / perPage)" class="px-3 py-1 rounded bg-[#232326] text-[#A1A1AA] font-bold text-xs disabled:opacity-50">Next</button>
                                    </div>
                                </section>
                            </div>
                        </div>
                        <div x-show="activeTab === 'media'" style="display: none;" class="prose prose-invert max-w-none">
                            <section class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl p-8 border border-[#3F3F46] shadow-2xl">
                                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Media Gallery</h2>
                                @php
                                    $mainPhoto = $product->image ?? null;
                                    $mainVideo = $product->video_url ?? null;
                                    $photos = [];
                                    if (is_array($product->photos)) {
                                        foreach ($product->photos as $photo) {
                                            if (!empty($photo['url'])) {
                                                $photos[] = $photo;
                                            }
                                        }
                                    }
                                    $videos = [];
                                    if (is_array($product->videos)) {
                                        foreach ($product->videos as $video) {
                                            if (!empty($video['url'])) {
                                                $videos[] = $video;
                                            }
                                        }
                                    }
                                @endphp
                                {{-- Videos Section --}}
                                @if($mainVideo || count($videos))
                                    <div class="mb-10">
                                        <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Videos</h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                            @if($mainVideo)
                                                <div class="rounded-xl overflow-hidden border border-[#3F3F46] bg-[#18181B] flex flex-col">
                                                    <div class="aspect-video w-full">
                                                        <iframe src="{{ $mainVideo }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                                    </div>
                                                    <div class="p-2 text-[#A1A1AA] text-sm text-center font-['Inter']">Main Gameplay Video</div>
                                                </div>
                                            @endif
                                            @foreach($videos as $video)
                                                <div class="rounded-xl overflow-hidden border border-[#3F3F46] bg-[#18181B] flex flex-col">
                                                    <div class="aspect-video w-full">
                                                        <iframe src="{{ $video['url'] }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                                    </div>
                                                    @if(!empty($video['title']))
                                                        <div class="p-2 text-[#A1A1AA] text-sm text-center font-['Inter']">{{ $video['title'] }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                {{-- Photos Section --}}
                                @if($mainPhoto || count($photos))
                                    <div x-data="{ showModal: false, modalImg: '', modalCaption: '' }">
                                        <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono']">Photos</h3>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                            @if($mainPhoto)
                                                <div class="rounded-xl overflow-hidden border border-[#3F3F46] bg-[#18181B] flex flex-col cursor-pointer hover:shadow-lg transition-shadow" @click="showModal = true; modalImg = '{{ $mainPhoto }}'; modalCaption = 'Main Game Image';">
                                                    <div class="w-full h-[110px] bg-black flex items-center justify-center">
                                                        <img src="{{ $mainPhoto }}" alt="Main Game Image" class="object-cover w-full h-full">
                                                    </div>
                                                    <div class="p-1 text-[#A1A1AA] text-xs text-center font-['Inter']">Main Game Image</div>
                                                </div>
                                            @endif
                                            @foreach($photos as $photo)
                                                <div class="rounded-xl overflow-hidden border border-[#3F3F46] bg-[#18181B] flex flex-col cursor-pointer hover:shadow-lg transition-shadow" @click="showModal = true; modalImg = '{{ $photo['url'] }}'; modalCaption = '{{ $photo['caption'] ?? '' }}';">
                                                    <div class="w-full h-[110px] bg-black flex items-center justify-center">
                                                        <img src="{{ $photo['url'] }}" alt="Screenshot" class="object-cover w-full h-full">
                                                    </div>
                                                    @if(!empty($photo['caption']))
                                                        <div class="p-1 text-[#A1A1AA] text-xs text-center font-['Inter']">{{ $photo['caption'] }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Lightbox Modal -->
                                        <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-4" style="display: none;">
                                            <div class="relative max-w-3xl w-full max-h-full flex flex-col items-center">
                                                <button @click="showModal = false" class="absolute top-2 right-2 text-white hover:text-[#E53E3E] z-10 bg-black bg-opacity-50 rounded-full p-2">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                                <img :src="modalImg" alt="Enlarged Photo" class="max-w-full max-h-[80vh] rounded-lg shadow-2xl">
                                                <div class="mt-2 text-[#A1A1AA] text-center text-sm font-['Inter']" x-text="modalCaption"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(!$mainPhoto && !count($photos) && !$mainVideo && !count($videos))
                                    <p class="text-center text-gray-400">Media content coming soon.</p>
                                @endif
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Prompt Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="text-4xl mb-4">🔐</div>
                <h3 class="text-xl font-bold text-white mb-2">Login Required</h3>
                <p class="text-[#A1A1AA] mb-6">You need to be logged in to rate games and write reviews.</p>
                <div class="flex space-x-3">
                    <a href="{{ route('login') }}" class="flex-1 bg-[#E53E3E] hover:bg-[#DC2626] text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 bg-[#3F3F46] hover:bg-[#4B5563] text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200">
                        Register
                    </a>
                </div>
                <button onclick="closeLoginPrompt()" class="mt-4 text-[#A1A1AA] hover:text-white text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-80 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-[#E53E3E] z-10 bg-black bg-opacity-50 rounded-full p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black/80 to-transparent p-4 rounded-b-lg">
                <h3 id="modalImageTitle" class="text-white font-semibold text-lg"></h3>
            </div>
        </div>
    </div>

</x-layouts.app> 
