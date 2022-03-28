import legacy from '@vitejs/plugin-legacy'
import ViteRestart from 'vite-plugin-restart';
import critical from 'rollup-plugin-critical';

// https://vitejs.dev/config/
export default ({ command }) => ({
  base: command === 'serve' ? '' : '/dist/',
  build: {
    emptyOutDir: true,
    manifest: true,
    outDir: './web/dist/',
    rollupOptions: {
      input: {
        app: 'src/js/app.js',
        algoliafilter: 'src/js/algoliafilter.js',
        search: 'src/js/search.js',
        lightgallery: 'src/js/lightgallery.js',
      }
    },
  },
  plugins: [
    legacy({
      targets: ['defaults', 'not IE 11']
    }),
    ViteRestart({
      reload: [
          'templates/**/*',
      ],
    }),
    critical({
      criticalUrl: 'http://oakland.test/',
      criticalBase: './web/dist/',
      criticalPages: [
          { uri: '', template: 'index' },
          { uri: '/services', template: 'services/index' },
      ],
      criticalConfig: {
      },
  }),
],
  server: {
    origin: 'http://localhost:3000',
    host: '0.0.0.0',
  },
  publicDir: 'src/public'
});