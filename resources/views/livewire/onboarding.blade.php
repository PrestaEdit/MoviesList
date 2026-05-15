<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm text-center space-y-6">
        <div class="text-5xl">🎬</div>
        <h1 class="text-2xl font-bold text-white">Bienvenue sur MoviesList</h1>
        <p class="text-slate-400">Comment tu t'appelles ?</p>
        <form wire:submit="save" class="space-y-4">
            <input
                wire:model="name"
                type="text"
                placeholder="Ton prénom…"
                class="py-3 px-4 block w-full bg-slate-800 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:ring-red-500 focus:border-red-500"
                autofocus
            >
            @error('name')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
            <button type="submit" class="w-full py-3 px-4 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition-colors">
                C'est parti →
            </button>
        </form>
    </div>
</div>
