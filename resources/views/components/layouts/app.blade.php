<!DOCTYPE html>
<html lang="fr">
<head>
    <script>
        (function() {
            var t = localStorage.getItem('movieslist_theme') || 'dark';
            document.documentElement.classList.add(t);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesList</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-900 min-h-screen">
    {{ $slot }}
    @livewireScripts
    <script>
        window.addEventListener('theme-changed', (e) => {
            const theme = e.detail.theme;
            localStorage.setItem('movieslist_theme', theme);
            document.documentElement.classList.remove('dark', 'light');
            document.documentElement.classList.add(theme);
        });
    </script>
</body>
</html>
