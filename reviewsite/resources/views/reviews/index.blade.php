@extends('layouts.app')

@section('title', 'Reviews')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-mono font-bold text-red-600 mb-6">Game & Hardware Reviews</h1>
    <form method="GET" action="{{ route('reviews.index') }}" class="flex flex-wrap gap-4 mb-8">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search games or hardware..." class="px-4 py-2 rounded bg-zinc-900 text-white border border-zinc-700 focus:border-red-600 focus:ring-0 font-mono" style="min-width: 220px;">
        <select name="type" class="px-4 py-2 rounded bg-zinc-900 text-white border border-zinc-700 focus:border-red-600 font-mono">
            <option value="">All Types</option>
            <option value="game" @if(request('type')==='game') selected @endif>Games</option>
            <option value="hardware" @if(request('type')==='hardware') selected @endif>Hardware</option>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @forelse($products as $product)
            <a href="{{ route('reviews.show', $product) }}" class="block bg-zinc-900 rounded-lg border border-zinc-800 hover:border-red-600 transition p-4 shadow-lg">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded mb-4">
                <div class="font-mono text-lg text-white font-bold mb-1">{{ $product->name }}</div>
                <div class="text-xs uppercase tracking-wider text-red-600 font-mono mb-2">{{ ucfirst($product->type) }}</div>
                <div class="text-zinc-400 text-sm line-clamp-3 mb-2">{{ Str::limit($product->description, 80) }}</div>
                @if($product->staff_rating)
                    <div class="text-yellow-400 font-mono text-sm">Staff Rating: <span class="font-bold">{{ $product->staff_rating }}/10</span></div>
                @endif
            </a>
        @empty
            <div class="col-span-full text-center text-zinc-400 font-mono">No products found.</div>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->withQueryString()->links() }}</div>
</div>
@endsection 