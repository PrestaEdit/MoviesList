<div class="relative min-h-screen" x-data="{ filtersOpen: false }">

    {{-- Barre sticky : statut + bouton filtres --}}
    <div class="px-4 py-3 flex gap-2 overflow-x-auto sticky bg-slate-50 dark:bg-slate-900 z-10 border-b border-slate-200 dark:border-slate-800" style="top: var(--safe-top);">
        <button wire:click="$set('statusFilter', '')"
            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === '' ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">Tous</button>
        <button wire:click="$set('statusFilter', 'to_watch')"
            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'to_watch' ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">À voir</button>
        <button wire:click="$set('statusFilter', 'watched')"
            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'watched' ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">Vus</button>

        {{-- Bouton filtres avec badge --}}
        @php $activeCount = (int)(bool)$typeFilter + (int)(bool)$durationFilter + (int)(bool)$genreFilter; @endphp
        <button @click="filtersOpen = !filtersOpen"
            class="relative ml-auto px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors shrink-0"
            :class="filtersOpen || {{ $activeCount }} > 0 ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700'">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 12h10M11 20h2"/>
                </svg>
                Filtres
                @if($activeCount > 0)
                <span class="w-4 h-4 rounded-full bg-white text-sky-500 text-[10px] font-bold flex items-center justify-center leading-none">{{ $activeCount }}</span>
                @endif
            </span>
        </button>
    </div>

    {{-- Panel filtres animé --}}
    <div x-show="filtersOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 px-4 py-4 space-y-4">

        {{-- Type --}}
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-2">Type</p>
            <div class="flex gap-2">
                <button wire:click="setTypeFilter('movie')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $typeFilter === 'movie' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">🎬 Films</button>
                <button wire:click="setTypeFilter('tv')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $typeFilter === 'tv' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">📺 Séries</button>
            </div>
        </div>

        {{-- Durée --}}
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-2">Durée</p>
            <div class="flex gap-2">
                <button wire:click="setDurationFilter('short')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $durationFilter === 'short' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">≤ 1h</button>
                <button wire:click="setDurationFilter('medium')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $durationFilter === 'medium' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">1h – 2h</button>
                <button wire:click="setDurationFilter('long')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $durationFilter === 'long' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">> 2h</button>
            </div>
        </div>

        {{-- Genres --}}
        @if($genres->count())
        <div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-2">Genre</p>
            <div class="flex flex-wrap gap-2">
                @foreach($genres as $genre)
                <button wire:click="toggleGenre('{{ $genre }}')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $genreFilter === $genre ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">{{ $genre }}</button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Reset --}}
        @if($activeCount > 0)
        <button wire:click="resetFilters" class="text-xs text-sky-500 dark:text-sky-400 font-semibold">
            Réinitialiser les filtres
        </button>
        @endif
    </div>

    {{-- Grille --}}
    @if($entries->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="text-5xl mb-4">🍿</div>
        <p class="text-slate-900 dark:text-white font-semibold">Aucun résultat</p>
        <p class="text-slate-400 text-sm mt-1">Modifie les filtres ou ajoute un titre.</p>
    </div>
    @else
    <div class="grid grid-cols-3 gap-1 p-1">
        @foreach($entries as $entry)
        <a href="/movie/{{ $entry->id }}" wire:navigate class="relative aspect-[2/3] block">
            @if($entry->movie->poster_path)
            <img src="{{ $entry->movie->posterUrl() }}" class="w-full h-full object-cover rounded-lg" alt="{{ $entry->movie->title }}">
            @else
            <div class="w-full h-full bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-300 text-2xl" title="{{ $entry->movie->title }}">🍿</div>
            @endif
            @if($entry->status === 'watched')
            <div class="absolute top-1 right-1 w-5 h-5 bg-sky-500 rounded-full flex items-center justify-center text-white text-xs">✓</div>
            @endif
            @if($entry->is_favorite)
            <div class="absolute top-1 left-1 text-yellow-400 text-xs">★</div>
            @endif
        </a>
        @endforeach
    </div>
    @endif

    {{-- FAB + modal --}}
    <livewire:movie-search />
</div>
