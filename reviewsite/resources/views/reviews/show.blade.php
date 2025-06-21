<x-layouts.app>
    <x-slot name="title">{{ $product->name }} Reviews</x-slot>
<div class="container mx-auto py-8 bg-[#151515] min-h-screen">
    <div class="flex flex-col md:flex-row gap-8 mb-8">
        <div class="md:w-1/2">
            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full rounded-lg border border-[#3F3F46] mb-4">
            @if($product->video)
                <div class="aspect-w-16 aspect-h-9 mb-4">
                    <iframe src="{{ $product->video }}" frameborder="0" allowfullscreen class="w-full h-64 rounded border border-[#3F3F46]"></iframe>
                </div>
            @endif
        </div>
        <div class="md:w-1/2 flex flex-col gap-4">
            <h1 class="text-3xl font-['Share_Tech_Mono'] font-bold text-[#E53E3E] mb-2">{{ $product->name }}</h1>
            <div class="text-xs uppercase tracking-wider text-[#E53E3E] font-['Share_Tech_Mono'] mb-2">{{ ucfirst($product->type) }}</div>
            <div class="text-[#A1A1AA] mb-4 font-['Inter']">{{ $product->description }}</div>
            @if($staffReview)
                <div class="bg-[#27272A] border-l-4 border-[#E53E3E] p-4 rounded mb-2 shadow-md">
                    <div class="font-['Share_Tech_Mono'] text-[#E53E3E] font-bold mb-1">Staff Review</div>
                    <div class="text-white mb-2 font-['Inter']">{{ $staffReview }}</div>
                    @if($staffRating)
                        <div class="text-[#E53E3E] font-['Share_Tech_Mono']">Staff Rating: <span class="font-bold">{{ $staffRating }}/10</span></div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-2xl font-['Share_Tech_Mono'] font-bold text-[#E53E3E] mb-4">User Reviews</h2>
        @if($userReviews->count())
            <div class="space-y-6">
                @foreach($userReviews as $review)
                    <div class="bg-[#27272A] border border-[#3F3F46] rounded-lg shadow-md p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-['Share_Tech_Mono'] text-white font-bold">{{ $review->user->name }}</span>
                            <span class="text-[#E53E3E] font-['Share_Tech_Mono']">Rating: <span class="font-bold">{{ $review->rating }}/10</span></span>
                        </div>
                        <div class="text-white font-['Inter']">{{ $review->review }}</div>
                        <div class="text-xs text-[#A1A1AA] mt-2 font-['Inter']">{{ $review->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-[#A1A1AA] font-['Share_Tech_Mono']">No user reviews yet. Be the first to review!</div>
        @endif
    </div>

    @auth
    <div class="mb-8">
        <h2 class="text-xl font-['Share_Tech_Mono'] font-bold text-[#E53E3E] mb-2">Leave a Review</h2>
        @if(session('success'))
            <div class="bg-[#4CAF50] text-white rounded-lg p-2 mb-2 font-['Inter']">{{ session('success') }}</div>
        @endif
        <form action="{{ route('reviews.store', $product) }}" method="POST" class="space-y-4 bg-[#27272A] p-6 rounded-lg border border-[#3F3F46] shadow-md">
            @csrf
            <div>
                <label for="rating" class="font-['Share_Tech_Mono'] text-white">Rating (1-10):</label>
                <input type="number" min="1" max="10" name="rating" id="rating" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-2.5 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-[#2563EB] font-['Inter'] mt-1" required value="{{ old('rating', 8) }}">
                @error('rating') <div class="text-[#E53E3E] font-['Share_Tech_Mono'] text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="review" class="font-['Share_Tech_Mono'] text-white">Your Review:</label>
                <textarea name="review" id="review" rows="4" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-2.5 text-white placeholder-[#A1A1AA] focus:border-[#2563EB] focus:ring-[#2563EB] font-['Inter'] mt-1" required>{{ old('review') }}</textarea>
                @error('review') <div class="text-[#E53E3E] font-['Share_Tech_Mono'] text-sm">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-['Inter']">Submit Review</button>
        </form>
    </div>
    @else
        <div class="text-[#A1A1AA] font-['Share_Tech_Mono'] mb-8">Please <a href="{{ route('login') }}" class="text-[#E53E3E] underline">log in</a> to leave a review.</div>
    @endauth
</div>
</x-layouts.app> 