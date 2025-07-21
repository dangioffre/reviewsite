@props([
    'title' => '',
    'value' => 0,
    'icon' => '',
    'color' => 'blue',
    'trend' => null,
    'trendDirection' => 'up',
    'description' => ''
])

@php
    $colorClasses = [
        'blue' => 'bg-[#2563EB] text-white',
        'green' => 'bg-[#22C55E] text-white',
        'orange' => 'bg-[#F59E42] text-white',
        'yellow' => 'bg-[#FACC15] text-white',
        'purple' => 'bg-[#A78BFA] text-white',
        'red' => 'bg-[#E53E3E] text-white',
        'indigo' => 'bg-[#6366F1] text-white',
    ];
    
    $trendColors = [
        'up' => 'text-[#22C55E]',
        'down' => 'text-[#E53E3E]',
        'neutral' => 'text-[#A1A1AA]'
    ];
@endphp

<div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl p-6 border border-[#3F3F46]">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                @if($icon)
                    <div class="{{ $colorClasses[$color] ?? $colorClasses['blue'] }} p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($icon === 'review')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            @elseif($icon === 'like')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            @elseif($icon === 'star')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            @elseif($icon === 'game')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @elseif($icon === 'stream')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            @endif
                        </svg>
                    </div>
                @endif
                <h3 class="text-lg font-semibold text-white font-['Share_Tech_Mono']">{{ $title }}</h3>
            </div>
            
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-white font-['Share_Tech_Mono']">{{ $value }}</span>
                @if($trend !== null)
                    <span class="text-sm {{ $trendColors[$trendDirection] ?? $trendColors['neutral'] }} font-['Inter']">
                        @if($trendDirection === 'up')
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        @elseif($trendDirection === 'down')
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                            </svg>
                        @endif
                        {{ $trend }}
                    </span>
                @endif
            </div>
            
            @if($description)
                <p class="text-sm text-[#A1A1AA] mt-1 font-['Inter']">{{ $description }}</p>
            @endif
        </div>
    </div>
</div> 
