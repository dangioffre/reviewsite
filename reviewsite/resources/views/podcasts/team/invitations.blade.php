@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Podcast Team Invitations</h1>
            <p class="mt-2 text-gray-600">Review and respond to your pending podcast team invitations</p>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Invitations List -->
        @if($invitations->count() > 0)
            <div class="space-y-6">
                @foreach($invitations as $invitation)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start">
                                <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                                    <span class="text-white font-medium text-lg">{{ substr($invitation->podcast->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ $invitation->podcast->name }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $invitation->podcast->description }}</p>
                                    
                                    <div class="mt-4 space-y-2">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="font-medium">Role:</span>
                                            <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-800 rounded">{{ ucfirst($invitation->role) }}</span>
                                        </div>
                                        
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="font-medium">Invited by:</span>
                                            <span class="ml-2">{{ $invitation->podcast->owner->name }}</span>
                                        </div>
                                        
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="font-medium">Invited on:</span>
                                            <span class="ml-2">{{ $invitation->invited_at->format('M j, Y \a\t g:i A') }}</span>
                                        </div>
                                    </div>

                                    <!-- Permissions Preview -->
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">Your permissions will include:</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @if($invitation->can_post_reviews)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Post Reviews</span>
                                            @endif
                                            @if($invitation->can_add_games)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Add Games</span>
                                            @endif
                                            @if($invitation->can_delete_games)
                                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Delete Games</span>
                                            @endif
                                            @if($invitation->can_manage_episodes)
                                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">Manage Episodes</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <form method="POST" action="{{ route('podcasts.team.decline', [$invitation->podcast, $invitation]) }}" class="inline">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                    onclick="return confirm('Are you sure you want to decline this invitation?')"
                                >
                                    Decline
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('podcasts.team.accept', [$invitation->podcast, $invitation]) }}" class="inline">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Accept Invitation
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- No Invitations -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No pending invitations</h3>
                <p class="text-gray-600">You don't have any pending podcast team invitations at the moment.</p>
                <div class="mt-6">
                    <a href="{{ route('podcasts.index') }}" class="text-blue-600 hover:text-blue-800">
                        Browse podcasts
                    </a>
                </div>
            </div>
        @endif

        <!-- Navigation -->
        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('podcasts.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Dashboard
            </a>
            <div class="text-sm text-gray-500">
                {{ $invitations->count() }} {{ $invitations->count() === 1 ? 'invitation' : 'invitations' }}
            </div>
        </div>
    </div>
</div>
@endsection 