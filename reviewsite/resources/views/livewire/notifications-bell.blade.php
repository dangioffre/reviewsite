<div class="relative" x-data="{ open: false }" @click.away="open = false">
    @if(Auth::check() && $unreadCount > 0)
        <button @click="open = !open" class="relative text-gray-400 hover:text-white focus:outline-none">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ $unreadCount }}</span>
        </button>
    @endif

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 w-80 mt-2 origin-top-right bg-[#18181B] border border-[#3F3F46] rounded-md shadow-lg z-50"
         style="display: none;">
        <div class="py-2">
            <div class="px-4 py-2 text-sm font-bold text-gray-300 border-b border-[#3F3F46]">
                Notifications
            </div>
            <div class="divide-y divide-[#3F3F46] max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div wire:key="notification-{{ $notification->id }}" class="p-3 hover:bg-[#27272A] transition-colors duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-grow pr-4">
                                <a href="{{ route('podcasts.episodes.show', ['podcast' => $notification->data['podcast_slug'], 'episode' => $notification->data['episode_slug']]) }}#comment-{{$notification->data['comment_id']}}" class="text-sm text-gray-300">
                                    <p><span class="font-bold text-white">{{ $notification->data['commenter_name'] }}</span> commented on your episode: <span class="font-semibold text-white">{{ Illuminate\Support\Str::limit($notification->data['episode_title'], 35) }}</span></p>
                                </a>
                                <p class="text-xs text-gray-500 mt-1 pt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <button @click.prevent="$wire.markAsRead('{{ $notification->id }}')" class="flex-shrink-0 text-gray-500 hover:text-white focus:outline-none transition-colors" title="Mark as read">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-white">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
