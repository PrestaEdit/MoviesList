<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesList</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-900 min-h-screen" x-data="{ activeTab: $persist('dashboard') }">

    <!-- Header -->
    <header class="sticky top-0 z-10 bg-slate-900 border-b border-slate-800 px-4 py-3 flex justify-between items-center">
        <p class="text-white font-semibold text-sm">
            Bonsoir, {{ \App\Models\Profile::first()?->name ?? '' }} 👋
        </p>
        <a href="/settings" wire:navigate>
            <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr(\App\Models\Profile::first()?->name ?? 'M', 0, 1)) }}
            </div>
        </a>
    </header>

    <!-- Tabs -->
    <nav class="border-b border-slate-800 flex">
        <button
            @click="activeTab = 'dashboard'"
            :class="activeTab === 'dashboard' ? 'border-b-2 border-red-500 text-red-500' : 'text-slate-500'"
            class="flex-1 py-3 text-sm font-medium transition-colors"
        >Accueil</button>
        <button
            @click="activeTab = 'movies'"
            :class="activeTab === 'movies' ? 'border-b-2 border-red-500 text-red-500' : 'text-slate-500'"
            class="flex-1 py-3 text-sm font-medium transition-colors"
        >Mes films</button>
    </nav>

    <!-- Content -->
    <main>
        <div x-show="activeTab === 'dashboard'">
            <livewire:dashboard />
        </div>
        <div x-show="activeTab === 'movies'">
            <livewire:movie-list />
        </div>
    </main>

    @livewireScripts
</body>
</html>
