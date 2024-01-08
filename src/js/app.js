import '/src/css/app.css'
import '/src/js/_init.js'
import '/src/js/accessibility.js'
import '/src/js/accordion.js'
import '/src/js/lightgallery.js'
import '/src/js/archive.js'
import '/src/js/navigation.js'
import '/src/js/gtmevents.js'
import '/src/js/modal.js'
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

window.Alpine = Alpine

Alpine.plugin(collapse)
Alpine.start()

// on page ready
document.addEventListener('DOMContentLoaded', () => {
  // add js class to body
  document.body.classList.add('js');
  // look for an element with the id of js-photoGallery
  // if it exists, run the lightGallery function on it
    if (document.getElementById('js-photoGallery')) {
      lightGallery(document.querySelector('#js-photoGallery'));
    }

    if (document.getElementById('js-map')) {
      lightGallery(document.querySelector('#js-map'));
    }
});

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
