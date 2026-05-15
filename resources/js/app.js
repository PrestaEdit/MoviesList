import './bootstrap';
import 'preline';
import Alpine from 'alpinejs'
import Persist from '@alpinejs/persist'
Alpine.plugin(Persist)
window.Alpine = Alpine
Alpine.start()
