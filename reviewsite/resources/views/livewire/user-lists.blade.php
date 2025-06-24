<div class="space-y-8">
    <!-- Create New List -->
    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
        <form wire:submit.prevent="createList" class="flex gap-4 items-center">
            <input type="text" wire:model.defer="newListName" placeholder="New list name..." class="flex-1 bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]" />
            <button type="submit" class="bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-2 rounded-lg font-semibold transition-colors">Create</button>
        </form>
    </div>

    <!-- User Lists -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($lists as $list)
            <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6 relative">
                <div class="flex items-center justify-between mb-2">
                    @if($editingListId === $list->id)
                        <input type="text" wire:model.defer="editingListName" class="bg-[#18181B] border border-[#3F3F46] rounded-lg px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-[#2563EB]" />
                        <button wire:click="saveEdit({{ $list->id }})" class="ml-2 text-[#22C55E] hover:text-white">Save</button>
                        <button wire:click="$set('editingListId', null)" class="ml-2 text-[#E53E3E] hover:text-white">Cancel</button>
                    @else
                        <div class="text-lg font-bold text-white font-['Share_Tech_Mono']">{{ $list->name }}</div>
                        <div class="flex gap-2 items-center">
                            <button wire:click="startEditing({{ $list->id }})" class="text-[#2563EB] hover:text-white" title="Rename"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                            <button wire:click="togglePublic({{ $list->id }})" class="text-[#7C3AED] hover:text-white" title="Toggle Public">
                                @if($list->is_public)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z" /></svg>
                                @else
                                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h-1v-4h-1" /></svg>
                                @endif
                            </button>
                            <button onclick="navigator.clipboard.writeText('{{ route('lists.public', $list->slug) }}')" class="text-[#22C55E] hover:text-white" title="Copy Link"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8m-4-4v8m0-8l-4 4m4-4l4 4" /></svg></button>
                            <button wire:click="deleteList({{ $list->id }})" class="text-[#E53E3E] hover:text-white" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                        </div>
                    @endif
                </div>
                <div class="text-[#A1A1AA] text-sm mb-2">{{ $list->items_count }} games</div>
                <a href="{{ route('lists.public', $list->slug) }}" target="_blank" class="text-[#2563EB] hover:underline text-xs">View Public Page</a>
            </div>
        @endforeach
    </div>
</div> 