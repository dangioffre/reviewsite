@props(['totalLists' => 0, 'totalGames' => 0, 'totalCategories' => 0])

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6 text-center">
        <div class="text-3xl font-bold text-[#2563EB] mb-2 font-['Share_Tech_Mono']">{{ number_format($totalLists) }}</div>
        <div class="text-[#A1A1AA] font-['Inter']">Public Lists</div>
    </div>
    
    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6 text-center">
        <div class="text-3xl font-bold text-[#059669] mb-2 font-['Share_Tech_Mono']">{{ number_format($totalGames) }}</div>
        <div class="text-[#A1A1AA] font-['Inter']">Games Listed</div>
    </div>
    
    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6 text-center">
        <div class="text-3xl font-bold text-[#7C3AED] mb-2 font-['Share_Tech_Mono']">{{ $totalCategories }}</div>
        <div class="text-[#A1A1AA] font-['Inter']">Categories</div>
    </div>
</div> 