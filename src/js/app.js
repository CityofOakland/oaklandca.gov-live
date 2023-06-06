import '/src/css/app.css'
import '/src/js/_init.js'
import '/src/js/accessibility.js'
import '/src/js/accordion.js'
import '/src/js/archive.js'
import '/src/js/navigation.js'
import '/src/js/gtmevents.js'
import '/src/js/modal.js'
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

window.Alpine = Alpine

Alpine.plugin(collapse)
Alpine.start()

// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log("HMR")
  });
}
