<div class="flex items-center gap-1">
    <button wire:click="toggleLike" 
            class="focus:outline-none transition-colors flex items-center {{ $liked ? 'text-red-500' : 'text-gray-400 hover:text-red-400' }} {{ $canLike ? 'cursor-pointer' : 'cursor-not-allowed' }}"
            {{ !$canLike ? 'disabled' : '' }}>
        <!-- Heart SVG -->
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
        </svg>
        <span class="ml-1 font-bold flex items-center justify-center transition {{ $liked ? 'text-red-500' : 'text-gray-200' }}" 
              style="width: 1.7rem; height: 1.7rem; border-radius: 9999px; font-size: 1.1rem;">
            {{ $likesCount }}
        </span>
    </button>
    @if(!$canLike)
        <span class="ml-2 text-xs text-gray-400">Login to like</span>
    @endif
</div>
