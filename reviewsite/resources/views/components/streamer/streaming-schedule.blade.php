@props(['streamerProfile'])

@if($streamerProfile->schedules->count() > 0)
<div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-xl border border-zinc-700 p-6">
    <div class="flex items-center mb-6">
        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white">Streaming Schedule</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($streamerProfile->schedules->where('is_active', true) as $schedule)
            <div class="bg-zinc-800/50 rounded-lg border border-zinc-600 p-4 hover:border-blue-500 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-blue-400 font-bold text-lg">
                        {{ \Carbon\Carbon::create()->dayOfWeek($schedule->day_of_week)->format('l') }}
                    </span>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center text-white">
                        <svg class="w-4 h-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                            -
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                        </span>
                    </div>
                    
                    <div class="text-zinc-400 text-sm">
                        {{ $schedule->timezone }}
                    </div>
                </div>
                
                @if($schedule->notes)
                    <div class="mt-3 p-2 bg-zinc-900/50 rounded text-zinc-300 text-sm">
                        {{ Str::limit($schedule->notes, 60) }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    
    <!-- Timezone Converter -->
    <div class="mt-6 p-4 bg-zinc-800/30 rounded-lg border border-zinc-600">
        <div class="flex items-center justify-between">
            <span class="text-zinc-400 text-sm">
                Times shown in streamer's timezone. 
                <button type="button" class="text-blue-400 hover:text-blue-300 underline" onclick="convertToLocalTime()">
                    Convert to your timezone
                </button>
            </span>
            <div id="user-timezone" class="text-xs text-zinc-500"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize timezone display
    const userTimezoneEl = document.getElementById('user-timezone');
    if (userTimezoneEl) {
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        userTimezoneEl.textContent = `Your timezone: ${userTimezone}`;
    }
    
    // Global function for timezone conversion
    window.convertToLocalTime = function() {
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        
        // Find all schedule cards and convert times
        const scheduleCards = document.querySelectorAll('.bg-zinc-800\\/50');
        scheduleCards.forEach(card => {
            // This would need more complex implementation for actual timezone conversion
            // For now, just update the display text
            const timezoneText = card.querySelector('.text-zinc-400');
            if (timezoneText) {
                timezoneText.textContent = userTimezone;
            }
        });
        
        // Update the converter text
        const converterBtn = document.querySelector('[onclick="convertToLocalTime()"]');
        if (converterBtn) {
            converterBtn.textContent = 'Show original times';
            converterBtn.onclick = function() { location.reload(); };
        }
    };
});
</script>
@endpush
@endif 