@props(['list'])

<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] overflow-hidden hover:border-[#2563EB] transition-all duration-300 group">
    <!-- List Header -->
    <div class="p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono'] line-clamp-2 group-hover:text-[#2563EB] transition-colors">
                    <a href="{{ route('lists.public', $list->slug) }}">{{ $list->name }}</a>
                </h3>
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-block bg-[#2563EB] text-white px-2 py-1 rounded-full text-xs font-medium">
                        {{ $list->category_label }}
                    </span>
                    <span class="text-[#A1A1AA] text-sm">{{ $list->items_count }} {{ Str::plural('game', $list->items_count) }}</span>
                </div>
            </div>
        </div>

        <!-- List Description -->
        @if($list->description)
            <p class="text-[#A1A1AA] text-sm mb-4 line-clamp-3 font-['Inter']">{{ $list->description }}</p>
        @endif

        <!-- List Creator -->
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] rounded-full flex items-center justify-center">
                <span class="text-white text-xs font-bold">{{ strtoupper(substr($list->user->name, 0, 1)) }}</span>
            </div>
            <div>
                <p class="text-white text-sm font-medium">{{ $list->user->name }}</p>
                <p class="text-[#71717A] text-xs">{{ $list->created_at->format('M j, Y') }}</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex items-center gap-4 text-sm text-[#A1A1AA] mb-4">
            <span class="flex items-center gap-1" title="{{ $list->items_count }} games">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $list->items_count }}
            </span>
            <span class="flex items-center gap-1" title="{{ $list->followers_count }} followers">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 12.052c1.995 0 3.5-1.505 3.5-3.5s-1.505-3.5-3.5-3.5-3.5 1.505-3.5 3.5 1.505 3.5 3.5 3.5zM9 13c-2.633 0-6 1.367-6 4v2h12v-2c0-2.633-3.367-4-6-4z"/>
                </svg>
                {{ $list->followers_count }}
            </span>
            <span class="flex items-center gap-1" title="{{ $list->comments_count }} comments">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                </svg>
                {{ $list->comments_count }}
            </span>
        </div>

        <!-- Sample Games Preview -->
        @if($list->items->count() > 0)
            <div>
                <h4 class="text-sm font-medium text-white mb-2 font-['Inter']">Sample Games:</h4>
                <div class="flex flex-wrap gap-1">
                    @foreach($list->items->take(3) as $item)
                        <span class="inline-block bg-[#18181B] text-[#A1A1AA] px-2 py-1 rounded text-xs" title="{{ $item->product->name }}">
                            {{ Str::limit($item->product->name, 15) }}
                        </span>
                    @endforeach
                    @if($list->items->count() > 3)
                        <span class="inline-block bg-[#18181B] text-[#71717A] px-2 py-1 rounded text-xs">
                            +{{ $list->items->count() - 3 }} more
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Action Button -->
    <div class="px-6 pb-6">
        <a href="{{ route('lists.public', $list->slug) }}" 
           class="w-full bg-gradient-to-r from-[#2563EB] to-[#3B82F6] hover:from-[#1D4ED8] hover:to-[#2563EB] text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2 group">
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
            View List
        </a>
    </div>
</div> 
