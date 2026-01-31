import './bootstrap';

import Alpine from 'alpinejs';
if (!window.location.pathname.startsWith('/checkout')) {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/serviceworker.js');
  }
}
window.Alpine = Alpine;

Alpine.start();
