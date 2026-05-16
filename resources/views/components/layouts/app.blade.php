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
<body class="h-full bg-slate-50 dark:bg-slate-900 font-sans antialiased" style="padding-top: var(--safe-top);">
    {{ $slot }}
    @livewireScripts
    <script>document.body.style.overscrollBehavior = 'none';</script>
</body>
</html>
