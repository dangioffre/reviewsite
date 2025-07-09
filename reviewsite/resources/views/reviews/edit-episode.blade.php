@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="flex items-center text-[#A1A1AA] text-sm mb-8 font-['Inter']">
            <a href="{{ route('podcasts.index') }}" class="hover:text-[#E53E3E] transition-colors">Podcasts</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('podcasts.show', $podcast) }}" class="hover:text-[#E53E3E] transition-colors">{{ $podcast->name }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" class="hover:text-[#E53E3E] transition-colors">{{ $episode->title }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('podcasts.episodes.reviews.show', [$podcast, $episode, $review]) }}" class="hover:text-[#E53E3E] transition-colors">{{ $review->title }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Edit Review</span>
        </div>

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 font-['Share_Tech_Mono']">
                Edit Episode Review
            </h1>
            <p class="text-[#A1A1AA] text-lg font-['Inter'] max-w-2xl mx-auto">
                Update your thoughts and improve your review
            </p>
        </div>

        <!-- Episode Info -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Editing Review For</h2>
            
            <div class="flex items-start gap-6">
                @if($episode->artwork_url)
                    <img src="{{ $episode->artwork_url }}" 
                         alt="{{ $episode->title }}"
                         class="w-24 h-24 rounded-lg object-cover">
                @else
                    <div class="w-24 h-24 bg-[#3F3F46] rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">{{ $episode->title }}</h3>
                    <p class="text-[#A1A1AA] text-sm font-['Inter'] mb-2">{{ $podcast->name }}</p>
                    
                    <div class="flex items-center text-[#A1A1AA] text-sm space-x-4 font-['Inter']">
                        @if($episode->display_number)
                            <span>{{ $episode->display_number }}</span>
                        @endif
                        <span>{{ $episode->published_at->format('M j, Y') }}</span>
                        @if($episode->duration)
                            <span>{{ $episode->formatted_duration }}</span>
                        @endif
                    </div>
                    
                    @if($episode->description)
                        <p class="text-[#A1A1AA] text-sm font-['Inter'] mt-3 leading-relaxed">
                            {{ Str::limit($episode->description, 200) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <form action="{{ route('podcasts.episodes.reviews.update', [$podcast, $episode, $review]) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

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
                               value="{{ old('title', $review->title) }}"
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
                        <div class="flex items-center gap-4">
                            <div class="flex">
                                @for($i = 1; $i <= 10; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" 
                                               name="rating" 
                                               value="{{ $i }}" 
                                               class="sr-only rating-input"
                                               {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                                        <svg class="w-6 h-6 text-[#3F3F46] hover:text-[#E53E3E] transition-colors star-icon" 
                                             fill="currentColor" 
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            <span class="text-white font-['Inter']" id="rating-display">{{ old('rating', $review->rating) }}/10</span>
                        </div>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Podcast Identity -->
                    @if($availablePodcasts->isNotEmpty())
                        <div class="md:col-span-2">
                            <label for="podcast_id" class="block text-sm font-medium text-white mb-2 font-['Inter']">Review Identity</label>
                            <select name="podcast_id" 
                                    id="podcast_id"
                                    class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']">
                                <option value="">Personal Review ({{ auth()->user()->name }})</option>
                                @foreach($availablePodcasts as $availablePodcast)
                                    <option value="{{ $availablePodcast->id }}" {{ old('podcast_id', $review->podcast_id) == $availablePodcast->id ? 'selected' : '' }}>
                                        Podcast Review ({{ $availablePodcast->name }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[#A1A1AA] text-sm mt-1 font-['Inter']">
                                Choose whether to post this review as yourself or as a podcast team member
                            </p>
                            @error('podcast_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Review Content -->
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Review Content</h2>
                
                <div class="space-y-6">
                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            Your Review *
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="8" 
                                  class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                  placeholder="Share your detailed thoughts about this episode..."
                                  required>{{ old('content', $review->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Positive Points -->
                    <div>
                        <label for="positive_points" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            What You Liked (Optional)
                        </label>
                        <textarea id="positive_points" 
                                  name="positive_points" 
                                  rows="4" 
                                  class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                  placeholder="List the positive aspects (one per line)...">{{ old('positive_points', is_array($review->positive_points) ? implode("\n", $review->positive_points) : $review->positive_points) }}</textarea>
                        @error('positive_points')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Negative Points -->
                    <div>
                        <label for="negative_points" class="block text-sm font-medium text-white mb-2 font-['Inter']">
                            What Could Be Better (Optional)
                        </label>
                        <textarea id="negative_points" 
                                  name="negative_points" 
                                  rows="4" 
                                  class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition font-['Inter']"
                                  placeholder="List areas for improvement (one per line)...">{{ old('negative_points', is_array($review->negative_points) ? implode("\n", $review->negative_points) : $review->negative_points) }}</textarea>
                        @error('negative_points')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center gap-4">
                <a href="{{ route('podcasts.episodes.reviews.show', [$podcast, $episode, $review]) }}" 
                   class="bg-[#3F3F46] text-white px-8 py-3 rounded-lg hover:bg-[#4B5563] transition-colors font-['Inter'] font-medium text-lg">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-[#E53E3E] text-white px-8 py-3 rounded-lg hover:bg-[#DC2626] transition-colors font-['Inter'] font-medium text-lg">
                    Update Review
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating system
    const ratingInputs = document.querySelectorAll('.rating-input');
    const starIcons = document.querySelectorAll('.star-icon');
    const ratingDisplay = document.getElementById('rating-display');
    
    function updateStars(rating) {
        starIcons.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-[#3F3F46]');
                star.classList.add('text-[#E53E3E]');
            } else {
                star.classList.remove('text-[#E53E3E]');
                star.classList.add('text-[#3F3F46]');
            }
        });
        
        if (rating > 0) {
            ratingDisplay.textContent = rating + '/10';
        } else {
            ratingDisplay.textContent = 'Select a rating';
        }
    }
    
    // Handle rating clicks
    ratingInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            updateStars(index + 1);
        });
    });
    
    // Handle star hovers
    starIcons.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            updateStars(index + 1);
        });
        
        star.addEventListener('click', function() {
            ratingInputs[index].checked = true;
            updateStars(index + 1);
        });
    });
    
    // Handle mouse leave
    document.querySelector('.flex').addEventListener('mouseleave', function() {
        const checkedRating = document.querySelector('.rating-input:checked');
        if (checkedRating) {
            updateStars(parseInt(checkedRating.value));
        } else {
            updateStars(0);
        }
    });
    
    // Initialize with existing rating
    const existingRating = document.querySelector('.rating-input:checked');
    if (existingRating) {
        updateStars(parseInt(existingRating.value));
    }
});
</script>
@endsection 