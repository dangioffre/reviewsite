@extends('layouts.app')

@section('content')
<div class="bg-[#18181B] py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white">Edit Review</h1>
            <p class="text-[#A1A1AA] mt-1">For episode: <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" class="text-[#E53E3E] hover:underline">{{ $episode->title }}</a></p>
        </div>

        <!-- Edit Form -->
        <div class="bg-[#27272A] rounded-xl border border-[#3F3F46] p-8">
            <form action="{{ route('podcasts.episodes.reviews.update', [$podcast, $episode, $review]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Rating -->
                    <div>
                        <label for="rating" class="block text-sm font-medium text-white mb-2">Rating</label>
                        <select id="rating" name="rating" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition-colors">
                            @for ($i = 10; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ $review->rating == $i ? 'selected' : '' }}>
                                    {{ $i }}/10
                                </option>
                            @endfor
                        </select>
                        @error('rating')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-white mb-2">Review</label>
                        <textarea id="content" name="content" rows="8" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E] transition-colors" required>{{ old('content', $review->content) }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" class="px-6 py-2 border border-transparent rounded-lg text-white hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-[#E53E3E] text-white font-bold rounded-lg hover:bg-red-700 transition-colors">
                        Update Review
                    </button>
                </div>
            </form>
        </div>
        <p class="text-center text-xs text-gray-500 mt-4">
            You are editing your review as {{ Auth::user()->name }}. Last updated: {{ $review->updated_at->format('M j, Y \a\t g:i A') }}.
        </p>
    </div>
</div>
@endsection 