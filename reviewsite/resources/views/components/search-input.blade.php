@props([
    'name' => 'search',
    'placeholder' => 'Search...',
    'value' => '',
    'class' => '',
    'icon' => true
])

<div class="relative {{ $class }}">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-[#71717A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    @endif
    <input type="text" 
           name="{{ $name }}" 
           value="{{ $value }}" 
           placeholder="{{ $placeholder }}"
           {{ $attributes->merge([
               'class' => ($icon ? 'pl-10' : 'pl-4') . ' pr-4 py-3 w-full bg-[#18181B] border border-[#3F3F46] rounded-lg text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent font-[\'Inter\']'
           ]) }}>
</div> 
