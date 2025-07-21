@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#1A1A1B] to-[#2D2D30] text-white py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="flex items-center text-[#A1A1AA] text-sm mb-8 font-['Inter']">
            <a href="{{ route('podcasts.index') }}" class="hover:text-[#E53E3E] transition-colors">Podcasts</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('podcasts.show', $podcast) }}" class="hover:text-[#E53E3E] transition-colors">{{ $podcast->name }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" class="hover:text-[#E53E3E] transition-colors">{{ $episode->title }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>{{ $review->title }}</span>
        </div>

        <!-- Review Header -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-4 font-['Share_Tech_Mono']">
                        {{ $review->title }}
                    </h1>
                    
                    <!-- Review Identity -->
                    <div class="flex items-center gap-4 mb-4">
                        <x-review-identity :review="$review" />
                    </div>
                    
                    <!-- Rating -->
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 10; $i++)
                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-[#E53E3E]' : 'text-[#3F3F46]' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $review->rating }}/10</span>
                    </div>
                    
                    <!-- Meta Info -->
                    <div class="flex items-center text-[#A1A1AA] text-sm gap-4 font-['Inter']">
                        <span>{{ $review->created_at->format('M j, Y') }}</span>
                        @if($review->created_at != $review->updated_at)
                            <span>• Updated {{ $review->updated_at->format('M j, Y') }}</span>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                @auth
                    @if(auth()->id() === $review->user_id || auth()->user()->is_admin)
                        <div class="flex gap-2">
                            <a href="{{ route('podcasts.episodes.reviews.edit', [$podcast, $episode, $review]) }}" 
                               class="bg-[#3F3F46] text-white px-4 py-2 rounded-lg hover:bg-[#4B5563] transition-colors font-['Inter'] text-sm">
                                Edit
                            </a>
                            <form action="{{ route('podcasts.episodes.reviews.destroy', [$podcast, $episode, $review]) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-['Inter'] text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Episode Context -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Episode Reviewed</h2>
            
            <div class="flex items-start gap-6">
                @if($episode->artwork_url)
                    <img src="{{ $episode->artwork_url }}" 
                         alt="{{ $episode->title }}"
                         class="w-24 h-24 rounded-lg object-cover">
                @else
                    <div class="w-24 h-24 bg-[#3F3F46] rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-[#A1A1AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2 font-['Share_Tech_Mono']">
                        <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" 
                           class="hover:text-[#E53E3E] transition-colors">
                            {{ $episode->title }}
                        </a>
                    </h3>
                    <p class="text-[#A1A1AA] text-sm font-['Inter'] mb-2">
                        <a href="{{ route('podcasts.show', $podcast) }}" 
                           class="hover:text-[#E53E3E] transition-colors">
                            {{ $podcast->name }}
                        </a>
                    </p>
                    
                    <div class="flex items-center text-[#A1A1AA] text-sm space-x-4 font-['Inter']">
                        @if($episode->display_number)
                            <span>{{ $episode->display_number }}</span>
                        @endif
                        <span>{{ $episode->published_at->format('M j, Y') }}</span>
                        @if($episode->duration)
                            <span>{{ $episode->formatted_duration }}</span>
                        @endif
                    </div>
                    
                    @if($episode->description)
                        <p class="text-[#A1A1AA] text-sm font-['Inter'] mt-3 leading-relaxed">
                            {{ Str::limit($episode->description, 200) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review Content -->
        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-['Share_Tech_Mono']">Review</h2>
            
            <div class="prose prose-invert max-w-none">
                <div class="text-[#A1A1AA] font-['Inter'] leading-relaxed text-lg whitespace-pre-wrap">
                    {{ $review->content }}
                </div>
            </div>
        </div>

        <!-- Positive/Negative Points -->
        @if(($review->positive_points_list && count($review->positive_points_list) > 0) || ($review->negative_points_list && count($review->negative_points_list) > 0))
            <div class="grid md:grid-cols-2 gap-8 mb-8">
                <!-- Positive Points -->
                @if($review->positive_points_list && count($review->positive_points_list) > 0)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                        <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono'] flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            What We Liked
                        </h3>
                        <ul class="space-y-2">
                            @foreach($review->positive_points_list as $point)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-[#A1A1AA] font-['Inter']">{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Negative Points -->
                @if($review->negative_points_list && count($review->negative_points_list) > 0)
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-2xl shadow-2xl border border-[#3F3F46] p-8">
                        <h3 class="text-xl font-bold text-white mb-4 font-['Share_Tech_Mono'] flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            What Could Be Better
                        </h3>
                        <ul class="space-y-2">
                            @foreach($review->negative_points_list as $point)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-red-500 mr-2 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-[#A1A1AA] font-['Inter']">{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        <!-- Back to Episode -->
        <div class="text-center">
            <a href="{{ route('podcasts.episodes.show', [$podcast, $episode]) }}" 
               class="bg-[#3F3F46] text-white px-6 py-3 rounded-lg hover:bg-[#4B5563] transition-colors font-['Inter'] font-medium inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Episode
            </a>
        </div>
    </div>
</div>
@endsection 
