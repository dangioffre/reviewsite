<div class="w-full flex flex-col items-center mb-2">
    <div class="flex gap-2 mb-3">
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#232326] text-xs font-bold text-[#A1A1AA] border border-[#3F3F46]">
            <svg class="w-3 h-3 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
            {{ $stats['have'] }} Have
        </span>
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#232326] text-xs font-bold text-[#A1A1AA] border border-[#3F3F46]">
            <svg class="w-3 h-3 mr-1 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
            {{ $stats['want'] }} Want
        </span>
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#232326] text-xs font-bold text-[#A1A1AA] border border-[#3F3F46]">
            <svg class="w-3 h-3 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10"/></svg>
            {{ $stats['played'] }} Played
        </span>
    </div>
    <div class="flex gap-2 mb-2">
        <button wire:click="toggle('have')" class="px-5 py-2 rounded-full font-bold text-sm border-2 transition-all duration-200 focus:outline-none flex items-center gap-2 shadow-sm
            {{ $have ? 'bg-green-600 border-green-600 text-white shadow-lg scale-105' : 'bg-[#232326] border-[#232326] text-[#A1A1AA] hover:bg-green-700 hover:border-green-700 hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Have
        </button>
        <button wire:click="toggle('want')" class="px-5 py-2 rounded-full font-bold text-sm border-2 transition-all duration-200 focus:outline-none flex items-center gap-2 shadow-sm
            {{ $want ? 'bg-blue-600 border-blue-600 text-white shadow-lg scale-105' : 'bg-[#232326] border-[#232326] text-[#A1A1AA] hover:bg-blue-700 hover:border-blue-700 hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Want
        </button>
        <button wire:click="toggle('played')" class="px-5 py-2 rounded-full font-bold text-sm border-2 transition-all duration-200 focus:outline-none flex items-center gap-2 shadow-sm
            {{ $played ? 'bg-yellow-400 border-yellow-400 text-black shadow-lg scale-105' : 'bg-[#232326] border-[#232326] text-[#A1A1AA] hover:bg-yellow-500 hover:border-yellow-500 hover:text-black' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Played
        </button>
    </div>
    @if (session()->has('message'))
        <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-[#232326] text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm font-semibold animate-fade-in-out">
            {{ session('message') }}
        </div>
    @endif
    <style>
    @keyframes fade-in-out {
        0% { opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { opacity: 0; }
    }
    .animate-fade-in-out {
        animation: fade-in-out 1.5s both;
    }
    </style>
</div>
