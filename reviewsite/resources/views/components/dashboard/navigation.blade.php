@props([
    'currentPage' => 'dashboard'
])

@php
    $navItems = [
        'dashboard' => [
            'label' => 'Overview',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />',
            'route' => 'dashboard'
        ],
        'reviews-and-likes' => [
            'label' => 'Reviews & Likes',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
            'route' => 'dashboard.reviews-and-likes'
        ],
        'lists' => [
            'label' => 'Lists',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />',
            'route' => 'dashboard.lists'
        ],
        'collection' => [
            'label' => 'Game Collection',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'route' => 'dashboard.collection'
        ],
    ];
@endphp

<nav class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-4">
    <div class="space-y-2">
        @foreach($navItems as $key => $item)
            <a href="{{ route($item['route']) }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors font-['Inter'] {{ $currentPage === $key ? 'bg-[#2563EB]/20 text-[#2563EB] border border-[#2563EB]/30' : 'text-[#A1A1AA] hover:bg-[#3F3F46] hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span class="font-medium">{{ $item['label'] }}</span>
                @if($currentPage === $key)
                    <div class="ml-auto w-2 h-2 bg-[#2563EB] rounded-full"></div>
                @endif
            </a>
        @endforeach
    </div>
</nav> 