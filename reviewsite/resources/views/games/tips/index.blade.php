@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-900 to-purple-900 py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $product->name }} - Tips & Tricks</h1>
                    <p class="text-blue-200">Share your knowledge and discover helpful tips from the community</p>
                </div>
                <a href="{{ route('games.show', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    ← Back to Game
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Tips List -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-4">Community Tips</h2>
                    
                    @if($tips->count() > 0)
                        <div class="space-y-6">
                            @foreach($tips as $tip)
                                <div class="bg-gray-700 rounded-lg p-6 border-l-4 border-blue-500">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-semibold mb-2">{{ $tip->title }}</h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-400">
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
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($tip->tags as $tag)
                                                <span class="bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded text-xs">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="prose prose-invert max-w-none mb-4">
                                        {!! \App\Services\MarkdownService::parse($tip->content) !!}
                                    </div>

                                    @if($tip->youtube_link)
                                        <div class="mb-4">
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

                                    <div class="flex items-center justify-between pt-4 border-t border-gray-600">
                                        <div class="flex items-center space-x-4 text-sm text-gray-400">
                                            <span>{{ $tip->comments_count }} comments</span>
                                        </div>
                                        <a href="{{ route('games.tips.show', [$product, $tip]) }}" class="text-blue-400 hover:text-blue-300 transition-colors">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $tips->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">💡</div>
                            <h3 class="text-xl font-semibold mb-2">No tips yet!</h3>
                            <p class="text-gray-400">Be the first to share a helpful tip for this game.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Tip Form -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg p-6 sticky top-8">
                    <h2 class="text-2xl font-bold mb-4">Submit a Tip</h2>
                    
                    @if(session('success'))
                        <div class="bg-green-600/20 border border-green-600 text-green-400 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('games.tips.store', $product) }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium mb-2">Tip Title</label>
                                <input type="text" id="title" name="title" required 
                                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter a descriptive title">
                                @error('title')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="game_tip_category_id" class="block text-sm font-medium mb-2">Category</label>
                                <select id="game_tip_category_id" name="game_tip_category_id" required 
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('game_tip_category_id')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="content" class="block text-sm font-medium mb-2">Content (Markdown supported)</label>
                                <textarea id="content" name="content" rows="8" required 
                                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Write your tip here... You can use Markdown formatting including **bold**, *italic*, and [spoiler] tags."></textarea>
                                @error('content')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="youtube_link" class="block text-sm font-medium mb-2">YouTube Video Link (Optional)</label>
                                <input type="url" id="youtube_link" name="youtube_link" 
                                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="https://www.youtube.com/watch?v=...">
                                @error('youtube_link')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Tags (Optional)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach(['Spoiler', 'Patch Dependent', 'Outdated', 'Beginner', 'Advanced', 'Exploit'] as $tag)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="tags[]" value="{{ $tag }}" 
                                                   class="rounded border-gray-600 bg-gray-700 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm">{{ $tag }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('tags')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                Submit Tip
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 p-4 bg-blue-600/20 border border-blue-600 rounded-lg">
                        <h3 class="font-semibold text-blue-400 mb-2">💡 Tip Guidelines</h3>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Be specific and helpful</li>
                            <li>• Use Markdown for formatting</li>
                            <li>• Add [spoiler] tags for story content</li>
                            <li>• All tips are reviewed before approval</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection 