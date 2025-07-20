<x-layouts.app>
    <x-slot name="title">Podcast Team Invitations - Dan & Brian Reviews</x-slot>
    
    <div class="min-h-screen bg-[#151515] py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4 font-['Share_Tech_Mono'] leading-tight">
                            Team Invitations
                        </h1>
                        <p class="text-[#A1A1AA] text-lg font-['Inter']">
                            Review and respond to your pending podcast team invitations
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-[#E53E3E] font-['Share_Tech_Mono']">
                            {{ $invitations->count() }}
                        </div>
                        <div class="text-sm text-[#A1A1AA] font-['Inter']">
                            {{ $invitations->count() === 1 ? 'Invitation' : 'Invitations' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-8 bg-gradient-to-r from-green-600/20 to-emerald-600/20 border border-green-500/30 rounded-xl p-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-green-200 font-['Inter']">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-8 bg-gradient-to-r from-red-600/20 to-red-700/20 border border-red-500/30 rounded-xl p-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-red-200 font-['Inter']">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Invitations List -->
            @if($invitations->count() > 0)
                <div class="grid gap-8">
                    @foreach($invitations as $invitation)
                        <div class="bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl shadow-2xl border border-zinc-700 overflow-hidden hover:border-[#E53E3E]/50 transition-all duration-500 transform hover:-translate-y-1">
                            <div class="p-8">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-start space-x-6">
                                        <!-- Podcast Avatar -->
                                        <div class="w-20 h-20 bg-gradient-to-br from-[#E53E3E] to-red-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                            @if($invitation->podcast->logo_url)
                                                <img src="{{ $invitation->podcast->logo_url }}" alt="{{ $invitation->podcast->name }}" class="w-full h-full rounded-2xl object-cover">
                                            @else
                                                <span class="text-white font-bold text-2xl font-['Share_Tech_Mono']">
                                                    {{ substr($invitation->podcast->name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Podcast Details -->
                                        <div class="flex-1">
                                            <h3 class="text-2xl font-bold text-white mb-3 font-['Share_Tech_Mono']">
                                                {{ $invitation->podcast->name }}
                                            </h3>
                                            @if($invitation->podcast->description)
                                                <p class="text-[#A1A1AA] mb-4 leading-relaxed font-['Inter']">
                                                    {{ Str::limit($invitation->podcast->description, 150) }}
                                                </p>
                                            @endif
                                            
                                            <!-- Invitation Info -->
                                            <div class="grid md:grid-cols-3 gap-4 mb-6">
                                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                                    <div class="text-xs text-[#A1A1AA] mb-1 font-['Inter']">ROLE</div>
                                                    <div class="text-white font-semibold font-['Inter'] capitalize">{{ $invitation->role }}</div>
                                                </div>
                                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                                    <div class="text-xs text-[#A1A1AA] mb-1 font-['Inter']">INVITED BY</div>
                                                    <div class="text-white font-semibold font-['Inter']">{{ $invitation->podcast->owner->name }}</div>
                                                </div>
                                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                                    <div class="text-xs text-[#A1A1AA] mb-1 font-['Inter']">INVITED ON</div>
                                                    <div class="text-white font-semibold font-['Inter']">{{ $invitation->invited_at->format('M j, Y') }}</div>
                                                </div>
                                            </div>

                                            <!-- Permissions Preview -->
                                            <div class="mb-6">
                                                <h4 class="text-sm font-bold text-white mb-3 font-['Inter'] uppercase tracking-wider">Your Permissions</h4>
                                                <div class="flex flex-wrap gap-2">
                                                    @if($invitation->can_post_reviews)
                                                        <span class="px-3 py-1.5 bg-green-600/20 border border-green-500/30 text-green-300 text-sm rounded-lg font-['Inter']">
                                                            📝 Post Reviews
                                                        </span>
                                                    @endif
                                                    @if($invitation->can_add_games)
                                                        <span class="px-3 py-1.5 bg-blue-600/20 border border-blue-500/30 text-blue-300 text-sm rounded-lg font-['Inter']">
                                                            🎮 Add Games
                                                        </span>
                                                    @endif
                                                    @if($invitation->can_delete_games)
                                                        <span class="px-3 py-1.5 bg-red-600/20 border border-red-500/30 text-red-300 text-sm rounded-lg font-['Inter']">
                                                            🗑️ Delete Games
                                                        </span>
                                                    @endif
                                                    @if($invitation->can_manage_episodes)
                                                        <span class="px-3 py-1.5 bg-purple-600/20 border border-purple-500/30 text-purple-300 text-sm rounded-lg font-['Inter']">
                                                            📻 Manage Episodes
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-zinc-700">
                                    <form method="POST" action="{{ route('podcasts.team.decline', [$invitation->podcast, $invitation]) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-6 py-3 text-sm font-bold text-zinc-300 bg-zinc-700 rounded-xl hover:bg-zinc-600 focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all duration-200 font-['Inter']"
                                            onclick="return confirm('Are you sure you want to decline this invitation?')"
                                        >
                                            Decline
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('podcasts.team.accept', [$invitation->podcast, $invitation]) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-6 py-3 text-sm font-bold text-white bg-[#E53E3E] rounded-xl hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all duration-200 transform hover:scale-105 font-['Inter'] shadow-xl"
                                        >
                                            Accept Invitation
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Invitations -->
                <div class="text-center py-20">
                    <div class="w-32 h-32 bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-full flex items-center justify-center mx-auto mb-8 border-2 border-zinc-700">
                        <svg class="w-16 h-16 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2m-6 4L9 20l-2-2m6-8h2a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V8a2 2 0 012-2h2m0 0V4a2 2 0 112 4m-6 8a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 font-['Share_Tech_Mono']">
                        No Pending Invitations
                    </h3>
                    <p class="text-[#A1A1AA] mb-8 max-w-md mx-auto leading-relaxed font-['Inter']">
                        You don't have any pending podcast team invitations at the moment. When podcast owners invite you to join their team, they'll appear here.
                    </p>
                    <div class="space-y-4">
                        <a href="{{ route('podcasts.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-[#E53E3E] text-white font-bold rounded-xl hover:bg-red-700 transition-all duration-200 transform hover:scale-105 font-['Inter'] shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Browse Podcasts
                        </a>
                        <div>
                            <a href="{{ route('podcasts.dashboard') }}" 
                               class="text-[#A1A1AA] hover:text-white transition-colors font-['Inter']">
                                Or go to your dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Navigation Footer -->
            <div class="mt-12 pt-8 border-t border-zinc-700 flex items-center justify-between">
                <a href="{{ route('podcasts.dashboard') }}" 
                   class="inline-flex items-center text-[#A1A1AA] hover:text-white transition-colors font-['Inter']">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
                
                @if($invitations->count() > 0)
                    <div class="text-sm text-[#A1A1AA] font-['Inter']">
                        Showing {{ $invitations->count() }} {{ $invitations->count() === 1 ? 'invitation' : 'invitations' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app> 