let mix = require('laravel-mix');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const source = 'platform/plugins/' + directory;
const dist = 'public/vendor/core/plugins/' + directory;

mix.js(source + '/resources/assets/js/popup-ads.js', dist + '/js');
mix.sass(source + '/resources/assets/sass/popup-ads.scss', dist + '/css');

if (mix.inProduction()) {
    mix.copy(dist + '/js/popup-ads.js', source + '/public/js')
        .copy(dist + '/css/popup-ads.css', source + '/public/css');
}
