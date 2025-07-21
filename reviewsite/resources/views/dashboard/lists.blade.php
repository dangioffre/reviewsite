<x-layouts.app>
<div class="min-h-screen bg-[#151515]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">My Lists</h1>
                    <p class="text-[#A1A1AA] text-lg font-['Inter']">Create, manage, and share your custom game lists</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-[#2563EB] hover:text-[#3B82F6] transition-colors font-['Inter']">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Navigation -->
        <div class="mb-8">
            <x-dashboard.navigation />
        </div>

        @livewire('user-lists')
    </div>
</div>
</x-layouts.app> 
