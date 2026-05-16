<div class="px-4 py-4 space-y-6 pb-24">

    {{-- Favoris --}}
    @if($favorites->count())
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">⭐ Favoris</h2>
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4">
            @foreach($favorites as $entry)
            <a href="/movie/{{ $entry->id }}" wire:navigate class="shrink-0 w-24">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-24 h-36 object-cover rounded-lg" alt="{{ $entry->movie->title }}">
                @else
                <div class="w-24 h-36 bg-slate-800 rounded-lg flex items-center justify-center text-slate-600 text-2xl">🎬</div>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Vus récemment --}}
    @if($recent->count())
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">🕐 Vus récemment</h2>
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4">
            @foreach($recent as $entry)
            <a href="/movie/{{ $entry->id }}" wire:navigate class="shrink-0 w-24">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-24 h-36 object-cover rounded-lg" alt="{{ $entry->movie->title }}">
                @else
                <div class="w-24 h-36 bg-slate-800 rounded-lg flex items-center justify-center text-slate-600 text-2xl">🎬</div>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Pioche du jour --}}
    @if($randomPick)
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">🎲 Pioche du jour</h2>
        <div class="bg-slate-800 rounded-xl overflow-hidden">
            <a href="/movie/{{ $randomPick->id }}" wire:navigate class="flex gap-4 p-4">
                @if($randomPick->movie->poster_path)
                <img src="{{ $randomPick->movie->posterUrl() }}" class="w-16 h-24 object-cover rounded-lg shrink-0" alt="{{ $randomPick->movie->title }}">
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-semibold truncate">{{ $randomPick->movie->title }}</h3>
                    <p class="text-slate-400 text-sm mt-1">
                        {{ $randomPick->movie->release_date?->year }}
                        · {{ $randomPick->movie->type === 'tv' ? 'Série' : 'Film' }}
                    </p>
                    @if($randomPick->movie->synopsis)
                    <p class="text-slate-500 text-xs mt-2 line-clamp-2">{{ $randomPick->movie->synopsis }}</p>
                    @endif
                </div>
            </a>
            <div class="px-4 pb-4">
                <button wire:click="repick" class="w-full py-2 border border-slate-600 hover:border-red-500 text-slate-400 hover:text-red-500 rounded-lg text-sm transition-colors">
                    🔀 Repioche
                </button>
            </div>
        </div>
    </section>
    @endif

    {{-- Tu pourrais aimer --}}
    @if(count($recommendations))
    <section>
        <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">✨ Tu pourrais aimer</h2>
        <div class="grid grid-cols-3 gap-2">
            @foreach($recommendations as $rec)
            <div class="bg-slate-800 rounded-lg overflow-hidden">
                @if($rec['poster_path'] ?? null)
                <div class="aspect-[2/3]">
                    <img src="https://image.tmdb.org/t/p/w342{{ $rec['poster_path'] }}" class="w-full h-full object-cover" alt="{{ $rec['title'] ?? $rec['name'] ?? '' }}">
                </div>
                @else
                <div class="aspect-[2/3] flex items-center justify-center text-slate-600 text-2xl">🎬</div>
                @endif
                <p class="text-slate-300 text-xs px-2 py-1 truncate">{{ $rec['title'] ?? $rec['name'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Empty state --}}
    @if($favorites->isEmpty() && $recent->isEmpty() && !$randomPick)
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">🎬</div>
        <p class="text-white font-semibold">Ta liste est vide</p>
        <p class="text-slate-400 text-sm mt-2">Va dans "Mes films" et appuie sur + pour commencer.</p>
    </div>
    @endif

</div>
