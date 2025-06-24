<x-layouts.app>
@if($list)
    <div class="min-h-screen bg-[#151515] py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif
            <!-- Enhanced Header -->
            <div class="text-center mb-12">
                <!-- Main Title Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <h1 class="text-5xl font-bold text-white font-['Share_Tech_Mono'] bg-gradient-to-r from-white to-[#A1A1AA] bg-clip-text text-transparent">
                            {{ $list->name }}
                        </h1>
                        @if($list->category && $list->category !== 'general')
                            <span class="bg-gradient-to-r from-[#7C3AED] to-[#A855F7] text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                                {{ \App\Models\ListModel::$categories[$list->category] ?? ucfirst($list->category) }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Subtitle -->
                    <p class="text-[#A1A1AA] font-['Inter'] text-lg max-w-2xl mx-auto">
                        A curated collection of games shared by the community
                    </p>
                    
                    <!-- Description -->
                    @if($list->description)
                        <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-6 max-w-4xl mx-auto mt-6">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-[#A1A1AA] text-sm font-semibold uppercase tracking-wide font-['Inter']">About This List</span>
                            </div>
                            <p class="text-white leading-relaxed font-['Inter'] text-center">{{ $list->description }}</p>
                        </div>
                    @endif
                </div>
                
                <!-- Creator & Basic Info -->
                <div class="flex items-center justify-center gap-8 text-[#A1A1AA] font-['Inter'] mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-r from-[#7C3AED] to-[#A855F7] rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                            </svg>
                        </div>
                        <span>Created by <span class="text-white font-semibold">{{ $list->user->name ?? 'Unknown' }}</span></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-r from-[#2563EB] to-[#3B82F6] rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-white">{{ $list->items ? $list->items->count() : 0 }}</span>
                        <span>{{ $list->items && $list->items->count() === 1 ? 'game' : 'games' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-r from-[#059669] to-[#10B981] rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span>{{ $list->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
                
                <!-- Enhanced Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mb-8">
                    <!-- Followers Stat -->
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-4 text-center hover:border-[#F59E0B]/50 transition-all duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-[#F59E0B] to-[#D97706] rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $list->followers_count }}</div>
                        <div class="text-xs text-[#A1A1AA] font-['Inter']">{{ $list->followers_count === 1 ? 'Follower' : 'Followers' }}</div>
                    </div>

                    <!-- Comments Stat -->
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-4 text-center hover:border-[#22C55E]/50 transition-all duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-[#22C55E] to-[#16A34A] rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-white font-['Share_Tech_Mono']">{{ $list->comments_count }}</div>
                        <div class="text-xs text-[#A1A1AA] font-['Inter']">{{ $list->comments_count === 1 ? 'Comment' : 'Comments' }}</div>
                    </div>

                    <!-- Collaboration Status -->
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-4 text-center hover:border-[#2563EB]/50 transition-all duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                        <div class="text-xs font-bold text-white font-['Share_Tech_Mono'] uppercase">
                            {{ $list->allow_collaboration ? 'Open' : 'Private' }}
                        </div>
                        <div class="text-xs text-[#A1A1AA] font-['Inter']">Collaboration</div>
                    </div>

                    <!-- List Type -->
                    <div class="bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] p-4 text-center hover:border-[#7C3AED]/50 transition-all duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-[#7C3AED] to-[#6D28D9] rounded-full flex items-center justify-center mx-auto mb-2">
                            @if($list->cloned_from)
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/>
                                    <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="text-xs font-bold text-white font-['Share_Tech_Mono'] uppercase">
                            {{ $list->cloned_from ? 'Cloned' : 'Original' }}
                        </div>
                        <div class="text-xs text-[#A1A1AA] font-['Inter']">List Type</div>
                    </div>
                </div>
                
                <!-- Sort Info -->
                @if($list->sort_by && $list->sort_by !== 'date_added')
                    <div class="bg-gradient-to-r from-[#27272A] to-[#1A1A1B] rounded-xl border border-[#3F3F46] px-6 py-3 inline-flex items-center gap-2 mb-6">
                        <svg class="w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        <span class="text-[#A1A1AA] text-sm font-['Inter']">Sorted by</span>
                        <span class="text-white font-semibold font-['Share_Tech_Mono']">{{ \App\Models\ListModel::$sortOptions[$list->sort_by] ?? 'Date Added' }}</span>
                        <span class="bg-[#7C3AED]/20 text-[#7C3AED] px-2 py-1 rounded text-xs font-bold">{{ ucfirst($list->sort_direction) }}</span>
                    </div>
                @endif
            </div>

            <!-- Enhanced Games Grid -->
            @if($list->items && $list->items->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($list->items as $item)
                        <a href="{{ route('games.show', $item->product->slug) }}" target="_blank" class="block group">
                            <div class="bg-gradient-to-br from-[#27272A]/80 to-[#1A1A1B]/80 backdrop-blur-sm rounded-2xl border border-[#3F3F46] p-6 hover:border-[#7C3AED]/50 group-hover:shadow-2xl group-hover:shadow-[#7C3AED]/20 transition-all duration-300 h-full flex flex-col hover:scale-[1.02] hover:-translate-y-1">
                                <!-- Enhanced Game Image -->
                                @if($item->product->image_url)
                                    <div class="mb-6 rounded-xl overflow-hidden aspect-video relative">
                                        <img src="{{ $item->product->image_url }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>
                                @endif
                                
                                <div class="flex-1 flex flex-col">
                                    <div class="mb-6">
                                        <h3 class="text-white font-bold font-['Share_Tech_Mono'] text-xl mb-3 group-hover:text-[#7C3AED] transition-colors duration-300 leading-tight">{{ $item->product->name }}</h3>
                                        <p class="text-[#A1A1AA] text-sm font-['Inter'] leading-relaxed line-clamp-3">
                                            {{ Str::limit($item->product->description, 100) }}
                                        </p>
                                    </div>

                                    <!-- Enhanced Game Info -->
                                    <div class="space-y-3 mb-6">
                                        @if($item->product->overall_rating)
                                            <div class="flex items-center justify-between bg-[#18181B] rounded-lg px-3 py-2">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <span class="text-[#A1A1AA] text-xs font-['Inter']">Rating</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-white font-bold text-lg font-['Share_Tech_Mono']">{{ number_format($item->product->overall_rating, 1) }}</span>
                                                    <span class="text-[#A1A1AA] text-xs ml-1">/ 10</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if($item->product->release_date)
                                            <div class="flex items-center justify-between bg-[#18181B] rounded-lg px-3 py-2">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-[#059669] mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-[#A1A1AA] text-xs font-['Inter']">Released</span>
                                                </div>
                                                <span class="text-white font-semibold text-sm font-['Share_Tech_Mono']">{{ \Carbon\Carbon::parse($item->product->release_date)->format('M Y') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Enhanced Tags & Action -->
                                    <div class="mt-auto">
                                        @if($item->product->genre)
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <span class="bg-gradient-to-r from-[#7C3AED] to-[#A855F7] text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg">
                                                    {{ $item->product->genre->name }}
                                                </span>
                                                @if($item->product->platform)
                                                    <span class="bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg">
                                                        {{ $item->product->platform->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <!-- Enhanced View Game Button -->
                                        <div class="bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] group-hover:from-[#1D4ED8] group-hover:to-[#1E40AF] text-white text-center py-3 px-4 rounded-xl font-bold text-sm transition-all duration-300 font-['Inter'] shadow-lg group-hover:shadow-xl flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Game
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Enhanced Empty State -->
                <div class="text-center py-24">
                    <div class="relative mb-8">
                        <div class="w-32 h-32 mx-auto bg-gradient-to-br from-[#27272A] to-[#1A1A1B] rounded-full border-2 border-[#3F3F46] flex items-center justify-center">
                            <svg class="w-16 h-16 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a1 1 0 011-1h6a1 1 0 011 1v2M7 7h10" />
                            </svg>
                        </div>
                        <!-- Floating particles -->
                        <div class="absolute top-4 left-1/2 w-2 h-2 bg-[#7C3AED] rounded-full animate-ping"></div>
                        <div class="absolute bottom-8 right-1/3 w-1 h-1 bg-[#2563EB] rounded-full animate-pulse"></div>
                        <div class="absolute top-1/2 left-1/4 w-1.5 h-1.5 bg-[#F59E0B] rounded-full animate-bounce"></div>
                    </div>
                    <h3 class="text-3xl font-bold text-white font-['Share_Tech_Mono'] mb-4 bg-gradient-to-r from-white to-[#A1A1AA] bg-clip-text text-transparent">Empty Collection</h3>
                    <p class="text-[#A1A1AA] font-['Inter'] text-lg mb-6 max-w-md mx-auto">This gaming list is waiting to be filled with amazing games. Check back later for updates!</p>
                    <div class="flex items-center justify-center gap-4 text-sm text-[#71717A]">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            <span>{{ $list->followers_count }} watching</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $list->comments_count }} discussing</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Collaboration Manager -->
            @if(isset($showCollaborationManager) && $showCollaborationManager)
                <div class="mt-12 mb-8">
                    @livewire('collaboration-manager', ['list' => $list])
                </div>
            @endif

            <!-- Social Actions & Comments Section -->
            @auth
                <div class="mt-12 space-y-6">
                    <!-- Enhanced Community Section -->
                    <div class="bg-gradient-to-br from-[#27272A]/80 to-[#1A1A1B]/80 backdrop-blur-sm rounded-2xl border border-[#3F3F46] p-6 hover:border-[#52525B] transition-all duration-300">
                        <!-- Compact Header -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-[#7C3AED] to-[#A855F7] rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono']">Join the Community</h3>
                                <p class="text-[#A1A1AA] font-['Inter'] text-sm">Connect with fellow gamers and stay updated</p>
                            </div>
                        </div>
                        
                        <!-- Compact Stats -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-[#18181B] rounded-lg p-3 text-center border border-[#3F3F46]">
                                <div class="text-xl font-bold text-[#F59E0B] font-['Share_Tech_Mono']">{{ $list->followers_count }}</div>
                                <div class="text-xs text-[#A1A1AA] font-['Inter']">{{ $list->followers_count === 1 ? 'Follower' : 'Followers' }}</div>
                            </div>
                            <div class="bg-[#18181B] rounded-lg p-3 text-center border border-[#3F3F46]">
                                <div class="text-xl font-bold text-[#22C55E] font-['Share_Tech_Mono']">{{ $list->comments_count }}</div>
                                <div class="text-xs text-[#A1A1AA] font-['Inter']">{{ $list->comments_count === 1 ? 'Comment' : 'Comments' }}</div>
                            </div>
                        </div>
                        
                        <!-- Compact Action Buttons -->
                        <div class="space-y-3">
                            @if(!$list->isFollowedBy(auth()->id()))
                                <form action="{{ route('lists.follow', $list->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-[#F59E0B] to-[#D97706] hover:from-[#D97706] hover:to-[#B45309] text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 font-['Inter'] flex items-center justify-center gap-2 shadow-lg hover:shadow-xl group">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Follow This List
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('lists.unfollow', $list->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-[#71717A] to-[#52525B] hover:from-[#52525B] hover:to-[#3F3F46] text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 font-['Inter'] flex items-center justify-center gap-2 shadow-lg hover:shadow-xl group">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Unfollow List
                                    </button>
                                </form>
                            @endif
                            
                            @if($list->allow_collaboration && $list->user_id !== auth()->id())
                                @php
                                    $existingCollaboration = $list->collaborators->where('user_id', auth()->id())->first();
                                @endphp
                                
                                @if($existingCollaboration)
                                    @if($existingCollaboration->isPending())
                                        <div class="w-full bg-gradient-to-r from-[#F59E0B] to-[#D97706] text-white px-6 py-3 rounded-lg font-semibold font-['Inter'] flex items-center justify-center gap-2 shadow-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Collaboration Request Pending
                                        </div>
                                    @else
                                        <div class="w-full bg-gradient-to-r from-[#22C55E] to-[#16A34A] text-white px-6 py-3 rounded-lg font-semibold font-['Inter'] flex items-center justify-center gap-2 shadow-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            You're a Collaborator
                                        </div>
                                        <div class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-3 py-2">
                                            <div class="text-center">
                                                <span class="text-[#A1A1AA] text-xs font-['Inter']">Your permissions:</span>
                                                <div class="text-[#22C55E] text-xs font-semibold">{{ $existingCollaboration->getPermissionSummary() }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <form action="{{ route('lists.collaborate', $list->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] hover:from-[#1D4ED8] hover:to-[#1E40AF] text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 font-['Inter'] flex items-center justify-center gap-2 shadow-lg hover:shadow-xl group">
                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Request Collaboration
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Compact Comments Section -->
                    @if($list->allow_comments)
                        <div class="bg-gradient-to-br from-[#27272A]/80 to-[#1A1A1B]/80 backdrop-blur-sm rounded-2xl border border-[#3F3F46] p-6 hover:border-[#52525B] transition-all duration-300">
                            <!-- Compact Header -->
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-gradient-to-r from-[#22C55E] to-[#16A34A] rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white font-['Share_Tech_Mono']">Discussion</h3>
                                    <p class="text-[#A1A1AA] font-['Inter'] text-sm">Share your thoughts and connect with other gamers</p>
                                </div>
                            </div>
                            
                            <!-- Compact Add Comment Form -->
                            <form action="{{ route('lists.comments.store', $list->id) }}" method="POST" class="mb-6">
                                @csrf
                                <div class="space-y-3">
                                    <div class="relative">
                                        <textarea name="content" 
                                                  placeholder="Share your thoughts about this list..." 
                                                  rows="3"
                                                  class="w-full bg-[#18181B] border border-[#3F3F46] rounded-lg px-4 py-3 text-white placeholder-[#71717A] focus:outline-none focus:ring-2 focus:ring-[#22C55E] focus:border-transparent font-['Inter'] resize-none transition-all duration-200"
                                                  required></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" 
                                                class="bg-gradient-to-r from-[#22C55E] to-[#16A34A] hover:from-[#16A34A] hover:to-[#15803D] text-white px-6 py-2 rounded-lg font-semibold transition-all duration-300 font-['Inter'] flex items-center gap-2 shadow-lg hover:shadow-xl">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                            Post Comment
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Comments List -->
                            @if($list->comments && $list->comments->count() > 0)
                                <div class="space-y-4">
                                    @foreach($list->comments as $comment)
                                        <div class="bg-[#18181B] rounded-lg p-4 border border-[#3F3F46]">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-[#7C3AED] rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                        {{ strtoupper(substr($comment->user->name ?? 'A', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-white font-semibold text-sm">{{ $comment->user->name ?? 'Anonymous' }}</div>
                                                        <div class="text-[#71717A] text-xs">{{ $comment->created_at->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                                
                                                @if($comment->likes_count > 0)
                                                    <div class="flex items-center gap-1 text-[#71717A] text-xs">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                                        </svg>
                                                        {{ $comment->likes_count }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <p class="text-[#A1A1AA] text-sm font-['Inter'] leading-relaxed mb-3">{{ $comment->content }}</p>
                                            
                                            <div class="flex items-center gap-4">
                                                <form action="{{ route('lists.comments.like', $comment->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="flex items-center gap-1 text-[#71717A] hover:text-[#22C55E] transition-colors text-xs">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                        </svg>
                                                        Like
                                                    </button>
                                                </form>
                                                
                                                <button onclick="toggleReply({{ $comment->id }})" 
                                                        class="text-[#71717A] hover:text-white transition-colors text-xs">
                                                    Reply
                                                </button>
                                            </div>

                                            <!-- Reply Form (Hidden by default) -->
                                            <div id="reply-form-{{ $comment->id }}" class="hidden mt-4 pl-8">
                                                <form action="{{ route('lists.comments.store', $list->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                    <div class="space-y-3">
                                                        <textarea name="content" 
                                                                  placeholder="Write a reply..." 
                                                                  rows="2"
                                                                  class="w-full bg-[#27272A] border border-[#52525B] rounded-lg px-3 py-2 text-white placeholder-[#71717A] focus:outline-none focus:ring-1 focus:ring-[#2563EB] font-['Inter'] text-sm resize-none"
                                                                  required></textarea>
                                                        <div class="flex gap-2">
                                                            <button type="submit" 
                                                                    class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white px-4 py-1.5 rounded text-xs font-semibold transition-colors">
                                                                Reply
                                                            </button>
                                                            <button type="button" 
                                                                    onclick="toggleReply({{ $comment->id }})"
                                                                    class="text-[#71717A] hover:text-white px-4 py-1.5 text-xs transition-colors">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Replies -->
                                            @if($comment->replies && $comment->replies->count() > 0)
                                                <div class="mt-4 pl-8 space-y-3">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="bg-[#27272A] rounded-lg p-3 border border-[#52525B]">
                                                            <div class="flex items-start justify-between mb-2">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="w-6 h-6 bg-[#2563EB] rounded-full flex items-center justify-center text-white font-bold text-xs">
                                                                        {{ strtoupper(substr($reply->user->name ?? 'A', 0, 1)) }}
                                                                    </div>
                                                                    <div>
                                                                        <div class="text-white font-semibold text-xs">{{ $reply->user->name ?? 'Anonymous' }}</div>
                                                                        <div class="text-[#71717A] text-xs">{{ $reply->created_at->diffForHumans() }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="text-[#A1A1AA] text-xs font-['Inter'] leading-relaxed">{{ $reply->content }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <p class="text-[#71717A] font-['Inter']">No comments yet. Be the first to share your thoughts!</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <!-- Enhanced Guest Actions -->
                <div class="mt-12 bg-gradient-to-br from-[#27272A]/80 to-[#1A1A1B]/80 backdrop-blur-sm rounded-2xl border border-[#3F3F46] p-8 text-center hover:border-[#52525B] transition-all duration-300">
                    <div class="w-20 h-20 bg-gradient-to-r from-[#7C3AED] to-[#A855F7] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-white font-['Share_Tech_Mono'] mb-4 bg-gradient-to-r from-white to-[#A1A1AA] bg-clip-text text-transparent">Join the Community</h3>
                    <p class="text-[#A1A1AA] font-['Inter'] mb-8 text-lg max-w-md mx-auto">Sign in to follow this list, leave comments, and connect with other gamers in our vibrant community!</p>
                    
                    <!-- Benefits -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-[#18181B] rounded-xl p-4 border border-[#3F3F46]">
                            <svg class="w-8 h-8 text-[#F59E0B] mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            <div class="text-white font-semibold text-sm mb-1">Follow Lists</div>
                            <div class="text-[#A1A1AA] text-xs">Stay updated on your favorite collections</div>
                        </div>
                        <div class="bg-[#18181B] rounded-xl p-4 border border-[#3F3F46]">
                            <svg class="w-8 h-8 text-[#22C55E] mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-white font-semibold text-sm mb-1">Join Discussions</div>
                            <div class="text-[#A1A1AA] text-xs">Share thoughts and connect with gamers</div>
                        </div>
                        <div class="bg-[#18181B] rounded-xl p-4 border border-[#3F3F46]">
                            <svg class="w-8 h-8 text-[#2563EB] mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                            </svg>
                            <div class="text-white font-semibold text-sm mb-1">Create Lists</div>
                            <div class="text-[#A1A1AA] text-xs">Build and share your own collections</div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
                        <a href="{{ route('login') }}" 
                           class="flex-1 bg-gradient-to-r from-[#7C3AED] to-[#6D28D9] hover:from-[#6D28D9] hover:to-[#5B21B6] text-white px-8 py-4 rounded-xl font-bold transition-all duration-300 font-['Inter'] flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" 
                           class="flex-1 bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] hover:from-[#1D4ED8] hover:to-[#1E40AF] text-white px-8 py-4 rounded-xl font-bold transition-all duration-300 font-['Inter'] flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Create Account
                        </a>
                    </div>
                </div>
            @endauth

            <!-- Enhanced Back to Site -->
            <div class="text-center mt-12">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center bg-gradient-to-r from-[#18181B] to-[#27272A] hover:from-[#27272A] hover:to-[#3F3F46] text-[#A1A1AA] hover:text-white px-8 py-4 rounded-xl font-bold transition-all duration-300 font-['Inter'] border border-[#3F3F46] hover:border-[#52525B] shadow-lg hover:shadow-xl group">
                    <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Site
                </a>
            </div>
        </div>
    </div>
@else
    <div class="min-h-screen bg-[#151515] flex items-center justify-center">
        <div class="text-center">
            <svg class="w-20 h-20 mx-auto mb-6 text-[#3F3F46]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <h1 class="text-3xl font-bold text-white font-['Share_Tech_Mono'] mb-4">List Not Found</h1>
            <p class="text-[#A1A1AA] font-['Inter'] mb-6">This list doesn't exist or is not public.</p>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center bg-[#7C3AED] hover:bg-[#6D28D9] text-white px-6 py-3 rounded-lg font-semibold transition-colors font-['Inter']">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Site
            </a>
                 </div>
     </div>
@endif

<script>
function toggleReply(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm.classList.contains('hidden')) {
        replyForm.classList.remove('hidden');
        replyForm.querySelector('textarea').focus();
    } else {
        replyForm.classList.add('hidden');
    }
}


</script>
</x-layouts.app> 