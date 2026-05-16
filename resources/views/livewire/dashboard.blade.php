<div class="p-4 max-w-lg mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between pt-1">
        <div>
            <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Bonsoir</p>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ \App\Models\Profile::first()?->name ?? '' }} 👋</h1>
        </div>
    </div>

    {{-- Favoris --}}
    @if($favorites->count())
    <section>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">⭐ Favoris</p>
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4">
            @foreach($favorites as $entry)
            <a href="/movie/{{ $entry->id }}" wire:navigate class="shrink-0 w-24">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-24 h-36 object-cover rounded-2xl shadow-sm" alt="{{ $entry->movie->title }}">
                @else
                <div class="w-24 h-36 bg-white dark:bg-slate-800 rounded-2xl shadow-sm flex items-center justify-center text-slate-400 text-2xl">🎬</div>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Vus récemment --}}
    @if($recent->count())
    <section>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">🕐 Vus récemment</p>
        <div class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4">
            @foreach($recent as $entry)
            <a href="/movie/{{ $entry->id }}" wire:navigate class="shrink-0 w-24">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-24 h-36 object-cover rounded-2xl shadow-sm" alt="{{ $entry->movie->title }}">
                @else
                <div class="w-24 h-36 bg-white dark:bg-slate-800 rounded-2xl shadow-sm flex items-center justify-center text-slate-400 text-2xl">🎬</div>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Pioche du jour --}}
    @if($randomPick)
    <section>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">🎲 Pioche du jour</p>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <a href="/movie/{{ $randomPick->id }}" wire:navigate class="flex gap-4 p-4">
                @if($randomPick->movie->poster_path)
                <img src="{{ $randomPick->movie->posterUrl() }}" class="w-16 h-24 object-cover rounded-xl shrink-0" alt="{{ $randomPick->movie->title }}">
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-slate-900 dark:text-white font-semibold truncate">{{ $randomPick->movie->title }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        {{ $randomPick->movie->release_date?->year }}
                        · {{ $randomPick->movie->type === 'tv' ? 'Série' : 'Film' }}
                    </p>
                    @if($randomPick->movie->synopsis)
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-2 line-clamp-2">{{ $randomPick->movie->synopsis }}</p>
                    @endif
                </div>
            </a>
            <div class="px-4 pb-4">
                <button wire:click="repick" class="w-full py-2.5 bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-medium transition-colors">
                    🔀 Repioche
                </button>
            </div>
        </div>
    </section>
    @endif

    {{-- Tu pourrais aimer --}}
    @if(count($recommendations))
    <section>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">✨ Tu pourrais aimer</p>
        <div class="grid grid-cols-3 gap-2">
            @foreach($recommendations as $rec)
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm overflow-hidden">
                @if($rec['poster_path'] ?? null)
                <div class="aspect-[2/3]">
                    <img src="https://image.tmdb.org/t/p/w342{{ $rec['poster_path'] }}" class="w-full h-full object-cover" alt="{{ $rec['title'] ?? $rec['name'] ?? '' }}">
                </div>
                @else
                <div class="aspect-[2/3] flex items-center justify-center text-slate-300 text-2xl">🎬</div>
                @endif
                <p class="text-slate-600 dark:text-slate-300 text-xs px-2 py-1.5 truncate font-medium">{{ $rec['title'] ?? $rec['name'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Empty state --}}
    @if($favorites->isEmpty() && $recent->isEmpty() && !$randomPick)
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">🎬</div>
        <p class="text-slate-900 dark:text-white font-semibold">Ta liste est vide</p>
        <p class="text-slate-400 text-sm mt-2">Va dans "Mes films" et appuie sur + pour commencer.</p>
    </div>
    @endif

</div>
