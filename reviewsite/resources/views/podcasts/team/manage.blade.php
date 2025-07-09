@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0A0A0A] text-white">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Team Management</h1>
                        <p class="mt-2 text-[#A1A1AA]">Manage team members for {{ $podcast->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('podcasts.show', $podcast) }}" class="text-[#E53E3E] hover:text-red-400 transition-colors">
                            View Podcast
                        </a>
                        <a href="{{ route('podcasts.dashboard') }}" class="text-[#A1A1AA] hover:text-white transition-colors">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-900 bg-opacity-20 border border-green-500 text-green-400 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-900 bg-opacity-20 border border-red-500 text-red-400 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Invite New Member -->
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white">Invite New Team Member</h2>
                    
                    <form method="POST" action="{{ route('podcasts.team.invite', $podcast) }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="user_identifier" class="block text-sm font-medium text-[#A1A1AA] mb-2">
                                Username or Email
                            </label>
                            <input 
                                type="text" 
                                id="user_identifier" 
                                name="user_identifier"
                                class="w-full px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none"
                                placeholder="Enter username or email"
                                required
                            >
                            @error('user_identifier')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-[#A1A1AA] mb-2">
                                Role
                            </label>
                            <select 
                                id="role" 
                                name="role"
                                class="w-full px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none"
                                required
                            >
                                <option value="member">Member</option>
                                <option value="moderator">Moderator</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-[#A1A1AA]">Permissions</label>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="can_post_reviews" 
                                        value="1" 
                                        checked
                                        class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-2 text-sm text-[#A1A1AA]">Can post reviews</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="can_add_games" 
                                        value="1" 
                                        checked
                                        class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-2 text-sm text-[#A1A1AA]">Can add games to reviews</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="can_delete_games" 
                                        value="1"
                                        class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-2 text-sm text-[#A1A1AA]">Can delete games from reviews</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="can_manage_episodes" 
                                        value="1"
                                        class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-2 text-sm text-[#A1A1AA]">Can manage episodes</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button 
                                type="submit" 
                                class="w-full bg-[#E53E3E] text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-[#E53E3E] focus:ring-offset-2 focus:ring-offset-[#1A1A1B] transition-colors"
                            >
                                Send Invitation
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Manage Podcast Links -->
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white">Manage Platform Links</h2>
                    <form method="POST" action="{{ route('podcasts.update-links', $podcast) }}" id="links-form">
                        @csrf
                        <div id="links-container" class="space-y-4">
                            @if(is_array($podcast->links))
                                @foreach($podcast->links as $index => $link)
                                <div class="flex items-center space-x-2 link-group">
                                    <input type="text" name="links[{{ $index }}][platform]" value="{{ $link['platform'] ?? '' }}" placeholder="Platform (e.g., Spotify)" class="w-1/3 px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none">
                                    <input type="url" name="links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="URL" class="flex-1 px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none">
                                    <button type="button" class="text-red-500 hover:text-red-400 remove-link-btn">&times;</button>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" id="add-link-btn" class="mt-4 text-sm text-[#E53E3E] hover:underline">+ Add Link</button>
                        <div class="pt-6">
                            <button type="submit" class="w-full bg-[#E53E3E] text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-[#E53E3E] focus:ring-offset-2 focus:ring-offset-[#1A1A1B] transition-colors">
                                Save Links
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Team Overview -->
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white">Team Overview</h2>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center p-4 bg-[#27272A] border border-[#3F3F46] rounded-lg">
                            <div class="text-2xl font-bold text-[#E53E3E]">{{ $podcast->activeTeamMembers->count() }}</div>
                            <div class="text-sm text-[#A1A1AA]">Active Members</div>
                        </div>
                        <div class="text-center p-4 bg-[#27272A] border border-[#3F3F46] rounded-lg">
                            <div class="text-2xl font-bold text-yellow-500">{{ $podcast->pendingTeamMembers->count() }}</div>
                            <div class="text-sm text-[#A1A1AA]">Pending Invitations</div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-white">Owner</h3>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-[#E53E3E] bg-opacity-20 border border-[#E53E3E] border-opacity-30 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-[#E53E3E] rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">{{ substr($podcast->owner->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ $podcast->owner->name }}</p>
                                    <p class="text-xs text-[#A1A1AA]">{{ $podcast->owner->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="px-2 py-1 bg-[#E53E3E] text-white text-xs rounded">Owner</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Team Members -->
            @if($podcast->activeTeamMembers->count() > 0)
            <div class="mt-8 bg-[#1A1A1B] border border-[#3F3F46] rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-white">Active Team Members</h2>
                <div class="space-y-4">
                    @foreach($podcast->activeTeamMembers as $member)
                    <div class="flex items-center justify-between p-4 bg-[#27272A] border border-[#3F3F46] rounded-lg hover:bg-[#2D2D30] transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-[#6B7280] rounded-full flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr($member->user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">{{ $member->user->name }}</p>
                                <p class="text-xs text-[#A1A1AA]">{{ $member->user->email }}</p>
                                <p class="text-xs text-[#A1A1AA]">Joined {{ $member->accepted_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <span class="px-2 py-1 bg-[#3F3F46] text-[#A1A1AA] text-xs rounded">
                                    {{ ucfirst($member->role) }}
                                </span>
                                <div class="text-xs text-[#A1A1AA] mt-1">
                                    @if($member->can_post_reviews) Reviews @endif
                                    @if($member->can_add_games) Games @endif
                                    @if($member->can_manage_episodes) Episodes @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button 
                                    onclick="showPermissionsModal({{ $member->id }}, '{{ $member->user->name }}', '{{ $member->role }}', {{ $member->can_add_games ? 'true' : 'false' }}, {{ $member->can_delete_games ? 'true' : 'false' }}, {{ $member->can_post_reviews ? 'true' : 'false' }}, {{ $member->can_manage_episodes ? 'true' : 'false' }})"
                                    class="text-[#E53E3E] hover:text-red-400 text-sm transition-colors"
                                >
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('podcasts.team.remove', [$podcast, $member]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="text-red-400 hover:text-red-300 text-sm transition-colors"
                                        onclick="return confirm('Are you sure you want to remove this team member?')"
                                    >
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Pending Invitations -->
            @if($podcast->pendingTeamMembers->count() > 0)
            <div class="mt-8 bg-[#1A1A1B] border border-[#3F3F46] rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-white">Pending Invitations</h2>
                <div class="space-y-4">
                    @foreach($podcast->pendingTeamMembers as $member)
                    <div class="flex items-center justify-between p-4 bg-yellow-900 bg-opacity-20 border border-yellow-500 border-opacity-30 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr($member->user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">{{ $member->user->name }}</p>
                                <p class="text-xs text-[#A1A1AA]">{{ $member->user->email }}</p>
                                <p class="text-xs text-[#A1A1AA]">Invited {{ $member->invited_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <span class="px-2 py-1 bg-yellow-600 text-white text-xs rounded">
                                    Pending
                                </span>
                                <div class="text-xs text-[#A1A1AA] mt-1">
                                    Role: {{ ucfirst($member->role) }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <form method="POST" action="{{ route('podcasts.team.remove', [$podcast, $member]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="text-red-400 hover:text-red-300 text-sm transition-colors"
                                        onclick="return confirm('Are you sure you want to cancel this invitation?')"
                                    >
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div id="permissionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-medium mb-4 text-white">Edit Permissions</h3>
        <form id="permissionsForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[#A1A1AA] mb-2">Role</label>
                    <select name="role" id="modalRole" class="w-full px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none">
                        <option value="member">Member</option>
                        <option value="moderator">Moderator</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#A1A1AA]">Permissions</label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="can_post_reviews" id="modalCanPostReviews" value="1" class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-2 text-sm text-[#A1A1AA]">Can post reviews</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="can_add_games" id="modalCanAddGames" value="1" class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-2 text-sm text-[#A1A1AA]">Can add games to reviews</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="can_delete_games" id="modalCanDeleteGames" value="1" class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-2 text-sm text-[#A1A1AA]">Can delete games from reviews</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="can_manage_episodes" id="modalCanManageEpisodes" value="1" class="rounded border-[#3F3F46] bg-[#27272A] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-2 text-sm text-[#A1A1AA]">Can manage episodes</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="hidePermissionsModal()" class="px-4 py-2 text-sm font-medium text-[#A1A1AA] bg-[#27272A] border border-[#3F3F46] rounded-lg hover:bg-[#2D2D30] transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#E53E3E] rounded-lg hover:bg-red-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const linksContainer = document.getElementById('links-container');
    const addLinkBtn = document.getElementById('add-link-btn');
    let linkIndex = {{ is_array($podcast->links) ? count($podcast->links) : 0 }};

    addLinkBtn.addEventListener('click', function () {
        const newLinkGroup = document.createElement('div');
        newLinkGroup.classList.add('flex', 'items-center', 'space-x-2', 'link-group');
        newLinkGroup.innerHTML = `
            <input type="text" name="links[${linkIndex}][platform]" placeholder="Platform (e.g., Spotify)" class="w-1/3 px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none">
            <input type="url" name="links[${linkIndex}][url]" placeholder="URL" class="flex-1 px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none">
            <button type="button" class="text-red-500 hover:text-red-400 remove-link-btn">&times;</button>
        `;
        linksContainer.appendChild(newLinkGroup);
        linkIndex++;
    });

    linksContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-link-btn')) {
            e.target.closest('.link-group').remove();
        }
    });
});

function showPermissionsModal(memberId, userName, role, canAddGames, canDeleteGames, canPostReviews, canManageEpisodes) {
    document.getElementById('permissionsForm').action = `/podcasts/{{ $podcast->slug }}/team/${memberId}/permissions`;
    document.getElementById('modalRole').value = role;
    document.getElementById('modalCanAddGames').checked = canAddGames;
    document.getElementById('modalCanDeleteGames').checked = canDeleteGames;
    document.getElementById('modalCanPostReviews').checked = canPostReviews;
    document.getElementById('modalCanManageEpisodes').checked = canManageEpisodes;
    
    document.getElementById('permissionsModal').classList.remove('hidden');
    document.getElementById('permissionsModal').classList.add('flex');
}

function hidePermissionsModal() {
    document.getElementById('permissionsModal').classList.add('hidden');
    document.getElementById('permissionsModal').classList.remove('flex');
}
</script>
@endsection 