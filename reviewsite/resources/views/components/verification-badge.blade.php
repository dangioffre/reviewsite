@props(['profile', 'size' => 'sm'])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
        default => 'w-4 h-4'
    };
    
    $textSizeClasses = match($size) {
        'xs' => 'text-xs',
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        default => 'text-xs'
    };
@endphp

@if($profile->isVerified())
    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full {{ $textSizeClasses }} font-medium bg-green-100 text-green-800" 
          title="Verified Channel">
        <svg class="{{ $sizeClasses }} text-green-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        Verified
    </span>
@elseif($profile->verification_status === 'requested')
    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full {{ $textSizeClasses }} font-medium bg-yellow-100 text-yellow-800" 
          title="Verification Requested">
        <svg class="{{ $sizeClasses }} text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
        </svg>
        Pending
    </span>
@elseif($profile->verification_status === 'in_review')
    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full {{ $textSizeClasses }} font-medium bg-blue-100 text-blue-800" 
          title="Under Review">
        <svg class="{{ $sizeClasses }} text-blue-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 3.314-2.686 6-6 6s-6-2.686-6-6a5.98 5.98 0 01.332-1.973z" clip-rule="evenodd"></path>
        </svg>
        In Review
    </span>
@endif
