<div>
    <div class="flex gap-4 mb-2 text-xs text-[#A1A1AA] font-semibold">
        <span>{{ $stats['have'] }} Have</span>
        <span>{{ $stats['want'] }} Want</span>
        <span>{{ $stats['played'] }} Played</span>
    </div>
    <div class="flex gap-2 mb-2">
        <button wire:click="toggle('have')" class="px-4 py-2 rounded-full font-bold text-xs border-2 transition-colors duration-200 focus:outline-none {{ $have ? 'bg-green-600 border-green-600 text-white shadow-lg' : 'bg-[#232326] border-[#232326] text-[#A1A1AA] hover:bg-green-700 hover:border-green-700 hover:text-white' }}">Have</button>
        <button wire:click="toggle('want')" class="px-4 py-2 rounded-full font-bold text-xs border-2 transition-colors duration-200 focus:outline-none {{ $want ? 'bg-blue-600 border-blue-600 text-white shadow-lg' : 'bg-[#232326] border-[#232326] text-[#A1A1AA] hover:bg-blue-700 hover:border-blue-700 hover:text-white' }}">Want</button>
        <button wire:click="toggle('played')" class="px-4 py-2 rounded-full font-bold text-xs border-2 transition-colors duration-200 focus:outline-none {{ $played ? 'bg-yellow-500 border-yellow-500 text-black shadow-lg' : 'bg-[#232326] border-[#232326] text-[#A1A1AA] hover:bg-yellow-600 hover:border-yellow-600 hover:text-black' }}">Played</button>
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
