@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="mb-10 flex items-center gap-4">
        <div class="flex items-center justify-center w-14 h-14 rounded-full bg-[#2563EB]/10 border-2 border-[#2563EB]">
            <svg class="w-8 h-8 text-[#2563EB]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        <div>
            <h1 class="text-4xl font-bold text-white font-['Share_Tech_Mono'] mb-1">Keyword: <span class="text-[#2563EB] underline underline-offset-4">{{ $keyword->name }}</span></h1>
            <p class="text-[#A1A1AA] text-lg">Games tagged with <span class="font-semibold">{{ $keyword->name }}</span>:</p>
        </div>
    </div>
    @if($games->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($games as $game)
                <div class="bg-gradient-to-br from-[#232326] to-[#18181B] border border-[#2563EB]/20 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row gap-4 transition-transform hover:scale-[1.025] hover:shadow-2xl duration-200">
                    <div class="flex-shrink-0 w-28 h-36 rounded-lg overflow-hidden bg-[#1A1A1B] border border-[#3F3F46]/30 flex items-center justify-center">
                        <img src="{{ $game->image ?? 'https://placehold.co/180x240/1A1A1B/A1A1AA?text=No+Image' }}" alt="{{ $game->name }}" class="object-cover w-full h-full" />
                    </div>
                    <div class="flex-1 flex flex-col justify-center">
                        <a href="{{ route('games.show', $game->slug) }}" class="text-2xl font-bold text-white font-['Share_Tech_Mono'] hover:text-[#2563EB] transition-colors mb-3">{{ $game->name }}</a>
                        <div class="flex flex-wrap gap-2 mb-2">
                            @if($game->genre)
                                <span class="inline-block bg-green-600/20 text-green-400 px-3 py-1 rounded-full text-sm">{{ $game->genre->name }}</span>
                            @endif
                            @if($game->developers && $game->developers->count())
                                @foreach($game->developers as $dev)
                                    <span class="inline-block bg-purple-500/20 text-purple-400 px-3 py-1 rounded-full text-sm">{{ $dev->name }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-[#18181B] border border-[#3F3F46] rounded-xl p-8 text-center text-[#A1A1AA] text-lg shadow-lg mt-10">
            <span class="text-2xl">😕</span><br>
            No games found for this keyword.
        </div>
    @endif
</div>
@endsection 