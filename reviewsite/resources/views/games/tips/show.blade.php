@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-900 to-purple-900 py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $tip->title }}</h1>
                    <p class="text-blue-200">Tip for {{ $product->name }}</p>
                </div>
                <a href="{{ route('games.tips.index', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    ← Back to Tips
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Tip Content -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <div class="flex items-center space-x-4 text-sm text-gray-400 mb-2">
                                <span>By {{ $tip->user->name }}</span>
                                <span>{{ $tip->created_at->diffForHumans() }}</span>
                                <span class="bg-blue-600/20 text-blue-400 px-2 py-1 rounded">{{ $tip->category->name }}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <livewire:game-tip-like :tip="$tip" :wire:key="'tip-like-' . $tip->id" />
                        </div>
                    </div>

                    @if($tip->tags)
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($tip->tags as $tag)
                                <span class="bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded text-xs">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="prose prose-invert max-w-none mb-6">
                        {!! \App\Services\MarkdownService::parse($tip->content) !!}
                    </div>

                    @if($tip->youtube_link)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Video Tutorial</h3>
                            <div class="aspect-video rounded-lg overflow-hidden">
                                <iframe 
                                    src="{{ $tip->getEmbedUrl() }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    class="w-full h-full">
                                </iframe>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Comments Section -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Comments ({{ $tip->comments->where('status', 'approved')->count() }})</h2>

                                         @auth
                         @if(session('success'))
                             <div class="bg-green-600/20 border border-green-600 text-green-400 px-4 py-3 rounded mb-4">
                                 {{ session('success') }}
                             </div>
                         @endif

                         @if(session('error'))
                             <div class="bg-red-600/20 border border-red-600 text-red-400 px-4 py-3 rounded mb-4">
                                 {{ session('error') }}
                             </div>
                         @endif

                         @if($errors->any())
                            <div class="bg-red-600/20 border border-red-600 text-red-400 px-4 py-3 rounded mb-4">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('games.tips.comment', $tip) }}" method="POST" class="mb-8">
                            @csrf
                            <div>
                                <label for="comment_content" class="block text-sm font-medium mb-2">Add a Comment</label>
                                <textarea id="comment_content" name="content" rows="4" required 
                                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Share your thoughts on this tip...">{{ old('content') }}</textarea>
                                @error('content')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Post Comment
                            </button>
                        </form>
                    @else
                        <div class="bg-blue-600/20 border border-blue-600 rounded-lg p-4 mb-6">
                            <p class="text-blue-400">Please <a href="{{ route('login') }}" class="underline">log in</a> to comment on this tip.</p>
                        </div>
                    @endauth

                    @if($tip->comments->where('status', 'approved')->count() > 0)
                        <div class="space-y-4">
                            @foreach($tip->comments->where('status', 'approved')->sortBy('created_at') as $comment)
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-blue-400">{{ $comment->user->name }}</span>
                                            <span class="text-sm text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="prose prose-invert max-w-none">
                                        {!! \App\Services\MarkdownService::parse($comment->content) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-4xl mb-2">💬</div>
                            <p class="text-gray-400">No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg p-6 sticky top-8">
                    <h3 class="text-xl font-bold mb-4">Tip Details</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-gray-400 text-sm">Category</span>
                            <p class="font-semibold">{{ $tip->category->name }}</p>
                        </div>

                        <div>
                            <span class="text-gray-400 text-sm">Submitted</span>
                            <p class="font-semibold">{{ $tip->created_at->format('M j, Y') }}</p>
                        </div>

                        <div>
                            <span class="text-gray-400 text-sm">Likes</span>
                            <p class="font-semibold">{{ $tip->likes_count }}</p>
                        </div>

                        <div>
                            <span class="text-gray-400 text-sm">Comments</span>
                            <p class="font-semibold">{{ $tip->comments->where('status', 'approved')->count() }}</p>
                        </div>

                        @if($tip->tags)
                            <div>
                                <span class="text-gray-400 text-sm">Tags</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($tip->tags as $tag)
                                        <span class="bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded text-xs">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <a href="{{ route('games.tips.index', $product) }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-2 px-4 rounded-lg transition-colors">
                            View All Tips
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection 