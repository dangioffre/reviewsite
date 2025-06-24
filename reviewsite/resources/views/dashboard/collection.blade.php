<x-layouts.app>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Game Collection</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Your personal game library and stats</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Navigation Sidebar -->
            <div class="lg:col-span-1">
                <x-dashboard.navigation current-page="collection" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Collection Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $gameStatuses->where('have', true)->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Owned</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $gameStatuses->where('want', true)->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Wishlist</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $gameStatuses->where('played', true)->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Played</div>
                        </div>
                    </div>
                </div>

                <!-- Game List -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Your Games</h2>
                    @forelse($gameStatuses as $status)
                        <div class="mb-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-lg text-gray-900 dark:text-white">
                                        {{ $status->product->name }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        @if($status->product->genre)
                                            {{ $status->product->genre->name }}
                                        @endif
                                        @if($status->product->platform)
                                            • {{ $status->product->platform->name }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    @if($status->have)
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs">Owned</span>
                                    @endif
                                    @if($status->want)
                                        <span class="inline-block px-2 py-1 bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 rounded text-xs">Wishlist</span>
                                    @endif
                                    @if($status->played)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded text-xs">Played</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>No games in your collection yet.</p>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($gameStatuses->hasPages())
                        <div class="mt-8">
                            {{ $gameStatuses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app> 