import '/src/css/app.css'
import '/src/js/_init.js'
import '/src/js/accordion.js'
import '/src/js/navigation.js'
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

window.Alpine = Alpine

Alpine.plugin(collapse)
Alpine.start()