<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {!! seo()->for($post) !!}

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
                <nav class="mb-4">
                    <a href="{{ route('posts.index') }}" class="text-blue-600 hover:underline">← Back to Posts</a>
                </nav>
                
                <h1 class="text-4xl font-bold mb-4">{{ $post->title }}</h1>
                
                @if($post->author)
                    <p class="text-gray-600 mb-2">By {{ $post->author }}</p>
                @endif
                
                <p class="text-sm text-gray-500">
                    Published: {{ $post->created_at->format('F j, Y') }}
                    @if($post->updated_at != $post->created_at)
                        | Updated: {{ $post->updated_at->format('F j, Y') }}
                    @endif
                </p>
            </header>

            @if($post->featured_image)
                <div class="mb-8">
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg">
                </div>
            @endif

            @if($post->excerpt)
                <div class="mb-6 p-4 bg-gray-100 rounded-lg">
                    <p class="text-lg italic">{{ $post->excerpt }}</p>
                </div>
            @endif

            <div class="prose max-w-none">
                {!! nl2br(e($post->content)) !!}
            </div>
        </div>
    </body>
</html> 
