@props([
    'productId',
    'buttonText' => 'Add to lists',
    'buttonClass' => 'w-full bg-[#a020f0] hover:bg-[#7c3aed] text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors flex items-center justify-center shadow-md',
    'size' => 'default' // default, small, large
])

@php
    $sizeClasses = [
        'small' => 'py-1 px-2 text-xs',
        'default' => 'py-2 px-4 text-sm',
        'large' => 'py-3 px-6 text-base'
    ];
    
    $iconSizes = [
        'small' => 'w-3 h-3',
        'default' => 'w-4 h-4',
        'large' => 'w-5 h-5'
    ];
    
    $appliedSizeClass = $sizeClasses[$size] ?? $sizeClasses['default'];
    $appliedIconSize = $iconSizes[$size] ?? $iconSizes['default'];
@endphp

@livewire('add-to-list-modal', [
    'productId' => $productId, 
    'buttonText' => $buttonText, 
    'buttonClass' => $buttonClass . ' ' . $appliedSizeClass, 
    'iconSize' => $appliedIconSize
]) 