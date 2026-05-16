import './bootstrap';
import 'preline';
import Persist from '@alpinejs/persist'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Persist)
})

window.addEventListener('theme-changed', (e) => {
    const theme = e.detail.theme;
    localStorage.setItem('movieslist_theme', theme);
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});
