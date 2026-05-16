import './bootstrap';
import 'preline';
import Persist from '@alpinejs/persist'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Persist)
})

function applyTheme() {
    const t = localStorage.getItem('movieslist_theme') || 'dark';
    if (t === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

// Re-apply after wire:navigate page swaps
document.addEventListener('livewire:navigated', applyTheme);

window.addEventListener('theme-changed', (e) => {
    const theme = e.detail.theme;
    localStorage.setItem('movieslist_theme', theme);
    applyTheme();
});
