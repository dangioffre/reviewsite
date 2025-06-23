@php
    $isStaff = $type === 'staff';
@endphp
<div class="relative flex group transition-transform duration-200 hover:scale-[1.025] hover:shadow-2xl">
    <div class="w-2 rounded-l-xl {{ $isStaff ? 'bg-gradient-to-b from-[#E53E3E] to-[#DC2626]' : 'bg-gradient-to-b from-[#2563EB] to-[#1e40af]' }} mr-4"></div>
    <div class="bg-[#1A1A1B] rounded-xl p-6 border border-[#3F3F46] flex-1 flex flex-col">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 {{ $isStaff ? 'bg-gradient-to-r from-[#E53E3E] to-[#DC2626]' : 'bg-gradient-to-r from-[#2563EB] to-[#1e40af]' }} rounded-full flex items-center justify-center">
                @if($isStaff)
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                @else
                    <span class="text-white font-bold font-['Share_Tech_Mono']">{{ substr($review->user->name, 0, 1) }}</span>
                @endif
            </div>
            <div>
                <span class="text-white font-semibold font-['Inter']">{{ $review->user->name }}</span>
                @if($isStaff)
                    <span class="ml-2 px-2 py-0.5 bg-[#E53E3E] text-white text-xs rounded font-bold align-middle">STAFF</span>
                @endif
                <div class="text-[#A1A1AA] text-xs font-['Inter']">{{ $review->created_at->format('M d, Y') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2 mb-2">
            <span class="text-yellow-400 font-bold text-xl font-['Share_Tech_Mono']">{{ $review->rating }}/10</span>
            <div class="flex">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ ($review->rating/2) >= $i ? 'text-[#FFC107]' : 'text-[#3F3F46]' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
            </div>
        </div>
        <div class="font-bold text-lg mb-1 text-white font-['Inter']">{{ $review->title }}</div>
        <div class="text-[#A1A1AA] leading-relaxed font-['Inter']">
            @if(isset($review->content) && $review->content)
                <p>{{ Str::limit($review->content, 150) }}</p>
                @if(isset($review->slug) && strlen($review->content) > 150)
                    <a href="{{ route('games.reviews.show', [$review->product, $review]) }}" class="text-[#2563EB] hover:text-blue-400 font-semibold mt-2 inline-block">
                        Read Full Review →
                    </a>
                @endif
            @else
                <p>{{ $review->review ?? 'No review content available.' }}</p>
            @endif
        </div>
    </div>
</div> 