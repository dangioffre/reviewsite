<x-layouts.app>
@php
    $list = \App\Models\ListModel::where('slug', $slug)->where('is_public', true)->with(['items.product', 'user'])->first();
@endphp

@if($list)
    <div class="min-h-screen bg-[#151515] py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white font-['Share_Tech_Mono'] mb-2">{{ $list->name }}</h1>
                <p class="text-[#A1A1AA] font-['Inter']">
                    Created by <span class="text-white font-semibold">{{ $list->user->name ?? 'Unknown' }}</span> • 
                    {{ $list->items ? $list->items->count() : 0 }} games
                </p>
            </div>

            <!-- Games Grid -->
            @if($list->items && $list->items->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($list->items as $item)
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6 hover:border-[#52525B] transition-all duration-200">
                            <div class="mb-4">
                                <h3 class="text-white font-bold font-['Share_Tech_Mono'] text-lg mb-2">{{ $item->product->name }}</h3>
                                <p class="text-[#A1A1AA] text-sm font-['Inter'] leading-relaxed">
                                    {{ Str::limit($item->product->description, 100) }}
                                </p>
                            </div>

                            <!-- Game Info -->
                            <div class="space-y-2 mb-4">
                                @if($item->product->overall_rating)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-white font-semibold text-sm">{{ number_format($item->product->overall_rating, 1) }}</span>
                                        <span class="text-[#A1A1AA] text-xs ml-1">/ 10</span>
                                    </div>
                                @endif

                                @if($item->product->release_date)
                                    <div class="text-[#A1A1AA] text-xs font-['Inter']">
                                        Released: {{ \Carbon\Carbon::parse($item->product->release_date)->format('M Y') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Genres -->
                            @if($item->product && $item->product->genres && $item->product->genres->count() > 0)
                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach($item->product->genres->take(3) as $genre)
                                        <span class="bg-[#7C3AED]/20 text-[#7C3AED] px-2 py-1 rounded text-xs font-semibold">
                                            {{ $genre->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- View Game Button -->
                            <a href="{{ route('games.show', $item->product->slug) }}" 
                               class="block w-full bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-center py-2 px-4 rounded-lg font-semibold text-sm transition-colors font-['Inter']">
                                View Game
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-20 h-20 mx-auto mb-6 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a1 1 0 011-1h6a1 1 0 011 1v2M7 7h10" />
                    </svg>
                    <h3 class="text-2xl font-bold text-white font-['Share_Tech_Mono'] mb-2">Empty List</h3>
                    <p class="text-[#A1A1AA] font-['Inter']">This list doesn't have any games yet.</p>
                </div>
            @endif

            <!-- Back to Site -->
            <div class="text-center mt-12">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold transition-colors font-['Inter']">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Site
                </a>
            </div>
        </div>
    </div>
@else
    <div class="min-h-screen bg-[#151515] flex items-center justify-center">
        <div class="text-center">
            <svg class="w-20 h-20 mx-auto mb-6 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono'] mb-4">List Not Found</h1>
            <p class="text-[#A1A1AA] font-['Inter'] mb-6">This list doesn't exist or is not public.</p>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold transition-colors font-['Inter']">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Site
            </a>
                 </div>
     </div>
@endif
</x-layouts.app> 