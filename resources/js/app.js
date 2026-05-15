import './bootstrap';
import 'preline';
import Persist from '@alpinejs/persist'

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Persist)
})
