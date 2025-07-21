@extends('layouts.app')

@section('title', 'Following')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Following</h1>
            <p class="text-gray-600">{{ $followedStreamers->count() }} streamers</p>
        </div>

        @if($followedStreamers->isEmpty())
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No followed streamers</h3>
                    <p class="mt-1 text-sm text-gray-500">Start following streamers to see their updates here.</p>
                    <div class="mt-6">
                        <a href="{{ route('streamer.profiles.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Discover Streamers
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($followedStreamers as $streamer)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                @if($streamer->profile_photo_url)
                                    <img src="{{ $streamer->profile_photo_url }}" alt="{{ $streamer->getDisplayName() }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium text-lg">{{ substr($streamer->getDisplayName(), 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $streamer->getDisplayName() }}</h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $streamer->platform === 'twitch' ? 'purple' : ($streamer->platform === 'youtube' ? 'red' : 'green') }}-100 text-{{ $streamer->platform === 'twitch' ? 'purple' : ($streamer->platform === 'youtube' ? 'red' : 'green') }}-800">
                                            {{ ucfirst($streamer->platform) }}
                                        </span>
                                        @if($streamer->is_verified)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Verified
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($streamer->bio)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $streamer->bio }}</p>
                            @endif

                            @if($streamer->vods->isNotEmpty())
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Recent VODs</h4>
                                    <div class="space-y-1">
                                        @foreach($streamer->vods->take(2) as $vod)
                                            <a href="{{ $vod->vod_url }}" target="_blank" class="block text-sm text-indigo-600 hover:text-indigo-800 truncate">
                                                {{ $vod->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex space-x-2">
                                    <a href="{{ route('streamer.profile.show', $streamer) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View Profile
                                    </a>
                                    <a href="{{ $streamer->channel_url }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Watch Live
                                    </a>
                                </div>
                                
                                <form action="{{ route('streamer.unfollow', $streamer) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium" onclick="return confirm('Are you sure you want to unfollow {{ $streamer->getDisplayName() }}?')">
                                        Unfollow
                                    </button>
                                </form>
                            </div>

                            <!-- Notification Preferences -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Notifications</h4>
                                <form action="{{ route('streamer.notification-preferences', $streamer) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    @php
                                        $preferences = json_decode($streamer->pivot->notification_preferences, true) ?: ['live' => true, 'reviews' => true];
                                    @endphp
                                    
                                    <label class="flex items-center">
                                        <input type="checkbox" name="live" value="1" {{ $preferences['live'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Live stream notifications</span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="checkbox" name="reviews" value="1" {{ $preferences['reviews'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">New review notifications</span>
                                    </label>
                                    
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        Update Preferences
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
