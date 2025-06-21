<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {!! seo() !!}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] p-6">
        <div class="max-w-2xl mx-auto">
            <header class="mb-8">
                <nav class="mb-4">
                    <a href="{{ route('posts.index') }}" class="text-blue-600 hover:underline">← Back to Posts</a>
                </nav>
                <h1 class="text-4xl font-bold mb-4">Create New Post</h1>
            </header>

            <form action="{{ route('posts.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="title" class="block text-sm font-medium mb-2">Title *</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('title') }}">
                    @error('title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author" class="block text-sm font-medium mb-2">Author</label>
                    <input type="text" id="author" name="author" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('author') }}">
                </div>

                <div>
                    <label for="excerpt" class="block text-sm font-medium mb-2">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('excerpt') }}</textarea>
                </div>

                <div>
                    <label for="featured_image" class="block text-sm font-medium mb-2">Featured Image URL</label>
                    <input type="url" id="featured_image" name="featured_image" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('featured_image') }}">
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium mb-2">Content *</label>
                    <textarea id="content" name="content" required rows="10" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Create Post
                    </button>
                    <a href="{{ route('posts.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </body>
</html> 