<x-layouts.app>
    <x-slot name="title">{{ $product->name }} Reviews</x-slot>
<div class="container mx-auto py-8">
    <div class="flex flex-col md:flex-row gap-8 mb-8">
        <div class="md:w-1/2">
            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full rounded-lg border border-zinc-800 mb-4">
            @if($product->video)
                <div class="aspect-w-16 aspect-h-9 mb-4">
                    <iframe src="{{ $product->video }}" frameborder="0" allowfullscreen class="w-full h-64 rounded"></iframe>
                </div>
            @endif
        </div>
        <div class="md:w-1/2 flex flex-col gap-4">
            <h1 class="text-3xl font-mono font-bold text-red-600 mb-2">{{ $product->name }}</h1>
            <div class="text-xs uppercase tracking-wider text-red-600 font-mono mb-2">{{ ucfirst($product->type) }}</div>
            <div class="text-zinc-300 mb-4">{{ $product->description }}</div>
            @if($staffReview)
                <div class="bg-zinc-900 border-l-4 border-red-600 p-4 rounded mb-2">
                    <div class="font-mono text-red-600 font-bold mb-1">Staff Review</div>
                    <div class="text-zinc-200 mb-2">{{ $staffReview }}</div>
                    @if($staffRating)
                        <div class="text-yellow-400 font-mono">Staff Rating: <span class="font-bold">{{ $staffRating }}/10</span></div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-2xl font-mono font-bold text-red-600 mb-4">User Reviews</h2>
        @if($userReviews->count())
            <div class="space-y-6">
                @foreach($userReviews as $review)
                    <div class="bg-zinc-900 border border-zinc-800 rounded p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-white font-bold">{{ $review->user->name }}</span>
                            <span class="text-yellow-400 font-mono">Rating: <span class="font-bold">{{ $review->rating }}/10</span></span>
                        </div>
                        <div class="text-zinc-200">{{ $review->review }}</div>
                        <div class="text-xs text-zinc-500 mt-2">{{ $review->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-zinc-400 font-mono">No user reviews yet. Be the first to review!</div>
        @endif
    </div>

    @auth
    <div class="mb-8">
        <h2 class="text-xl font-mono font-bold text-red-600 mb-2">Leave a Review</h2>
        @if(session('success'))
            <div class="bg-green-900 text-green-200 rounded p-2 mb-2">{{ session('success') }}</div>
        @endif
        <form action="{{ route('reviews.store', $product) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="rating" class="font-mono text-white">Rating (1-10):</label>
                <input type="number" min="1" max="10" name="rating" id="rating" class="form-input bg-zinc-900 border-zinc-700 text-white rounded px-3 py-2 w-20" required value="{{ old('rating', 8) }}">
                @error('rating') <div class="text-red-600 font-mono text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="review" class="font-mono text-white">Your Review:</label>
                <textarea name="review" id="review" rows="4" class="form-input bg-zinc-900 border-zinc-700 text-white rounded px-3 py-2 w-full" required>{{ old('review') }}</textarea>
                @error('review') <div class="text-red-600 font-mono text-sm">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
    @else
        <div class="text-zinc-400 font-mono mb-8">Please <a href="{{ route('login') }}" class="text-red-600 underline">log in</a> to leave a review.</div>
    @endauth
</div>
</x-layouts.app> 