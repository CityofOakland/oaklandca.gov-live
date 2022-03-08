// Grabs the package.json file to use our site’s environment/values
const mix = require("laravel-mix");

// CSS Plugins
const tailwindcss = require("tailwindcss");
const hexrgba = require('postcss-hexrgba');

mix.setPublicPath('./web/dist/')
  .postCss("./src/css/app.css", "css")
  .combine(
    [
      "./src/css/lightgallery/lightgallery.css", 
      "./src/css/lightgallery/lg-fb-comment-box.css", 
      "./src/css/lightgallery/lg-transitions.css"
    ], 
    "./web/dist/css/lightgallery.css"
  )
  .options({
    postCss: [
      tailwindcss(),
      hexrgba
    ],
    processCssUrls: false,
  })
  .js("./src/js/app.js", "js")
  .js("./src/js/algoliafilter.js", "js")
  .js("./src/js/search.js", "js")
  .js("./src/js/lightgallery.js", "js")
  .extract(["alpinejs", "picturefill"])
  .copy('./src/img', './web/dist/img')
  .copy('./src/fonts', './web/dist/fonts')
  .sourceMaps()
  .browserSync({
    proxy: process.env.MIX_LOCAL_SITE_URL,
    files: ["templates/**/*.*"],
    notify: {
      styles: {
        top: 'auto',
        bottom: '1rem'
      }
    },
  });

mix.disableSuccessNotifications();

mix.webpackConfig({
  stats: {
    hash: true,
    version: true,
    timings: true,
    children: true,
    errors: true,
    errorDetails: true,
    warnings: true,
    chunks: true,
    modules: false,
    reasons: true,
    source: true,
    publicPath: true,
  }
});

if (mix.inProduction()) {
  mix.version();
}
