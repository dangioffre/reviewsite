<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dan & Brian Reviews')</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono:wght@400&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Styles -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body {
                    background: #1A1A1B;
                    font-family: 'Inter', sans-serif;
                    color: #FFFFFF;
                }
                
                /* Audio Player Styles */
                .audio-player {
                    background: #27272A;
                    border: 1px solid #3F3F46;
                    border-radius: 8px;
                    padding: 1rem;
                    margin: 1rem 0;
                }
                
                .audio-player audio {
                    width: 100%;
                    background: #1A1A1B;
                    border-radius: 8px;
                }
                
                .audio-player audio::-webkit-media-controls-panel {
                    background-color: #1A1A1B;
                }
                
                .audio-player audio::-webkit-media-controls-play-button,
                .audio-player audio::-webkit-media-controls-pause-button {
                    background-color: #E53E3E;
                    border-radius: 50%;
                }
                
                .audio-player audio::-webkit-media-controls-timeline {
                    background-color: #3F3F46;
                    border-radius: 4px;
                }
                
                .audio-player audio::-webkit-media-controls-current-time-display,
                .audio-player audio::-webkit-media-controls-time-remaining-display {
                    color: #FFFFFF;
                }
            </style>
        @endif
        
        @stack('styles')
    </head>
    <body>
        <!-- Header -->
        <x-navbar />
        
        <!-- Main Content -->
        <main class="bg-[#1A1A1B] min-h-screen">
            @yield('content')
        </main>
        
        <!-- Report Review Modal -->
        <div id="report-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
            <div class="bg-[#27272A] rounded-xl border border-[#3F3F46] p-8 w-full max-w-md">
                <form id="report-form" method="POST">
                    @csrf
                    <h2 class="text-xl font-bold mb-4">Report a Review</h2>
                    <p class="text-sm text-[#A1A1AA] mb-6">You are reporting a review. Please select a reason below.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="reason" class="block text-sm font-medium text-white mb-2">Reason</label>
                            <select id="reason" name="reason" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white focus:border-[#E53E3E] focus:ring-[#E53E3E]" required>
                                @foreach(\App\Models\Report::getReasons() as $key => $reason)
                                    <option value="{{ $key }}">{{ $reason }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="additional_info" class="block text-sm font-medium text-white mb-2">Additional Information (Optional)</label>
                            <textarea id="additional_info" name="additional_info" rows="4" class="w-full rounded-lg border-[#3F3F46] bg-[#1A1A1B] p-3 text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-[#E53E3E]"></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" id="cancel-report" class="px-6 py-2 border border-transparent rounded-lg text-white hover:bg-gray-700 transition-colors">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-[#E53E3E] text-white font-bold rounded-lg hover:bg-red-700 transition-colors">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts
        
        @stack('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const reportModal = document.getElementById('report-modal');
                const reportForm = document.getElementById('report-form');
                const cancelReportBtn = document.getElementById('cancel-report');

                document.querySelectorAll('.report-button').forEach(button => {
                    button.addEventListener('click', function () {
                        const reportUrl = this.dataset.reportUrl;
                        reportForm.action = reportUrl;
                        reportModal.classList.remove('hidden');
                    });
                });

                cancelReportBtn.addEventListener('click', () => {
                    reportModal.classList.add('hidden');
                });

                reportModal.addEventListener('click', function(event) {
                    if (event.target === reportModal) {
                        reportModal.classList.add('hidden');
                    }
                });
            });
        </script>
    </body>
</html> 