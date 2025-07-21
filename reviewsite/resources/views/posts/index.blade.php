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
        <div class="max-w-4xl mx-auto">
            <header class="mb-8">
                <h1 class="text-4xl font-bold mb-4">Blog Posts</h1>
                <a href="{{ route('posts.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Create New Post
                </a>
            </header>

            @if($posts->count() > 0)
                <div class="grid gap-6">
                    @foreach($posts as $post)
                        <article class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <h2 class="text-2xl font-semibold mb-2">
                                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:underline">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            @if($post->author)
                                <p class="text-gray-600 mb-2">By {{ $post->author }}</p>
                            @endif
                            
                            @if($post->excerpt)
                                <p class="text-gray-700 mb-4">{{ $post->excerpt }}</p>
                            @endif
                            
                            <p class="text-sm text-gray-500">
                                Published: {{ $post->created_at->format('F j, Y') }}
                            </p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600 text-lg">No posts found.</p>
                    <a href="{{ route('posts.create') }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Create Your First Post
                    </a>
                </div>
            @endif
        </div>
    </body>
</html> 
