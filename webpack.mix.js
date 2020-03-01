const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

mix.js([
    // 'resources/js/front/jquery.flexslider.js',
    // 'resources/js/front/jquery.min.js',
    // 'resources/js/front/main.js',
    // 'resources/js/front/memenu.js',
    // 'resources/js/front/responsiveslides.min.js',
    // 'resources/js/front/simpleCart.min.js',
    'resources/js/front.js',
], 'public/js/front.js');

mix.styles([
    'node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
], 'public/css/style.css');

mix.styles([
    'resources/css/style.css',
    'resources/css/bootstrap.css',
    'resources/css/flexslider.css',
    'resources/css/memenu.css',
], 'public/css/front.css');

mix.autoload({
    'moment': ['moment','window.moment']
});
