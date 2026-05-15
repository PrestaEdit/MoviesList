<div class="relative min-h-screen">
    <!-- Quick filters -->
    <div class="px-4 py-3 flex gap-2 border-b border-slate-800 overflow-x-auto">
        <button wire:click="$set('statusFilter', '')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === '' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">Tous</button>
        <button wire:click="$set('statusFilter', 'to_watch')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'to_watch' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">À voir</button>
        <button wire:click="$set('statusFilter', 'watched')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $statusFilter === 'watched' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">Vus</button>
        <button wire:click="$toggle('showAdvancedFilters')"
            class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $showAdvancedFilters ? 'bg-slate-600 text-white' : 'bg-slate-800 text-slate-400' }}">⚙ Filtres</button>
    </div>

    <!-- Advanced filters -->
    @if($showAdvancedFilters)
    <div class="px-4 py-3 border-b border-slate-800 space-y-3 bg-slate-800/50">
        <div class="flex gap-2">
            <select wire:model.live="typeFilter" class="flex-1 bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:border-red-500 focus:outline-none">
                <option value="">Tous types</option>
                <option value="movie">Films</option>
                <option value="tv">Séries</option>
            </select>
            <select wire:model.live="durationFilter" class="flex-1 bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:border-red-500 focus:outline-none">
                <option value="">Toutes durées</option>
                <option value="short">Court (≤ 60 min)</option>
                <option value="medium">Moyen (61–120 min)</option>
                <option value="long">Long (> 120 min)</option>
            </select>
        </div>
        @if($genres->count())
        <select wire:model.live="genreFilter" class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:border-red-500 focus:outline-none">
            <option value="">Tous genres</option>
            @foreach($genres as $genre)
            <option value="{{ $genre }}">{{ $genre }}</option>
            @endforeach
        </select>
        @endif
    </div>
    @endif

    <!-- Grid -->
    @if($entries->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="text-5xl mb-4">🎬</div>
        <p class="text-slate-400">Ta liste est vide.</p>
        <p class="text-slate-500 text-sm mt-1">Appuie sur + pour ajouter un titre.</p>
    </div>
    @else
    <div class="grid grid-cols-3 gap-1 p-1">
        @foreach($entries as $entry)
        <a href="/movie/{{ $entry->id }}" wire:navigate class="relative aspect-[2/3] block">
            @if($entry->movie->poster_path)
            <img src="{{ $entry->movie->posterUrl() }}" class="w-full h-full object-cover rounded" alt="{{ $entry->movie->title }}">
            @else
            <div class="w-full h-full bg-slate-800 rounded flex items-center justify-center text-slate-600 text-2xl" title="{{ $entry->movie->title }}">🎬</div>
            @endif
            @if($entry->status === 'watched')
            <div class="absolute top-1 right-1 w-5 h-5 bg-green-600 rounded-full flex items-center justify-center text-white text-xs">✓</div>
            @endif
            @if($entry->is_favorite)
            <div class="absolute top-1 left-1 text-yellow-400 text-xs">★</div>
            @endif
        </a>
        @endforeach
    </div>
    @endif

    <!-- Movie search FAB + modal -->
    <livewire:movie-search />
</div>
