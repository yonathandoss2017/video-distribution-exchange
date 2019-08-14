const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/assets/js/components/marketplace/search.js', 'public/js/marketplace/search.js')
    .js('resources/assets/js/components/marketplace/video.js', 'public/js/marketplace/video.js')
    .js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/distribution-playlist.js', 'public/js')
    .js('resources/assets/js/distribution-sp.js', 'public/js')
    .js('resources/assets/js/marketplace/main.js', 'public/js/marketplace/marketplace.js')
    ;

mix.webpackConfig({    
  resolve: {      
    alias: {
      '@': path.resolve(__dirname, 'resources/assets/js/marketplace')      
    },    
  },
  output: {
    chunkFilename: 'js/marketplace/[name].chunk.js'
  },  
})

if(! mix.inProduction()) {
    mix.sourceMaps();
}
