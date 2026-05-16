<div class="relative min-h-screen">

    {{-- Quick filters --}}
    <div class="px-4 py-3 flex gap-2 overflow-x-auto sticky top-0 bg-slate-50 dark:bg-slate-900 z-10 border-b border-slate-200 dark:border-slate-800">
        <button wire:click="$set('statusFilter', '')"
            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === '' ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">Tous</button>
        <button wire:click="$set('statusFilter', 'to_watch')"
            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'to_watch' ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">À voir</button>
        <button wire:click="$set('statusFilter', 'watched')"
            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'watched' ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">Vus</button>
        <button wire:click="$toggle('showAdvancedFilters')"
            class="px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $showAdvancedFilters ? 'bg-sky-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">⚙ Filtres</button>
    </div>

    {{-- Advanced filters --}}
    @if($showAdvancedFilters)
    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 space-y-3 bg-white dark:bg-slate-800/50">
        <div class="flex gap-2">
            <select wire:model.live="typeFilter" class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white text-sm focus:border-sky-500 focus:outline-none">
                <option value="">Tous types</option>
                <option value="movie">Films</option>
                <option value="tv">Séries</option>
            </select>
            <select wire:model.live="durationFilter" class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white text-sm focus:border-sky-500 focus:outline-none">
                <option value="">Toutes durées</option>
                <option value="short">Court (≤ 60 min)</option>
                <option value="medium">Moyen (61–120 min)</option>
                <option value="long">Long (> 120 min)</option>
            </select>
        </div>
        @if($genres->count())
        <select wire:model.live="genreFilter" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-900 dark:text-white text-sm focus:border-sky-500 focus:outline-none">
            <option value="">Tous genres</option>
            @foreach($genres as $genre)
            <option value="{{ $genre }}">{{ $genre }}</option>
            @endforeach
        </select>
        @endif
    </div>
    @endif

    {{-- Grid --}}
    @if($entries->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="text-5xl mb-4">🎬</div>
        <p class="text-slate-900 dark:text-white font-semibold">Ta liste est vide</p>
        <p class="text-slate-400 text-sm mt-1">Appuie sur + pour ajouter un titre.</p>
    </div>
    @else
    <div class="grid grid-cols-3 gap-1 p-1">
        @foreach($entries as $entry)
        <a href="/movie/{{ $entry->id }}" wire:navigate class="relative aspect-[2/3] block">
            @if($entry->movie->poster_path)
            <img src="{{ $entry->movie->posterUrl() }}" class="w-full h-full object-cover rounded-lg" alt="{{ $entry->movie->title }}">
            @else
            <div class="w-full h-full bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-300 text-2xl" title="{{ $entry->movie->title }}">🎬</div>
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
