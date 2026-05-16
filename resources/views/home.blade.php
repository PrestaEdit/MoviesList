<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <script>
        (function() {
            var t = localStorage.getItem('movieslist_theme') || 'dark';
            if (t === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>MoviesList</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>:root { --safe-top: 0px; --safe-bottom: 0px; }</style>
    <script>
        (function () {
            var probe = document.createElement('div');
            probe.style.cssText = 'position:fixed;top:0;left:0;width:0;pointer-events:none;visibility:hidden;height:env(safe-area-inset-top,0px);';
            document.documentElement.appendChild(probe);
            function apply() {
                var top = probe.getBoundingClientRect().height;
                if (top === 0) {
                    var vvOffset = (window.visualViewport && window.visualViewport.offsetTop) || 0;
                    top = vvOffset > 0 ? vvOffset : 48;
                }
                document.documentElement.style.setProperty('--safe-top', top + 'px');
                var probeBottom = document.createElement('div');
                probeBottom.style.cssText = 'position:fixed;bottom:0;left:0;width:0;pointer-events:none;visibility:hidden;height:env(safe-area-inset-bottom,0px);';
                document.documentElement.appendChild(probeBottom);
                var bottom = probeBottom.getBoundingClientRect().height;
                document.documentElement.removeChild(probeBottom);
                document.documentElement.style.setProperty('--safe-bottom', bottom + 'px');
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', apply);
            } else {
                apply();
            }
        })();
    </script>
</head>
<body class="h-full bg-slate-50 dark:bg-slate-900 font-sans antialiased" x-data="{ activeTab: $persist('dashboard') }">

    <main class="min-h-full pb-20" style="padding-top: var(--safe-top);">
        <div x-show="activeTab === 'dashboard'">
            <livewire:dashboard />
        </div>
        <div x-show="activeTab === 'movies'">
            <livewire:movie-list />
        </div>
    </main>

    <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 z-50" style="padding-bottom: var(--safe-bottom);">
        <div class="flex justify-around items-center h-16 max-w-lg mx-auto px-4">
            <button @click="activeTab = 'dashboard'"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-xl transition-colors"
                :class="activeTab === 'dashboard' ? 'text-sky-500' : 'text-slate-400 dark:text-slate-500'">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span class="text-xs font-medium">Accueil</span>
            </button>
            <button @click="activeTab = 'movies'"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-xl transition-colors"
                :class="activeTab === 'movies' ? 'text-sky-500' : 'text-slate-400 dark:text-slate-500'">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
                <span class="text-xs font-medium">Mes films</span>
            </button>
            <a href="/settings" wire:navigate
               class="flex flex-col items-center gap-1 px-3 py-2 rounded-xl transition-colors text-slate-400 dark:text-slate-500 hover:text-sky-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs font-medium">Réglages</span>
            </a>
        </div>
    </nav>

    @livewireScripts
    <script>document.body.style.overscrollBehavior = 'none';</script>
</body>
</html>
