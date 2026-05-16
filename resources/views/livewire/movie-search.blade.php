<div>
    {{-- FAB - positioned above bottom nav --}}
    <button
        wire:click="$set('open', true)"
        class="fixed bottom-24 right-5 z-20 w-14 h-14 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg transition-colors"
        style="background: linear-gradient(135deg, #0ea5e9, #6366f1);"
    >+</button>

    {{-- Modal --}}
    <div x-show="$wire.open" class="fixed inset-0 z-30 bg-slate-900/80 backdrop-blur-sm flex flex-col" x-transition>
        <div class="flex flex-col bg-white dark:bg-slate-900 h-full" style="padding-top: var(--safe-top, 0px);">
            {{-- Header --}}
            <div class="px-4 py-4 flex items-center gap-3 border-b border-slate-200 dark:border-slate-800">
                <button wire:click="$set('open', false)"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    ✕
                </button>
                <h2 class="text-slate-900 dark:text-white font-extrabold">Ajouter un titre</h2>
            </div>

            @if(!$selectedMovie)
            {{-- Search step --}}
            <div class="px-4 py-4">
                <input
                    wire:model.live.debounce.300ms="query"
                    type="text"
                    placeholder="Rechercher un film ou une série…"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 focus:border-sky-500 focus:outline-none"
                    autofocus
                >
            </div>
            <div class="flex-1 overflow-y-auto px-4 space-y-2 pb-6">
                @foreach($results as $result)
                    @if(in_array($result['media_type'] ?? '', ['movie', 'tv']))
                    <button
                        wire:click="selectResult({{ $result['id'] }}, '{{ $result['media_type'] }}')"
                        class="w-full flex items-center gap-3 bg-slate-50 dark:bg-slate-800 rounded-2xl p-3 text-left hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                    >
                        @if($result['poster_path'] ?? null)
                        <img src="https://image.tmdb.org/t/p/w92{{ $result['poster_path'] }}" class="w-10 h-14 object-cover rounded-xl" alt="">
                        @else
                        <div class="w-10 h-14 bg-slate-200 dark:bg-slate-700 rounded-xl flex items-center justify-center text-slate-400 text-lg">🎬</div>
                        @endif
                        <div>
                            <p class="text-slate-900 dark:text-white text-sm font-medium">{{ $result['title'] ?? $result['name'] ?? '' }}</p>
                            <p class="text-slate-400 text-xs mt-0.5">{{ substr($result['release_date'] ?? $result['first_air_date'] ?? '', 0, 4) }} · {{ ($result['media_type'] ?? '') === 'tv' ? 'Série' : 'Film' }}</p>
                        </div>
                    </button>
                    @endif
                @endforeach
            </div>

            @else
            {{-- Add form step --}}
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-5 pb-6">
                {{-- Movie header --}}
                <div class="flex gap-3">
                    @if($selectedMovie['poster_path'] ?? null)
                    <img src="https://image.tmdb.org/t/p/w154{{ $selectedMovie['poster_path'] }}" class="w-16 h-24 object-cover rounded-2xl shadow-sm" alt="">
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="text-slate-900 dark:text-white font-extrabold">{{ $selectedMovie['title'] ?? $selectedMovie['name'] }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 line-clamp-3">{{ $selectedMovie['overview'] ?? '' }}</p>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Statut</p>
                    <div class="flex gap-2">
                        <button wire:click="$set('status', 'to_watch')"
                            class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $status === 'to_watch' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                            À voir
                        </button>
                        <button wire:click="$set('status', 'watched')"
                            class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $status === 'watched' ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                            Vu ✓
                        </button>
                    </div>
                </div>

                @if($status === 'watched')
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Date de visionnage</p>
                    <input wire:model="watchedAt" type="date"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:border-sky-500 focus:outline-none">
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Note ({{ $rating ?? '—' }}/10)</p>
                    <input wire:model="rating" type="range" min="1" max="10" class="w-full accent-sky-500">
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Commentaire</p>
                    <textarea wire:model="comment" rows="2"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white placeholder-slate-400 focus:border-sky-500 focus:outline-none resize-none"
                        placeholder="Ton avis…"></textarea>
                </div>

                @if($coWatchers->count())
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Avec qui ?</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($coWatchers as $cw)
                        <button wire:click="$toggle('selectedCoWatcherIds', {{ $cw->id }})"
                            class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ in_array($cw->id, $selectedCoWatcherIds) ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                            {{ $cw->name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
                @endif

                <button wire:click="addToWatchlist"
                    class="w-full py-3 text-white font-semibold rounded-xl transition-colors text-sm"
                    style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                    Ajouter à ma liste
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
