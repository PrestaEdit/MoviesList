<div class="min-h-screen bg-slate-50 dark:bg-slate-900" style="padding-bottom: var(--safe-bottom, 0px);">
    {{-- Header --}}
    <div class="px-4 py-4 flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <a href="/" wire:navigate class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">‹</a>
        <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">Réglages</h1>
    </div>

    <div class="p-4 max-w-lg mx-auto space-y-4">

        {{-- Profil --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Profil</p>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-lg font-extrabold shrink-0"
                     style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                    {{ strtoupper(substr($this->name ?: 'M', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <input
                        wire:model="name"
                        wire:blur="updateName"
                        type="text"
                        value="{{ $name }}"
                        class="bg-transparent text-slate-900 dark:text-white font-semibold w-full focus:outline-none border-b border-transparent focus:border-sky-500 transition-colors"
                    >
                    <p class="text-slate-400 text-xs mt-1">Toucher pour modifier</p>
                </div>
            </div>
        </div>

        {{-- Co-watchers --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Co-watchers</p>
            <div class="space-y-2">
                @foreach($coWatchers as $cw)
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                    <span class="text-slate-900 dark:text-white text-sm font-medium">{{ $cw->name }}</span>
                    <button wire:click="deleteCoWatcher({{ $cw->id }})" class="w-7 h-7 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-400 hover:text-red-500 text-sm transition-colors">✕</button>
                </div>
                @endforeach

                <form wire:submit="addCoWatcher" class="flex gap-2 pt-2">
                    <input
                        wire:model="newCoWatcherName"
                        type="text"
                        placeholder="Ajouter un co-watcher…"
                        class="flex-1 bg-slate-50 dark:bg-slate-700 border border-dashed border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:border-sky-500 focus:outline-none"
                    >
                    <button type="submit" class="w-10 h-10 rounded-xl bg-sky-500 hover:bg-sky-400 text-white flex items-center justify-center text-xl font-light transition-colors">+</button>
                </form>
                @error('newCoWatcherName')
                <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Clé API TMDB --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Clé API TMDB</p>
            <p class="text-xs text-slate-400 mb-3">Nécessaire pour rechercher des films et séries. Obtenez votre clé sur themoviedb.org.</p>
            <form wire:submit="saveTmdbApiKey" class="space-y-2">
                <div class="flex gap-2">
                    <input
                        wire:model="tmdbApiKey"
                        type="{{ $tmdbApiKeyVisible ? 'text' : 'password' }}"
                        placeholder="Entrez votre clé API…"
                        autocomplete="off"
                        class="flex-1 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:border-sky-500 focus:outline-none font-mono"
                    >
                    <button
                        type="button"
                        wire:click="toggleTmdbApiKeyVisibility"
                        class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-sky-500 transition-colors text-base shrink-0"
                        title="{{ $tmdbApiKeyVisible ? 'Masquer' : 'Afficher' }}"
                    >{{ $tmdbApiKeyVisible ? '🙈' : '👁' }}</button>
                </div>
                @error('tmdbApiKey')
                <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-sm font-semibold transition-colors"
                >
                    @if($tmdbApiKeySaved)
                        ✓ Enregistrée
                    @else
                        Enregistrer la clé
                    @endif
                </button>
            </form>
        </div>

        {{-- Thème --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-4">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Thème</p>
            <div class="flex gap-2">
                <button
                    wire:click="setTheme('dark')"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold transition-colors border {{ $theme === 'dark' ? 'bg-sky-500 border-sky-500 text-white' : 'bg-slate-50 dark:bg-slate-700 border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400' }}"
                >
                    🌙 Sombre
                </button>
                <button
                    wire:click="setTheme('light')"
                    class="flex-1 py-3 rounded-xl text-sm font-semibold transition-colors border {{ $theme === 'light' ? 'bg-sky-500 border-sky-500 text-white' : 'bg-slate-50 dark:bg-slate-700 border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400' }}"
                >
                    ☀️ Clair
                </button>
            </div>
        </div>

        <p class="text-center text-xs text-slate-300 dark:text-slate-600 mt-2">MoviesList</p>
    </div>
</div>
