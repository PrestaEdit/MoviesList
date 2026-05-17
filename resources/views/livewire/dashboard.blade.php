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
                <div class="w-24 h-36 bg-white dark:bg-slate-800 rounded-2xl shadow-sm flex items-center justify-center text-slate-400 text-2xl">🍿</div>
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
                <div class="w-24 h-36 bg-white dark:bg-slate-800 rounded-2xl shadow-sm flex items-center justify-center text-slate-400 text-2xl">🍿</div>
                @endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Pioche du jour --}}
    @if($hasToWatch)
    <section>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">🎲 Pioche du jour</p>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm overflow-hidden">

            {{-- Filtres rapides --}}
            <div class="flex items-center justify-between px-3 py-2.5 border-b border-slate-100 dark:border-slate-700/60">
                {{-- Toggle type --}}
                <div class="flex gap-1">
                    <button wire:click="setPickType('')"
                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $pickType === '' ? 'bg-sky-500 text-white' : 'text-slate-400 dark:text-slate-500' }}">Tous</button>
                    <button wire:click="setPickType('movie')"
                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $pickType === 'movie' ? 'bg-sky-500 text-white' : 'text-slate-400 dark:text-slate-500' }}">Film</button>
                    <button wire:click="setPickType('tv')"
                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $pickType === 'tv' ? 'bg-sky-500 text-white' : 'text-slate-400 dark:text-slate-500' }}">Série</button>
                </div>
                {{-- Durée --}}
                <div class="flex gap-1">
                    <button wire:click="setPickDuration('short')"
                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $pickDuration === 'short' ? 'bg-sky-500 text-white' : 'text-slate-400 dark:text-slate-500' }}">Court</button>
                    <button wire:click="setPickDuration('medium')"
                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $pickDuration === 'medium' ? 'bg-sky-500 text-white' : 'text-slate-400 dark:text-slate-500' }}">Moyen</button>
                    <button wire:click="setPickDuration('long')"
                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ $pickDuration === 'long' ? 'bg-sky-500 text-white' : 'text-slate-400 dark:text-slate-500' }}">Long</button>
                </div>
            </div>

            @if($randomPick)
            <a href="/movie/{{ $randomPick->id }}" wire:navigate class="flex gap-4 p-4">
                @if($randomPick->movie->poster_path)
                <img src="{{ $randomPick->movie->posterUrl() }}" class="w-16 h-24 object-cover rounded-xl shrink-0" alt="{{ $randomPick->movie->title }}">
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-slate-900 dark:text-white font-semibold truncate">{{ $randomPick->movie->title }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        {{ $randomPick->movie->release_date?->year }}
                        · {{ $randomPick->movie->type === 'tv' ? 'Série' : 'Film' }}
                        @if($randomPick->movie->duration) · {{ $randomPick->movie->duration }} min @endif
                    </p>
                    @if($randomPick->movie->synopsis)
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-2 line-clamp-2">{{ $randomPick->movie->synopsis }}</p>
                    @endif
                </div>
            </a>
            @else
            <div class="px-4 py-6 text-center">
                <p class="text-slate-400 dark:text-slate-500 text-sm">Aucun titre ne correspond à ces filtres.</p>
            </div>
            @endif

            <div class="px-4 pb-4">
                <button wire:click="repick" class="w-full py-2.5 bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-medium transition-colors flex items-center justify-center gap-2">
                    <svg viewBox="0 0 20 20" class="w-4 h-4 shrink-0">
                        <rect x="1" y="1" width="18" height="18" rx="3" fill="currentColor"/>
                        <circle cx="5.5" cy="5.5" r="1.5" fill="white"/>
                        <circle cx="14.5" cy="5.5" r="1.5" fill="white"/>
                        <circle cx="10" cy="10" r="1.5" fill="white"/>
                        <circle cx="5.5" cy="14.5" r="1.5" fill="white"/>
                        <circle cx="14.5" cy="14.5" r="1.5" fill="white"/>
                    </svg>
                    Repioche
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
                <div class="aspect-[2/3] flex items-center justify-center text-slate-300 text-2xl">🍿</div>
                @endif
                <p class="text-slate-600 dark:text-slate-300 text-xs px-2 py-1.5 truncate font-medium">{{ $rec['title'] ?? $rec['name'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Empty state --}}
    @if($favorites->isEmpty() && $recent->isEmpty() && !$hasToWatch)
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">🍿</div>
        <p class="text-slate-900 dark:text-white font-semibold">Ta liste est vide</p>
        <p class="text-slate-400 text-sm mt-2">Va dans "Mes films" et appuie sur + pour commencer.</p>
    </div>
    @endif

</div>
