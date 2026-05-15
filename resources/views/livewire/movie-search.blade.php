<div>
    <!-- FAB -->
    <button
        wire:click="$set('open', true)"
        class="fixed bottom-6 right-6 z-20 w-14 h-14 bg-red-600 hover:bg-red-500 rounded-full flex items-center justify-center text-white text-2xl shadow-lg shadow-red-900/50 transition-colors"
    >+</button>

    <!-- Modal -->
    <div x-show="$wire.open" class="fixed inset-0 z-30 bg-slate-900/95 flex flex-col" x-transition>
        <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-3">
            <button wire:click="$set('open', false)" class="text-slate-400">✕</button>
            <h2 class="text-white font-semibold">Ajouter un titre</h2>
        </div>

        @if(!$selectedMovie)
        <!-- Search step -->
        <div class="px-4 py-4">
            <input
                wire:model.live.debounce.300ms="query"
                type="text"
                placeholder="Rechercher un film ou une série…"
                class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:border-red-500 focus:outline-none"
                autofocus
            >
        </div>
        <div class="flex-1 overflow-y-auto px-4 space-y-2">
            @foreach($results as $result)
                @if(in_array($result['media_type'] ?? '', ['movie', 'tv']))
                <button
                    wire:click="selectResult({{ $result['id'] }}, '{{ $result['media_type'] }}')"
                    class="w-full flex items-center gap-3 bg-slate-800 rounded-lg p-3 text-left hover:bg-slate-700 transition-colors"
                >
                    @if($result['poster_path'] ?? null)
                    <img src="https://image.tmdb.org/t/p/w92{{ $result['poster_path'] }}" class="w-10 h-14 object-cover rounded" alt="">
                    @else
                    <div class="w-10 h-14 bg-slate-700 rounded flex items-center justify-center text-slate-500 text-lg">🎬</div>
                    @endif
                    <div>
                        <p class="text-white text-sm font-medium">{{ $result['title'] ?? $result['name'] ?? '' }}</p>
                        <p class="text-slate-500 text-xs">{{ substr($result['release_date'] ?? $result['first_air_date'] ?? '', 0, 4) }} · {{ ($result['media_type'] ?? '') === 'tv' ? 'Série' : 'Film' }}</p>
                    </div>
                </button>
                @endif
            @endforeach
        </div>

        @else
        <!-- Add form step -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-5">
            <!-- Movie header -->
            <div class="flex gap-3">
                @if($selectedMovie['poster_path'] ?? null)
                <img src="https://image.tmdb.org/t/p/w154{{ $selectedMovie['poster_path'] }}" class="w-16 h-24 object-cover rounded-lg" alt="">
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-semibold">{{ $selectedMovie['title'] ?? $selectedMovie['name'] }}</h3>
                    <p class="text-slate-400 text-sm mt-1 line-clamp-3">{{ $selectedMovie['overview'] ?? '' }}</p>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Statut</label>
                <div class="flex gap-2">
                    <button wire:click="$set('status', 'to_watch')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'to_watch' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        À voir
                    </button>
                    <button wire:click="$set('status', 'watched')"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'watched' ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        Vu ✓
                    </button>
                </div>
            </div>

            @if($status === 'watched')
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Date de visionnage</label>
                <input wire:model="watchedAt" type="date"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none">
            </div>

            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Note ({{ $rating ?? '—' }}/10)</label>
                <input wire:model="rating" type="range" min="1" max="10" class="w-full accent-red-500">
            </div>

            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Commentaire</label>
                <textarea wire:model="comment" rows="2"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:border-red-500 focus:outline-none resize-none"
                    placeholder="Ton avis…"></textarea>
            </div>

            @if($coWatchers->count())
            <div>
                <label class="text-slate-400 text-xs uppercase tracking-widest block mb-2">Avec qui ?</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($coWatchers as $cw)
                    <button wire:click="$toggle('selectedCoWatcherIds', {{ $cw->id }})"
                        class="px-3 py-1 rounded-full text-sm transition-colors {{ in_array($cw->id, $selectedCoWatcherIds) ? 'bg-red-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ $cw->name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            <button wire:click="addToWatchlist"
                class="w-full py-3 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition-colors">
                Ajouter à ma liste
            </button>
        </div>
        @endif
    </div>
</div>
