@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white font-['Share_Tech_Mono'] mb-2">
                    Podcast Dashboard
                </h1>
                <p class="text-[#A1A1AA] font-['Inter']">
                    Manage your podcasts and track their status
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('podcasts.invitations') }}" 
                   class="bg-gradient-to-r from-[#6366F1] to-[#4F46E5] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#5B21B6] hover:to-[#7C3AED] transition-all duration-200 relative">
                    @if($pendingInvitations->count() > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
                            {{ $pendingInvitations->count() }}
                        </span>
                    @endif
                    Team Invitations
                </a>
                <a href="{{ route('podcasts.create') }}" 
                   class="bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200">
                    Submit New Podcast
                </a>
            </div>
        </div>

        <!-- Owned Podcasts -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Your Podcasts</h2>
            
            @if($ownedPodcasts->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($ownedPodcasts as $podcast)
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    @if($podcast->logo_url)
                                        <img src="{{ $podcast->logo_url }}" 
                                             alt="{{ $podcast->name }}"
                                             class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 bg-[#3F3F46] rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div>
                                        <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">
                                            {{ $podcast->name }}
                                        </h3>
                                        <p class="text-[#A1A1AA] text-sm font-['Inter']">
                                            {{ $podcast->episodes->count() }} episodes
                                        </p>
                                    </div>
                                </div>

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($podcast->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($podcast->status === 'verified') bg-blue-100 text-blue-800
                                    @elseif($podcast->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($podcast->status) }}
                                </span>
                            </div>

                            @if($podcast->description)
                                <p class="text-[#A1A1AA] text-sm mb-4 font-['Inter']">
                                    {{ Str::limit($podcast->description, 120) }}
                                </p>
                            @endif

                            @if($podcast->status === 'approved')
                                <div class="mb-4 p-3 bg-[#1A1A1B] rounded-lg border border-[#3F3F46]">
                                    <div class="flex justify-between items-center">
                                        <div class="text-[#A1A1AA] text-sm font-['Inter']">
                                            <span class="font-medium">Team:</span>
                                            {{ $podcast->activeTeamMembers->count() }} members
                                        </div>
                                        @if($podcast->pendingTeamMembers->count() > 0)
                                            <div class="text-yellow-400 text-sm font-['Inter']">
                                                {{ $podcast->pendingTeamMembers->count() }} pending
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <div class="text-[#A1A1AA] text-sm font-['Inter']">
                                    <span>Submitted {{ $podcast->created_at->format('M j, Y') }}</span>
                                </div>

                                <div class="flex space-x-2">
                                    @if($podcast->status === 'pending')
                                        <a href="{{ route('podcasts.verify', $podcast) }}" 
                                           class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-[#DC2626] transition-colors font-['Inter'] text-sm">
                                            Verify
                                        </a>
                                    @elseif($podcast->status === 'approved')
                                        <a href="{{ route('podcasts.show', $podcast) }}" 
                                           class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-[#DC2626] transition-colors font-['Inter'] text-sm">
                                            View
                                        </a>
                                        <a href="{{ route('podcasts.team.manage', $podcast) }}" 
                                           class="bg-[#6366F1] text-white px-4 py-2 rounded-lg hover:bg-[#5B21B6] transition-colors font-['Inter'] text-sm">
                                            Manage Team
                                        </a>
                                        <form action="{{ route('podcasts.sync-rss', $podcast) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-[#27272A] text-white px-4 py-2 rounded-lg border border-[#3F3F46] hover:bg-[#374151] transition-colors font-['Inter'] text-sm">
                                                Sync RSS
                                            </button>
                                        </form>
                                    @elseif($podcast->status === 'verified')
                                        <span class="text-[#A1A1AA] text-sm font-['Inter']">
                                            Pending admin approval
                                        </span>
                                    @else
                                        <span class="text-red-400 text-sm font-['Inter']">
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($podcast->admin_notes)
                                <div class="mt-4 p-3 bg-[#1A1A1B] rounded-lg border border-[#3F3F46]">
                                    <p class="text-[#A1A1AA] text-sm mb-1 font-['Inter']">Admin Notes:</p>
                                    <p class="text-white text-sm font-['Inter']">{{ $podcast->admin_notes }}</p>
                                </div>
                            @endif

                            @if($podcast->rss_error)
                                <div class="mt-4 p-3 bg-red-900/20 rounded-lg border border-red-700">
                                    <p class="text-red-400 text-sm mb-1 font-['Inter']">RSS Error:</p>
                                    <p class="text-red-300 text-sm font-['Inter']">{{ $podcast->rss_error }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-[#3F3F46] rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">No Podcasts Yet</h3>
                    <p class="text-[#A1A1AA] mb-6 font-['Inter']">
                        Get started by submitting your first podcast for review.
                    </p>
                    <a href="{{ route('podcasts.create') }}" 
                       class="bg-gradient-to-r from-[#E53E3E] to-[#B91C1C] text-white font-bold py-3 px-6 rounded-lg font-['Inter'] hover:from-[#DC2626] hover:to-[#991B1B] transition-all duration-200 inline-block">
                        Submit Your First Podcast
                    </a>
                </div>
            @endif
        </div>

        <!-- Team Memberships -->
        @if($teamMemberships->count() > 0)
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Team Memberships</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($teamMemberships as $membership)
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    @if($membership->podcast->logo_url)
                                        <img src="{{ $membership->podcast->logo_url }}" 
                                             alt="{{ $membership->podcast->name }}"
                                             class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 bg-[#3F3F46] rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div>
                                        <h3 class="text-xl font-bold text-white font-['Share_Tech_Mono']">
                                            {{ $membership->podcast->name }}
                                        </h3>
                                        <p class="text-[#A1A1AA] text-sm font-['Inter']">
                                            {{ $membership->podcast->episodes->count() }} episodes
                                        </p>
                                    </div>
                                </div>

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($membership->role) }}
                                </span>
                            </div>

                            @if($membership->podcast->description)
                                <p class="text-[#A1A1AA] text-sm mb-4 font-['Inter']">
                                    {{ Str::limit($membership->podcast->description, 120) }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between">
                                <div class="text-[#A1A1AA] text-sm font-['Inter']">
                                    <span>Joined {{ $membership->accepted_at->format('M j, Y') }}</span>
                                </div>

                                <div class="flex space-x-2">
                                    @if($membership->podcast->status === 'approved')
                                        <a href="{{ route('podcasts.show', $membership->podcast) }}" 
                                           class="bg-[#E53E3E] text-white px-4 py-2 rounded-lg hover:bg-[#DC2626] transition-colors font-['Inter'] text-sm">
                                            View
                                        </a>
                                    @endif
                                    <form action="{{ route('podcasts.team.leave', $membership->podcast) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-['Inter'] text-sm"
                                                onclick="return confirm('Are you sure you want to leave this team?')">
                                            Leave Team
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Quick Actions</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('podcasts.create') }}" 
                   class="bg-[#E53E3E] text-white p-6 rounded-lg hover:bg-[#DC2626] transition-colors text-center">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <h3 class="font-bold font-['Share_Tech_Mono']">Submit New Podcast</h3>
                    <p class="text-sm opacity-90 font-['Inter']">Add another podcast to your collection</p>
                </a>

                <a href="{{ route('podcasts.index') }}" 
                   class="bg-[#3F3F46] text-white p-6 rounded-lg hover:bg-[#374151] transition-colors text-center">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                    <h3 class="font-bold font-['Share_Tech_Mono']">Browse Podcasts</h3>
                    <p class="text-sm opacity-90 font-['Inter']">Discover other gaming podcasts</p>
                </a>

                <a href="{{ route('dashboard') }}" 
                   class="bg-[#3F3F46] text-white p-6 rounded-lg hover:bg-[#374151] transition-colors text-center">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="font-bold font-['Share_Tech_Mono']">Main Dashboard</h3>
                    <p class="text-sm opacity-90 font-['Inter']">View your reviews and activity</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 