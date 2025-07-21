@props([
    'item'
])

@php
    $isReview = $item['data']->product_id !== null;
    $isComment = $item['data']->episode_id !== null;
    $product = $isReview ? $item['data']->product : null;
    $episode = $isComment ? $item['data']->episode : null;
    $podcast = $isComment ? $item['data']->episode->podcast : null;
@endphp

<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] overflow-hidden">
    <div class="p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['type'] === 'review' ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'bg-[#E53E3E]/20 text-[#E53E3E] border border-[#E53E3E]/30' }}">
                        {{ $item['type'] === 'review' ? 'My Review' : 'Liked Review' }}
                    </span>
                    
                    @if($isReview && $product && $product->genre)
                        <span class="text-sm text-[#A1A1AA] font-['Inter']">{{ $product->genre->name }}</span>
                    @elseif($isComment && $podcast)
                        <span class="text-sm text-[#A1A1AA] font-['Inter']">Podcast Comment</span>
                    @endif
                    
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 10; $i++)
                            <span class="text-sm {{ $i <= $item['data']->rating ? 'text-yellow-400' : 'text-[#3F3F46]' }}">★</span>
                        @endfor
                        <span class="text-sm text-[#A1A1AA] ml-1 font-['Inter']">({{ $item['data']->rating }}/10)</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-semibold text-white mb-2 font-['Inter']">
                    @if($isReview && $product)
                        <a href="{{ route($product->type === 'game' ? 'games.reviews.show' : 'tech.reviews.show', [$product, $item['data']]) }}" class="hover:text-[#2563EB] transition-colors">
                            {{ $item['data']->title }}
                        </a>
                    @elseif($isComment && $episode && $podcast)
                        <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" class="hover:text-[#2563EB] transition-colors">
                            {{ $item['data']->title }}
                        </a>
                    @endif
                </h3>
                
                <p class="text-sm text-[#A1A1AA] mb-3 font-['Inter']">
                    @if($item['type'] === 'like')
                        by <span class="font-medium">{{ $item['data']->user->name }}</span> • 
                    @endif
                    @if($isReview && $product)
                        Review for <a href="{{ route($product->type === 'game' ? 'games.show' : 'tech.show', $product) }}" class="text-[#2563EB] hover:underline">{{ $product->name }}</a>
                    @elseif($isComment && $episode && $podcast)
                        Comment on <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" class="text-[#2563EB] hover:underline">{{ $episode->title }}</a>
                    @endif
                </p>
                
                <div class="text-[#A1A1AA] text-sm line-clamp-3 mb-4 font-['Inter']">
                    {{ Str::limit(strip_tags($item['data']->content), 200) }}
                </div>
                
                <div class="flex items-center justify-between text-sm text-[#71717A] font-['Inter']">
                    <div class="flex items-center gap-4">
                        <span>{{ $item['data']->created_at->format('M j, Y') }}</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-[#E53E3E]" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" /></svg>
                            <span>{{ $item['data']->likes_count ?? 0 }}</span>
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @if($item['type'] === 'review')
                             <a href="{{ $isReview && $product ? route($product->type === 'game' ? 'games.reviews.edit' : 'tech.reviews.edit', [$product, $item['data']]) : '#' }}" class="text-[#2563EB] hover:text-[#3B82F6] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                        @else
                            <span class="text-xs text-[#71717A]">Liked {{ $item['date']->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
