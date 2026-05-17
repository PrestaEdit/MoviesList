<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    {{-- Backdrop header --}}
    @if($entry->movie->backdrop_path)
    <div class="relative h-48">
        <img src="{{ $entry->movie->backdropUrl() }}" class="w-full h-full object-cover" alt="">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-50 dark:to-slate-900"></div>
        <a href="/" wire:navigate class="absolute top-4 left-4 w-8 h-8 rounded-xl bg-white/80 dark:bg-slate-900/80 flex items-center justify-center text-slate-700 dark:text-white shadow-sm">←</a>
    </div>
    @else
    <div class="px-4 py-4 flex items-center gap-3" style="padding-top: calc(var(--safe-top, 0px) + 1rem);">
        <a href="/" wire:navigate class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">←</a>
    </div>
    @endif

    <div class="px-4 py-4 space-y-5 max-w-lg mx-auto">
        {{-- Title + favorite --}}
        <div class="flex items-start justify-between gap-3">
            <div class="flex gap-3">
                @if($entry->movie->poster_path)
                <img src="{{ $entry->movie->posterUrl() }}" class="w-16 h-24 object-cover rounded-2xl shadow-sm shrink-0" alt="{{ $entry->movie->title }}">
                @endif
                <div>
                    <h1 class="text-slate-900 dark:text-white font-extrabold text-lg leading-tight">{{ $entry->movie->title }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        {{ $entry->movie->release_date?->year }}
                        @if($entry->movie->type === 'tv') · Série @else · Film @endif
                        @if($entry->movie->duration) · {{ $entry->movie->duration }} min @endif
                    </p>
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($entry->movie->genres ?? [] as $genre)
                        <span class="text-xs bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 px-2 py-0.5 rounded-full font-medium">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <button wire:click="toggleFavorite" class="text-2xl shrink-0 mt-1 transition-colors {{ $entry->is_favorite ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600' }}">
                {{ $entry->is_favorite ? '★' : '☆' }}
            </button>
        </div>

        {{-- Synopsis --}}
        @if($entry->movie->synopsis)
        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">{{ $entry->movie->synopsis }}</p>
        @endif

        {{-- Mon avis --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-slate-900 dark:text-white font-extrabold">Mon avis</h2>
                <button wire:click="$toggle('editing')" class="text-sky-500 text-sm font-medium">
                    {{ $editing ? 'Annuler' : 'Modifier' }}
                </button>
            </div>

            @if(!$editing)
            <div class="space-y-2.5">
                <div class="flex justify-between items-center">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Statut</p>
                    <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $entry->status === 'watched' ? 'Vu ✓' : 'À voir' }}</span>
                </div>
                @if($entry->rating)
                <div class="flex justify-between items-center">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Note</p>
                    <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $entry->rating }}/10</span>
                </div>
                @endif
                @if($entry->comment)
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Commentaire</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $entry->comment }}</p>
                </div>
                @endif
                @if($entry->watched_at)
                <div class="flex justify-between items-center">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Vu le</p>
                    <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $entry->watched_at->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($entry->coWatchers->count())
                <div class="flex justify-between items-center">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Avec</p>
                    <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $entry->coWatchers->pluck('name')->join(', ') }}</span>
                </div>
                @endif
            </div>
            @else
            {{-- Edit form --}}
            <div class="space-y-4">
                <div class="flex gap-2">
                    <button wire:click="$set('status', 'to_watch')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $status === 'to_watch' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">À voir</button>
                    <button wire:click="$set('status', 'watched')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $status === 'watched' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">Vu ✓</button>
                </div>
                @if($status === 'watched')
                <input wire:model="watchedAt" type="date"
                    class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:border-sky-500 focus:outline-none">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Note : {{ $rating ?? '—' }}/10</p>
                    <input wire:model="rating" type="range" min="1" max="10" class="w-full accent-sky-500">
                </div>
                <textarea wire:model="comment" rows="2" placeholder="Ton avis…"
                    class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white placeholder-slate-400 focus:border-sky-500 focus:outline-none resize-none"></textarea>
                @if($coWatchers->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($coWatchers as $cw)
                    <button wire:click="toggleCoWatcher({{ $cw->id }})"
                        class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ in_array($cw->id, $selectedCoWatcherIds) ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                        {{ $cw->name }}
                    </button>
                    @endforeach
                </div>
                @endif
                @endif
                <button wire:click="save"
                    class="w-full py-3 text-white font-semibold rounded-xl transition-colors text-sm"
                    style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                    Enregistrer
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
