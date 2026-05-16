<div class="min-h-screen bg-slate-900">
    <!-- Header -->
    <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-3">
        <a href="/" wire:navigate class="text-slate-400 hover:text-white">←</a>
        <h1 class="text-white font-semibold">Paramètres</h1>
    </div>

    <div class="px-4 py-6 space-y-8">
        <!-- Profil -->
        <section>
            <div class="bg-slate-800 rounded-xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-white text-lg font-bold">
                    {{ strtoupper(substr($this->name ?: 'M', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <input
                        wire:model="name"
                        wire:blur="updateName"
                        type="text"
                        value="{{ $name }}"
                        class="bg-transparent text-white font-medium w-full focus:outline-none border-b border-transparent focus:border-red-500 transition-colors"
                    >
                    <p class="text-slate-500 text-xs mt-1">Appuyer pour modifier</p>
                </div>
            </div>
        </section>

        <!-- Co-watchers -->
        <section>
            <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">Co-watchers</h2>
            <div class="space-y-2">
                @foreach($coWatchers as $cw)
                <div class="bg-slate-800 rounded-lg px-4 py-3 flex justify-between items-center">
                    <span class="text-white text-sm">{{ $cw->name }}</span>
                    <button wire:click="deleteCoWatcher({{ $cw->id }})" class="text-red-500 hover:text-red-400 text-lg leading-none">✕</button>
                </div>
                @endforeach

                <!-- Add form -->
                <form wire:submit="addCoWatcher" class="flex gap-2">
                    <input
                        wire:model="newCoWatcherName"
                        type="text"
                        placeholder="Nouveau co-watcher…"
                        class="flex-1 bg-slate-800 border border-dashed border-slate-600 rounded-lg px-4 py-3 text-white text-sm placeholder-slate-500 focus:border-red-500 focus:outline-none"
                    >
                    <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-4 rounded-lg text-sm font-medium transition-colors">+</button>
                </form>
                @error('newCoWatcherName')
                <p class="text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <!-- Thème -->
        <section>
            <h2 class="text-slate-400 text-xs uppercase tracking-widest mb-3">Thème</h2>
            <div class="flex gap-2">
                <button
                    wire:click="setTheme('dark')"
                    class="flex-1 py-3 rounded-xl text-sm font-medium transition-colors border {{ $theme === 'dark' ? 'bg-slate-700 border-red-500 text-white' : 'bg-slate-800 border-slate-600 text-slate-400' }}"
                >
                    🌙 Sombre
                </button>
                <button
                    wire:click="setTheme('light')"
                    class="flex-1 py-3 rounded-xl text-sm font-medium transition-colors border {{ $theme === 'light' ? 'bg-slate-200 border-red-500 text-slate-900' : 'bg-slate-800 border-slate-600 text-slate-400' }}"
                >
                    ☀️ Clair
                </button>
            </div>
        </section>
    </div>
</div>
