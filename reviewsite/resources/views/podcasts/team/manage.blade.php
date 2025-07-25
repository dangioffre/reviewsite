@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('podcasts.dashboard') }}" 
                       class="text-[#A1A1AA] hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono']">
                        Podcast Management
                    </h1>
                </div>
                <p class="text-[#A1A1AA] font-['Inter'] ml-8">
                    Manage team members and settings for {{ $podcast->name }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('podcasts.episodes.index', $podcast) }}" 
                   class="px-4 py-2 bg-[#10B981] text-white rounded-lg font-['Inter'] hover:bg-[#059669] transition-all duration-200 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    Manage Episode Reviews
                </a>
                <a href="{{ route('podcasts.show', $podcast) }}" 
                   class="px-4 py-2 bg-[#E53E3E] text-white rounded-lg font-['Inter'] hover:bg-[#DC2626] transition-all duration-200 text-sm">
                    View Podcast
                </a>
                <a href="{{ route('podcasts.dashboard') }}" 
                   class="px-4 py-2 bg-[#6366F1] text-white rounded-lg font-['Inter'] hover:bg-[#5B21B6] transition-all duration-200 text-sm">
                    Back to Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Team Management -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Team Overview -->
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white font-['Share_Tech_Mono']">Team Overview</h2>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-[#27272A] rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-white mb-1">{{ $podcast->activeTeamMembers()->count() }}</div>
                            <div class="text-sm text-[#A1A1AA]">Active Members</div>
                        </div>
                        <div class="bg-[#27272A] rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $podcast->teamMembers()->whereNull('accepted_at')->count() }}</div>
                            <div class="text-sm text-[#A1A1AA]">Pending Invitations</div>
                        </div>
                    </div>

                    <!-- Owner -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-[#A1A1AA] mb-3">Owner</h3>
                        <div class="bg-[#27272A] rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-[#E53E3E] to-[#DC2626] rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ substr($podcast->owner->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-white font-medium">{{ $podcast->owner->name }}</div>
                                    <div class="text-[#A1A1AA] text-sm">{{ $podcast->owner->email }}</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#E53E3E]/20 text-[#E53E3E]">
                                Owner
                            </span>
                        </div>
                    </div>

                    <!-- Team Members -->
                    @if($podcast->activeTeamMembers()->count() > 0)
                        <div>
                            <h3 class="text-sm font-medium text-[#A1A1AA] mb-3">Team Members</h3>
                            <div class="space-y-3">
                                @foreach($podcast->activeTeamMembers as $member)
                                    <div class="bg-[#27272A] rounded-lg p-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">{{ substr($member->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-white font-medium">{{ $member->user->name }}</div>
                                                <div class="text-[#A1A1AA] text-sm">{{ $member->user->email }}</div>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    @if($member->can_post_reviews)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400">Reviews</span>
                                                    @endif
                                                    @if($member->can_add_games)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">Games</span>
                                                    @endif
                                                    @if($member->can_manage_episodes)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400">Episodes</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
                                                {{ ucfirst($member->role) }}
                                            </span>
                                            <button 
                                                onclick="showPermissionsModal({{ $member->id }}, '{{ $member->user->name }}', '{{ $member->role }}', {{ $member->can_add_games ? 'true' : 'false' }}, {{ $member->can_delete_games ? 'true' : 'false' }}, {{ $member->can_post_reviews ? 'true' : 'false' }}, {{ $member->can_manage_episodes ? 'true' : 'false' }})"
                                                class="text-[#E53E3E] hover:text-red-400 text-sm transition-colors">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('podcasts.team.remove', [$podcast, $member]) }}" class="inline" onsubmit="return confirm('Are you sure you want to remove this team member?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition-colors">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Invite New Team Member -->
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white font-['Share_Tech_Mono']">Invite New Team Member</h2>
                    
                    <form method="POST" action="{{ route('podcasts.team.invite', $podcast) }}" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="user_identifier" class="block text-sm font-medium text-[#A1A1AA] mb-2">
                                    Username or Email *
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
                                    Role *
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
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#A1A1AA] mb-3">Permissions</label>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="can_post_reviews" 
                                        value="1" 
                                        checked
                                        class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-3 text-sm text-white">Can post reviews</span>
                                </label>
                                
                                <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="can_add_games" 
                                        value="1" 
                                        checked
                                        class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-3 text-sm text-white">Can add games to reviews</span>
                                </label>
                                
                                <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="can_delete_games" 
                                        value="1"
                                        class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-3 text-sm text-white">Can delete games from reviews</span>
                                </label>
                                
                                <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="can_manage_episodes" 
                                        value="1"
                                        class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]"
                                    >
                                    <span class="ml-3 text-sm text-white">Can manage episodes</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full px-4 py-3 bg-[#E53E3E] text-white font-bold rounded-lg hover:bg-[#DC2626] transition-colors">
                            Send Invitation
                        </button>
                    </form>
                </div>

                <!-- Platform Links -->
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white font-['Share_Tech_Mono']">Platform Links</h2>
                    
                    <form method="POST" action="{{ route('podcasts.update-links', $podcast) }}" class="space-y-4">
                        @csrf
                        
                        @for($i = 0; $i < 3; $i++)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <input 
                                        type="text" 
                                        name="links[{{ $i }}][platform]"
                                        value="{{ isset($podcast->links[$i]) ? $podcast->links[$i]['platform'] : '' }}"
                                        class="w-full px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none"
                                        placeholder="Platform (e.g., Spotify)"
                                    >
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input 
                                        type="url" 
                                        name="links[{{ $i }}][url]"
                                        value="{{ isset($podcast->links[$i]) ? $podcast->links[$i]['url'] : '' }}"
                                        class="flex-1 px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white placeholder-[#A1A1AA] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none"
                                        placeholder="https://..."
                                    >
                                    @if($i > 0)
                                        <button type="button" class="text-red-400 hover:text-red-300 p-2" onclick="clearPlatformLink({{ $i }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endfor

                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors">
                            Update Platform Links
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Quick Stats & Danger Zone -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white font-['Share_Tech_Mono']">Quick Stats</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[#A1A1AA] font-['Inter']">Episodes</span>
                            <span class="text-white font-semibold font-['Inter']">{{ $podcast->episodes()->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[#A1A1AA] font-['Inter']">Reviews</span>
                            <span class="text-white font-semibold font-['Inter']">{{ $podcast->episodes()->withCount('attachedReviews')->get()->sum('attached_reviews_count') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[#A1A1AA] font-['Inter']">Team Size</span>
                            <span class="text-white font-semibold font-['Inter']">{{ $podcast->activeTeamMembers()->count() + 1 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[#A1A1AA] font-['Inter']">Status</span>
                            <span class="text-green-400 font-semibold font-['Inter']">{{ ucfirst($podcast->status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-[#1A1A1B] border border-red-500/30 rounded-xl p-6">
                    <h2 class="text-xl font-semibold mb-4 text-red-400 font-['Share_Tech_Mono']">Danger Zone</h2>
                    <p class="text-red-300 mb-4 text-sm">
                        Deleting your podcast is a permanent action. All episodes, episode reviews, and team members will be removed. This cannot be undone.
                    </p>
                    <form method="POST" action="{{ route('podcasts.destroy', $podcast) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you absolutely sure you want to delete this podcast? This action cannot be undone and will remove all episodes, reviews, and team members.')"
                                class="w-full px-4 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors">
                            Delete Podcast
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div id="permissionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#1A1A1B] border border-[#3F3F46] rounded-lg max-w-md w-full">
            <div class="flex items-center justify-between p-4 border-b border-[#3F3F46]">
                <h3 class="text-lg font-semibold text-white">Edit Permissions</h3>
                <button onclick="closePermissionsModal()" class="text-[#A1A1AA] hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="permissionsForm" method="POST" class="p-4">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="modalRole" class="block text-sm font-medium text-[#A1A1AA] mb-2">Role</label>
                    <select id="modalRole" name="role" class="w-full px-3 py-2 bg-[#27272A] border border-[#3F3F46] rounded-lg text-white focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E] focus:outline-none">
                        <option value="member">Member</option>
                        <option value="moderator">Moderator</option>
                    </select>
                </div>
                
                <div class="space-y-3 mb-6">
                    <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                        <input type="checkbox" name="can_add_games" id="modalCanAddGames" value="1" class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-3 text-sm text-white">Can add games to reviews</span>
                    </label>
                    
                    <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                        <input type="checkbox" name="can_delete_games" id="modalCanDeleteGames" value="1" class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-3 text-sm text-white">Can delete games from reviews</span>
                    </label>
                    
                    <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                        <input type="checkbox" name="can_post_reviews" id="modalCanPostReviews" value="1" class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-3 text-sm text-white">Can post reviews</span>
                    </label>
                    
                    <label class="flex items-center p-3 bg-[#27272A] rounded-lg hover:bg-[#3F3F46] transition-colors cursor-pointer">
                        <input type="checkbox" name="can_manage_episodes" id="modalCanManageEpisodes" value="1" class="rounded border-[#3F3F46] bg-[#1A1A1B] text-[#E53E3E] focus:border-[#E53E3E] focus:ring-1 focus:ring-[#E53E3E]">
                        <span class="ml-3 text-sm text-white">Can manage episodes</span>
                    </label>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closePermissionsModal()" class="px-4 py-2 bg-[#3F3F46] text-white rounded-lg hover:bg-[#52525B] transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#E53E3E] text-white rounded-lg hover:bg-[#DC2626] transition-colors">
                        Update Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showPermissionsModal(memberId, userName, role, canAddGames, canDeleteGames, canPostReviews, canManageEpisodes) {
    document.getElementById('permissionsForm').action = `/podcasts/{{ $podcast->slug }}/team/${memberId}/permissions`;
    document.getElementById('modalRole').value = role;
    document.getElementById('modalCanAddGames').checked = canAddGames;
    document.getElementById('modalCanDeleteGames').checked = canDeleteGames;
    document.getElementById('modalCanPostReviews').checked = canPostReviews;
    document.getElementById('modalCanManageEpisodes').checked = canManageEpisodes;
    
    document.getElementById('permissionsModal').classList.remove('hidden');
}

function closePermissionsModal() {
    document.getElementById('permissionsModal').classList.add('hidden');
}

function clearPlatformLink(index) {
    const platformInput = document.querySelector(`input[name="links[${index}][platform]"]`);
    const urlInput = document.querySelector(`input[name="links[${index}][url]"]`);
    
    platformInput.value = '';
    urlInput.value = '';
}

// Close modal on backdrop click
document.getElementById('permissionsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePermissionsModal();
    }
});
</script>
@endsection