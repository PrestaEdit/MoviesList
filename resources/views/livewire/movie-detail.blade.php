<div class="min-h-screen bg-slate-900">
    <!-- Backdrop header -->
    @if($entry->movie->backdrop_path)
    <div class="relative h-48">
        <img src="{{ $entry->movie->backdropUrl() }}" class="w-full h-full object-cover" alt="">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900"></div>
        <a href="/" wire:navigate class="absolute top-4 left-4 text-white bg-slate-900/60 rounded-full w-8 h-8 flex items-center justify-center">←</a>
    </div>
    @else
    <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-3">
        <a href="/" wire:navigate class="text-slate-400">←</a>
    </div>
    @endif

    <div class="px-4 py-4 space-y-5">
        <!-- Title + favorite -->
        <div class="flex items-start justify-between gap-3">
            <div class="flex gap-3">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-16 h-24 object-cover rounded-lg shrink-0" alt="{{ $entry->movie->title }}">
                @endif
                <div>
                    <h1 class="text-white font-bold text-lg leading-tight">{{ $entry->movie->title }}</h1>
                    <p class="text-slate-400 text-sm mt-1">
                        {{ $entry->movie->release_date?->year }}
                        @if($entry->movie->type === 'tv') · Série @else · Film @endif
                        @if($entry->movie->duration) · {{ $entry->movie->duration }} min @endif
                    </p>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($entry->movie->genres ?? [] as $genre)
                        <span class="text-xs bg-slate-700 text-slate-300 px-2 py-0.5 rounded-full">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <button wire:click="toggleFavorite" class="text-2xl shrink-0 mt-1">
                {{ $entry->is_favorite ? '★' : '☆' }}
            </button>
        </div>

        <!-- Synopsis -->
        @if($entry->movie->synopsis)
        <p class="text-slate-400 text-sm leading-relaxed">{{ $entry->movie->synopsis }}</p>
        @endif

        <!-- Mon avis -->
        <div class="border-t border-slate-800 pt-4">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-white font-semibold">Mon avis</h2>
                <button wire:click="$toggle('editing')" class="text-red-500 text-sm">
                    {{ $editing ? 'Annuler' : 'Modifier' }}
                </button>
            </div>

            @if(!$editing)
            <div class="space-y-2">
                <p class="text-sm">
                    <span class="text-slate-500">Statut : </span>
                    <span class="text-white">{{ $entry->status === 'watched' ? 'Vu ✓' : 'À voir' }}</span>
                </p>
                @if($entry->rating)
                <p class="text-sm"><span class="text-slate-500">Note : </span><span class="text-white">{{ $entry->rating }}/10</span></p>
                @endif
                @if($entry->comment)
                <p class="text-sm"><span class="text-slate-500">Commentaire : </span><span class="text-white">{{ $entry->comment }}</span></p>
                @endif
                @if($entry->watched_at)
                <p class="text-sm"><span class="text-slate-500">Vu le : </span><span class="text-white">{{ $entry->watched_at->format('d/m/Y') }}</span></p>
                @endif
                @if($entry->coWatchers->count())
                <p class="text-sm">
                    <span class="text-slate-500">Avec : </span>
                    <span class="text-white">{{ $entry->coWatchers->pluck('name')->join(', ') }}</span>
                </p>
                @endif
            </div>
            @else
            <!-- Edit form -->
            <div class="space-y-4">
                <div class="flex gap-2">
                    <button wire:click="$set('status', 'to_watch')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'to_watch' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">À voir</button>
                    <button wire:click="$set('status', 'watched')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'watched' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">Vu ✓</button>
                </div>
                @if($status === 'watched')
                <input wire:model="watchedAt" type="date"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none">
                <div>
                    <label class="text-slate-400 text-xs mb-1 block">Note : {{ $rating ?? '—' }}/10</label>
                    <input wire:model="rating" type="range" min="1" max="10" class="w-full accent-red-500">
                </div>
                <textarea wire:model="comment" rows="2" placeholder="Ton avis…"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:border-red-500 focus:outline-none resize-none"></textarea>
                @if($coWatchers->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($coWatchers as $cw)
                    <button wire:click="$toggle('selectedCoWatcherIds', {{ $cw->id }})"
                        class="px-3 py-1 rounded-full text-sm transition-colors {{ in_array($cw->id, $selectedCoWatcherIds) ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ $cw->name }}
                    </button>
                    @endforeach
                </div>
                @endif
                @endif
                <button wire:click="save"
                    class="w-full py-3 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition-colors">
                    Enregistrer
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
