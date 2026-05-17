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
    <style>:root { --safe-top: env(safe-area-inset-top, 0px); --safe-bottom: env(safe-area-inset-bottom, 0px); }</style>
</head>
<body class="h-full bg-slate-50 dark:bg-slate-900 font-sans antialiased" style="padding-top: var(--safe-top);">
    {{ $slot }}
    @livewireScripts
    <script>document.body.style.overscrollBehavior = 'none';</script>
</body>
</html>
