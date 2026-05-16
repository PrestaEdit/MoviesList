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
</head>
<body class="h-full bg-slate-50 dark:bg-slate-900 font-sans antialiased">
    {{ $slot }}
    @livewireScripts
</body>
</html>
