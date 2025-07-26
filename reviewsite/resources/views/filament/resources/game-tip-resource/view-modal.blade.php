<div class="space-y-6">
    <!-- Tip Header Information -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $tip->title }}</h3>
                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <div><span class="font-medium">Game:</span> {{ $tip->product->name }}</div>
                    <div><span class="font-medium">Category:</span> {{ $tip->category->name }}</div>
                    <div><span class="font-medium">Submitted by:</span> {{ $tip->user->name }}</div>
                    <div><span class="font-medium">Date:</span> {{ $tip->created_at->format('M j, Y \a\t g:i A') }}</div>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $tip->likes_count }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Likes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $tip->comments_count }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Comments</div>
                    </div>
                    <div class="text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($tip->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @elseif($tip->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                            {{ ucfirst($tip->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tags -->
    @if($tip->tags && count($tip->tags) > 0)
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags:</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($tip->tags as $tag)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Content -->
    <div>
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content:</h4>
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="prose prose-sm dark:prose-invert max-w-none">
                {!! \App\Services\MarkdownService::parse($tip->content) !!}
            </div>
        </div>
    </div>

    <!-- YouTube Video -->
    @if($tip->youtube_link)
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">YouTube Video:</h4>
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="aspect-video rounded-lg overflow-hidden">
                    <iframe 
                        src="{{ $tip->getEmbedUrl() }}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        class="w-full h-full">
                    </iframe>
                </div>
                <div class="mt-2">
                    <a href="{{ $tip->youtube_link }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                        View on YouTube →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- User Information -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Submitter Information:</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Name:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->user->name }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Email:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->user->email }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Member since:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->user->created_at->format('M j, Y') }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Total tips submitted:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->user->gameTips()->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Game Information -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Game Information:</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Game:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->product->name }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Total tips for this game:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->product->gameTips()->count() }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Approved tips:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->product->gameTips()->approved()->count() }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Pending tips:</span>
                <span class="text-gray-900 dark:text-white">{{ $tip->product->gameTips()->pending()->count() }}</span>
            </div>
        </div>
    </div>
</div> 