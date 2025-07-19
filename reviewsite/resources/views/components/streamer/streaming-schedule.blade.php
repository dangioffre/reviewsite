@props(['streamerProfile'])

@if($streamerProfile->schedules->count() > 0)
@php
    $activeSchedules = $streamerProfile->schedules->where('is_active', true)->sortBy('day_of_week');
    $now = now();
    $today = $now->dayOfWeek;
    
    // Find next upcoming stream
    $nextStream = null;
    $dayColors = [
        0 => 'from-red-500 to-red-600',    // Sunday
        1 => 'from-blue-500 to-blue-600',   // Monday  
        2 => 'from-green-500 to-green-600', // Tuesday
        3 => 'from-yellow-500 to-yellow-600', // Wednesday
        4 => 'from-purple-500 to-purple-600', // Thursday
        5 => 'from-pink-500 to-pink-600',   // Friday
        6 => 'from-indigo-500 to-indigo-600' // Saturday
    ];
    
    foreach($activeSchedules as $schedule) {
        $streamDay = \Carbon\Carbon::create()->dayOfWeek($schedule->day_of_week);
        $streamStart = $streamDay->setTimeFromTimeString($schedule->start_time);
        
        if ($streamStart->isFuture() || ($streamStart->isToday() && $streamStart->gt($now))) {
            if (!$nextStream || $streamStart->lt($nextStream['time'])) {
                $nextStream = [
                    'schedule' => $schedule,
                    'time' => $streamStart,
                    'day' => $streamDay
                ];
            }
        }
    }
@endphp

<div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-xl border border-zinc-700 overflow-hidden">
    <!-- Header -->
    <div class="bg-black/20 backdrop-blur-sm p-6 border-b border-zinc-600/50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Streaming Schedule</h2>
            </div>
            
            <!-- Timezone Info -->
            <div class="text-right">
                <div class="text-zinc-400 text-sm" id="timezone-display">
                    Streamer's Time
                </div>
                <button type="button" 
                        class="text-blue-400 hover:text-blue-300 text-xs underline transition-colors" 
                        onclick="toggleTimezone()">
                    Convert to your time
                </button>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Next Stream Highlight -->
        @if($nextStream)
        <div class="mb-8 p-6 bg-gradient-to-r {{ $dayColors[$nextStream['schedule']->day_of_week] ?? 'from-blue-500 to-blue-600' }} rounded-2xl text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 002 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="font-semibold text-lg">Next Stream</span>
                    </div>
                    <div class="mb-1">
                        <span class="text-2xl font-bold">
                            {{ $nextStream['day']->format('l') }}
                        </span>
                    </div>
                    <div class="flex items-center text-white/90">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium stream-time" 
                              data-start="{{ $nextStream['schedule']->start_time }}"
                              data-end="{{ $nextStream['schedule']->end_time }}"
                              data-timezone="{{ $nextStream['schedule']->timezone }}">
                            {{ \Carbon\Carbon::parse($nextStream['schedule']->start_time)->format('g:i A') }}
                            -
                            {{ \Carbon\Carbon::parse($nextStream['schedule']->end_time)->format('g:i A') }}
                        </span>
                    </div>
                    <div class="flex items-center text-white/60 text-sm mt-1">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="timezone-label" data-timezone="{{ $nextStream['schedule']->timezone }}">
                            @php
                                $timezoneMap = [
                                    'America/New_York' => 'EST',
                                    'America/Chicago' => 'CST', 
                                    'America/Denver' => 'MST',
                                    'America/Los_Angeles' => 'PST',
                                    'America/Phoenix' => 'MST',
                                    'Europe/London' => 'GMT',
                                    'Europe/Paris' => 'CET',
                                    'Asia/Tokyo' => 'JST',
                                    'Australia/Sydney' => 'AEST',
                                ];
                                $nextStreamTimezone = $timezoneMap[$nextStream['schedule']->timezone] ?? $nextStream['schedule']->timezone;
                            @endphp
                            {{ $nextStreamTimezone }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white/80 text-sm mb-1">
                        {{ $nextStream['time']->diffForHumans() }}
                    </div>
                    <div class="text-white/60 text-xs">
                        {{ $nextStream['time']->format('M j, Y') }}
                    </div>
                </div>
            </div>
            
            @if($nextStream['schedule']->notes)
            <div class="mt-4 p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                <p class="text-white/90 text-sm">{{ $nextStream['schedule']->notes }}</p>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Weekly Schedule Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($activeSchedules as $schedule)
                @php
                    $isToday = $schedule->day_of_week == $today;
                    $dayName = \Carbon\Carbon::create()->dayOfWeek($schedule->day_of_week)->format('l');
                    $gradientClass = $dayColors[$schedule->day_of_week] ?? 'from-gray-500 to-gray-600';
                @endphp
                
                <div class="group relative bg-zinc-800/50 rounded-xl border border-zinc-600 overflow-hidden hover:border-blue-500 hover:bg-zinc-800/70 transition-all duration-300 {{ $isToday ? 'ring-2 ring-blue-500/50 shadow-lg' : '' }}">
                    <!-- Day Header -->
                    <div class="bg-gradient-to-r {{ $gradientClass }} p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-white font-bold text-lg">{{ $dayName }}</div>
                            </div>
                            @if($isToday)
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Schedule Content -->
                    <div class="p-4">
                        <div class="flex items-center text-white mb-2">
                            <svg class="w-4 h-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold stream-time"
                                  data-start="{{ $schedule->start_time }}"
                                  data-end="{{ $schedule->end_time }}"
                                  data-timezone="{{ $schedule->timezone }}">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                                <span class="text-zinc-400 mx-1">-</span>
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                            </span>
                        </div>
                        
                        <!-- Timezone Display -->
                        <div class="flex items-center text-zinc-400 text-xs mb-3">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="timezone-label" data-timezone="{{ $schedule->timezone }}">
                                @php
                                    $timezoneMap = [
                                        'America/New_York' => 'EST',
                                        'America/Chicago' => 'CST', 
                                        'America/Denver' => 'MST',
                                        'America/Los_Angeles' => 'PST',
                                        'America/Phoenix' => 'MST',
                                        'Europe/London' => 'GMT',
                                        'Europe/Paris' => 'CET',
                                        'Asia/Tokyo' => 'JST',
                                        'Australia/Sydney' => 'AEST',
                                    ];
                                    $shortTimezone = $timezoneMap[$schedule->timezone] ?? $schedule->timezone;
                                @endphp
                                {{ $shortTimezone }}
                            </span>
                        </div>
                        
                        @if($schedule->notes)
                            <div class="p-3 bg-zinc-900/50 rounded-lg border border-zinc-700">
                                <p class="text-zinc-300 text-sm leading-relaxed">
                                    {{ Str::limit($schedule->notes, 80) }}
                                </p>
                            </div>
                        @endif
                        
                        @if($isToday)
                            <div class="mt-3 flex items-center text-blue-400 text-sm font-medium">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Today
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- No Schedule Message -->
        @if($activeSchedules->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No Regular Schedule</h3>
                <p class="text-zinc-400">This streamer doesn't have a set streaming schedule yet.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let usingLocalTime = false;
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Debug: Log detected timezone
    console.log('🌍 Detected user timezone:', userTimezone);
    
    // Initialize timezone display
    updateTimezoneDisplay();
    
    function updateTimezoneDisplay() {
        const timezoneDisplay = document.getElementById('timezone-display');
        if (timezoneDisplay) {
            timezoneDisplay.textContent = usingLocalTime ? 
                `Your Time (${userTimezone})` : 
                'Streamer\'s Time';
        }
    }
    
    // Store original times for conversion
    const originalTimes = new Map();
    
    // Initialize original times storage
    const streamTimeElements = document.querySelectorAll('.stream-time');
    console.log('🏁 INITIALIZING - Found stream time elements:', streamTimeElements.length);
    
    streamTimeElements.forEach((timeEl, index) => {
        const startTime = timeEl.dataset.start;
        const endTime = timeEl.dataset.end;
        const timezone = timeEl.dataset.timezone;
        const originalText = timeEl.textContent.trim();
        
        console.log(`📊 Element ${index} data:`, {
            startTime,
            endTime,
            timezone,
            originalText,
            element: timeEl
        });
        
        originalTimes.set(timeEl, {
            start: startTime,
            end: endTime,
            original: originalText
        });
    });
    
    console.log('💾 Stored original times for', originalTimes.size, 'elements');
    
    // Global function for timezone conversion
    window.toggleTimezone = function() {
        usingLocalTime = !usingLocalTime;
        
        console.log('🔄 TOGGLE TIMEZONE - Now using local time:', usingLocalTime);
        
        const streamTimes = document.querySelectorAll('.stream-time');
        const toggleBtn = document.querySelector('[onclick="toggleTimezone()"]');
        
        console.log('📋 Found stream time elements:', streamTimes.length);
        
        streamTimes.forEach((timeEl, index) => {
            try {
                const originalData = originalTimes.get(timeEl);
                if (!originalData) {
                    console.log(`❌ No original data for element ${index}`);
                    return;
                }
                
                const startTime = originalData.start;
                const endTime = originalData.end;
                const streamerTimezone = timeEl.dataset.timezone || 'America/New_York';
                
                console.log(`🔄 Processing element ${index}:`, {
                    startTime,
                    endTime,
                    streamerTimezone,
                    originalText: originalData.original
                });
                
                if (usingLocalTime) {
                    // Convert from streamer timezone to user's local timezone
                    const convertedStart = convertTimezone(startTime, streamerTimezone, userTimezone);
                    const convertedEnd = convertTimezone(endTime, streamerTimezone, userTimezone);
                    
                    console.log(`✅ Conversion results for element ${index}:`, {
                        convertedStart,
                        convertedEnd
                    });
                    
                    if (convertedStart && convertedEnd) {
                        const newHTML = `${convertedStart}<span class="text-zinc-400 mx-1">-</span>${convertedEnd}`;
                        console.log(`📝 Setting HTML for element ${index}:`, newHTML);
                        timeEl.innerHTML = newHTML;
                    } else {
                        // Fallback to original if conversion fails
                        console.log(`❌ Conversion failed, using original for element ${index}`);
                        timeEl.innerHTML = originalData.original;
                    }
                } else {
                    // Show original streamer times
                    console.log(`📝 Restoring original time for element ${index}:`, originalData.original);
                    timeEl.innerHTML = originalData.original;
                }
            } catch (error) {
                console.error(`❌ Error converting time for element ${index}:`, error);
                // Keep original time on error
                const originalData = originalTimes.get(timeEl);
                if (originalData) {
                    timeEl.innerHTML = originalData.original;
                }
            }
        });
        
        // Update timezone labels
        const timezoneLabels = document.querySelectorAll('.timezone-label');
        timezoneLabels.forEach(label => {
            if (usingLocalTime) {
                // Show user's timezone abbreviation
                const userTzParts = userTimezone.split('/');
                const userTzShort = userTzParts[userTzParts.length - 1].replace('_', ' ');
                label.textContent = `Your Time (${userTzShort})`;
            } else {
                // Restore original timezone
                const originalTz = label.dataset.timezone;
                const timezoneMap = {
                    'America/New_York': 'EST',
                    'America/Chicago': 'CST', 
                    'America/Denver': 'MST',
                    'America/Los_Angeles': 'PST',
                    'America/Phoenix': 'MST',
                    'Europe/London': 'GMT',
                    'Europe/Paris': 'CET',
                    'Asia/Tokyo': 'JST',
                    'Australia/Sydney': 'AEST',
                };
                label.textContent = timezoneMap[originalTz] || originalTz;
            }
        });
        
        // Update button text
        if (toggleBtn) {
            toggleBtn.textContent = usingLocalTime ? 
                'Show streamer time' : 
                'Convert to your time';
        }
        
        updateTimezoneDisplay();
    };
    
    function convertTimezone(timeStr, fromTimezone, toTimezone) {
        try {
            if (!timeStr) return null;
            
            console.log('🔄 RAW INPUT:', timeStr, 'type:', typeof timeStr);
            
            // Clean and parse the time - handle both "HH:MM" and "YYYY-MM-DD HH:MM:SS" formats
            let cleanTime = String(timeStr).trim();
            
            // Check if this is a full datetime string (YYYY-MM-DD HH:MM:SS)
            if (cleanTime.includes(' ') && cleanTime.includes('-')) {
                // Extract just the time portion from "YYYY-MM-DD HH:MM:SS"
                const parts = cleanTime.split(' ');
                if (parts.length >= 2) {
                    cleanTime = parts[1]; // Get the "HH:MM:SS" part
                }
            }
            
            // Remove seconds if present (e.g., "18:00:00" -> "18:00")
            const timeParts = cleanTime.split(':');
            if (timeParts.length >= 2) {
                cleanTime = timeParts[0] + ':' + timeParts[1];
            }
            
            console.log('🧹 CLEANED TIME:', cleanTime);
            
            // Parse hours and minutes with validation
            const timeMatch = cleanTime.match(/^(\d{1,2}):(\d{2})$/);
            if (!timeMatch) {
                throw new Error(`Invalid time format: ${cleanTime}`);
            }
            
            const hours = parseInt(timeMatch[1], 10);
            const minutes = parseInt(timeMatch[2], 10);
            
            if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
                throw new Error(`Time out of range: ${hours}:${minutes}`);
            }
            
            console.log('⏰ PARSED:', { hours, minutes });
            
            // Ultra-simple conversion using built-in JavaScript timezone handling
            // Create a date representing "today at this time" 
            const today = new Date();
            
            // Use a fixed date to avoid any DST edge cases
            const baseDate = new Date(2024, 5, 15); // June 15, 2024 (middle of year)
            baseDate.setHours(hours, minutes, 0, 0);
            
            console.log('📅 BASE DATE (local):', baseDate.toISOString());
            
            // Method: Use Intl.DateTimeFormat to handle the conversion
            // Create the time as if it exists in the streamer's timezone
            const formatter = new Intl.DateTimeFormat('en-US', {
                timeZone: fromTimezone,
                year: 'numeric',
                month: '2-digit', 
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            
            // Format the base date AS IF it were in the streamer's timezone
            const streamerLocalTime = formatter.formatToParts(baseDate);
            const streamerTimeObj = {};
            streamerLocalTime.forEach(part => {
                streamerTimeObj[part.type] = part.value;
            });
            
            console.log('🎯 STREAMER TIME PARTS:', streamerTimeObj);
            
            // Now create a new date with these parts, representing the TRUE time in streamer's zone
            const trueStreamerDate = new Date(
                parseInt(streamerTimeObj.year),
                parseInt(streamerTimeObj.month) - 1, // Month is 0-indexed
                parseInt(streamerTimeObj.day),
                hours, // Use original hours/minutes 
                minutes,
                0,
                0
            );
            
            console.log('🌍 TRUE STREAMER DATE:', trueStreamerDate.toISOString());
            
            // Finally, format this in the user's timezone
            const userTimeFormatter = new Intl.DateTimeFormat('en-US', {
                timeZone: userTimezone,
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            const result = userTimeFormatter.format(trueStreamerDate);
            
            console.log('✅ FINAL RESULT:', {
                originalInput: timeStr,
                cleanedTime: cleanTime,
                parsedHours: hours,
                parsedMinutes: minutes,
                streamerTz: fromTimezone,
                userTz: userTimezone,
                convertedTime: result
            });
            
            return result;
            
        } catch (error) {
            console.error('❌ CONVERSION ERROR:', error);
            console.log('📋 ERROR CONTEXT:', { timeStr, fromTimezone, userTimezone });
            
            // Simple fallback: parse what we can and format it
            try {
                const fallbackMatch = String(timeStr).match(/(\d{1,2}):(\d{2})/);
                if (fallbackMatch) {
                    const h = parseInt(fallbackMatch[1]);
                    const m = parseInt(fallbackMatch[2]);
                    
                    if (h >= 0 && h <= 23 && m >= 0 && m <= 59) {
                        const fallbackDate = new Date();
                        fallbackDate.setHours(h, m, 0, 0);
                        
                        return fallbackDate.toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                    }
                }
            } catch (fallbackError) {
                console.error('❌ FALLBACK FAILED:', fallbackError);
            }
            
            return `${timeStr} (conversion failed)`;
        }
    }
});
</script>
@endpush
@endif 