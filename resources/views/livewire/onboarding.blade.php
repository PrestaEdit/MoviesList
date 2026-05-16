<div class="min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-sm text-center space-y-8">
        <div>
            <div class="text-6xl mb-4">🎬</div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Bienvenue sur MoviesList</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Comment tu t'appelles ?</p>
        </div>
        <form wire:submit="save" class="space-y-4">
            <input
                wire:model="name"
                type="text"
                placeholder="Ton prénom…"
                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 focus:border-sky-500 focus:outline-none text-center text-lg"
                autofocus
            >
            @error('name')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
            <button type="submit" class="w-full py-3 px-4 bg-sky-500 hover:bg-sky-400 text-white font-semibold rounded-xl transition-colors text-sm">
                C'est parti →
            </button>
        </form>
    </div>
</div>
