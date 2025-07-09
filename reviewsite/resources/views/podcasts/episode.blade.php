@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#18181B] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-sm mb-8 font-sans">
            <a href="{{ route('podcasts.index') }}" class="text-[#A1A1AA] hover:text-[#E53E3E] transition-colors">Podcasts</a>
            <span class="text-[#4B5563]">/</span>
            <a href="{{ route('podcasts.show', $podcast) }}" class="text-[#A1A1AA] hover:text-[#E53E3E] transition-colors line-clamp-1">{{ $podcast->name }}</a>
            <span class="text-[#4B5563]">/</span>
            <span class="text-white line-clamp-1">{{ $episode->title }}</span>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-300 px-6 py-4 rounded-lg mb-8 flex items-center gap-3 font-sans">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-6 py-4 rounded-lg mb-8 flex items-center gap-3 font-sans">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-12">
            <!-- Main Content (Left) -->
            <main class="lg:col-span-2 space-y-8">
                <!-- Episode Header -->
                <header>
                    <div class="flex items-center gap-3 mb-2">
                        @if($episode->episode_type && $episode->episode_type !== 'full')
                            <span class="px-3 py-1 bg-blue-500/20 text-blue-300 text-xs rounded-full border border-blue-500/30 font-semibold">{{ ucfirst($episode->episode_type) }}</span>
                        @endif
                         @if($episode->is_explicit)
                            <span class="px-3 py-1 bg-red-500/20 text-red-300 text-xs rounded-full border border-red-500/30 font-semibold">Explicit</span>
                        @endif
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 leading-tight">{{ $episode->title }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[#A1A1AA]">
                        @if($episode->display_number)
                            <span>{{ $episode->display_number }}</span>
                        @endif
                        <span>{{ $episode->published_at->format('F j, Y') }}</span>
                        @if($episode->duration)
                            <span>{{ $episode->formatted_duration }}</span>
                        @endif
                    </div>
                </header>

                <!-- Audio Player -->
                @if($episode->audio_url)
                <section class="bg-[#27272A] rounded-xl border border-[#3F3F46] p-6">
                    <div class="flex items-center gap-5">
                        <img src="{{ $episode->artwork_url ?? $podcast->logo_url }}" alt="{{ $episode->title }}" class="w-28 h-28 rounded-md object-cover shadow-lg">
                        <div class="flex-1">
                            <h2 class="text-xl font-bold">Listen Now</h2>
                            <p class="text-[#A1A1AA] text-sm">{{ $podcast->name }}</p>
                            <audio controls class="w-full mt-4 clean-audio-player" preload="metadata">
                                <source src="{{ $episode->audio_url }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    </div>
                </section>
                @endif
                
                <!-- Show Notes -->
                @if($episode->show_notes)
                <section class="bg-[#27272A] rounded-xl border border-[#3F3F46] p-8">
                    <h2 class="text-xl font-bold mb-4">Show Notes</h2>
                    <div class="prose prose-invert max-w-none prose-p:text-[#D4D4D8] prose-a:text-[#E53E3E] hover:prose-a:text-red-400 prose-strong:text-white">
                        {!! $episode->show_notes !!}
                    </div>
                </section>
                @endif

                <!-- Episode Reviews -->
                <section id="episode-reviews">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Episode Reviews ({{ $episodeReviews->count() }})</h2>
                        @auth
                            @if($episode->canBeReviewedBy(auth()->user()) && !$episode->hasReviewFrom(auth()->user()))
                                <a href="{{ route('podcasts.episodes.reviews.create', [$podcast, $episode]) }}" class="px-4 py-2 bg-[#E53E3E] text-white font-bold rounded-lg hover:bg-red-700 transition-colors text-sm">Write a Review</a>
                            @endif
                        @endauth
                    </div>
                    
                    @if($episodeReviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($episodeReviews as $review)
                        <article class="bg-[#27272A] rounded-xl border border-[#3F3F46] p-6 flex gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=E53E3E&color=fff&bold=true" alt="{{ $review->user->name }}" class="w-10 h-10 rounded-full">
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-bold">{{ $review->user->name }}</span>
                                        <p class="text-sm text-[#A1A1AA]">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-1 text-yellow-400 font-bold text-sm bg-yellow-400/10 border border-yellow-400/20 px-3 py-1 rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <span>{{ $review->rating }}/10</span>
                                    </div>
                                </div>
                                <p class="mt-4 text-[#D4D4D8]">{{ $review->content }}</p>
                            </div>
                        </article>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12 bg-[#27272A] rounded-xl border border-[#3F3F46]">
                        <p class="text-[#A1A1AA] font-medium">No reviews for this episode yet.</p>
                        @guest
                            <a href="{{ route('login') }}" class="text-[#E53E3E] hover:underline mt-2 inline-block text-sm">Log in to write a review</a>
                        @endguest
                    </div>
                    @endif
                </section>
            </main>

            <!-- Sidebar (Right) -->
            <aside class="space-y-8 mt-12 lg:mt-0">
                <!-- Podcast Info -->
                <section class="bg-[#27272A] rounded-xl border border-[#3F3F46] p-6">
                    <h3 class="text-lg font-bold mb-4">About The Podcast</h3>
                    <div class="flex items-center gap-4">
                        <img src="{{ $podcast->logo_url }}" alt="{{ $podcast->name }}" class="w-16 h-16 rounded-lg object-cover">
                        <div>
                            <h4 class="font-bold text-white">{{ $podcast->name }}</h4>
                            <p class="text-sm text-[#A1A1AA] mb-1">By {{ $podcast->owner->name }}</p>
                            <a href="{{ route('podcasts.show', $podcast) }}" class="text-sm text-[#E53E3E] hover:underline font-semibold">View Podcast</a>
                        </div>
                    </div>
                </section>
                
                <!-- Attached Reviews -->
                @if($attachedReviews->count() > 0)
                <section>
                    <h3 class="text-lg font-bold mb-4">Related Product Reviews</h3>
                    <div class="space-y-4">
                        @foreach($attachedReviews as $review)
                        <a href="{{ route('games.reviews.show', ['product' => $review->product, 'review' => $review]) }}" class="block bg-[#27272A] rounded-xl border border-[#3F3F46] p-4 hover:border-[#E53E3E] transition-colors">
                             <div class="flex items-center gap-4">
                                <img src="{{ $review->product->image_url }}" alt="{{ $review->product->name }}" class="w-14 h-14 object-cover rounded-md">
                                <div class="flex-1">
                                    <p class="font-bold text-white leading-tight">{{ $review->product->name }}</p>
                                    <p class="text-sm text-[#A1A1AA]">{{ $review->title }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </section>
                @endif
                
                <!-- More Episodes -->
                @if($recentEpisodes->count() > 0)
                <section>
                    <h3 class="text-lg font-bold mb-4">More Episodes</h3>
                    <div class="space-y-3">
                        @foreach($recentEpisodes as $recentEpisode)
                            <a href="{{ route('podcasts.episodes.show', [$podcast, $recentEpisode]) }}" class="block bg-[#27272A] rounded-xl border border-[#3F3F46] p-4 hover:border-[#E53E3E] transition-colors">
                                <div class="flex items-center gap-4">
                                     <img src="{{ $recentEpisode->artwork_url ?? $podcast->logo_url }}" alt="{{ $recentEpisode->title }}" class="w-12 h-12 rounded-md object-cover">
                                     <div class="flex-1">
                                         <p class="font-semibold text-white leading-tight">{{ $recentEpisode->title }}</p>
                                         <p class="text-xs text-[#A1A1AA]">{{ $recentEpisode->published_at->format('M j, Y') }}</p>
                                     </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
                @endif
            </aside>
        </div>
    </div>
</div>

<style>
.clean-audio-player::-webkit-media-controls-panel {
    background-color: #27272A;
    padding: 8px;
}
.clean-audio-player::-webkit-media-controls-play-button,
.clean-audio-player::-webkit-media-controls-mute-button {
    background-color: #E53E3E;
    border-radius: 50%;
}
.clean-audio-player::-webkit-media-controls-current-time-display,
.clean-audio-player::-webkit-media-controls-time-remaining-display {
    color: #A1A1AA;
    font-size: 12px;
    font-weight: 500;
}
</style>
@endsection 