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

mix.js('resources/js/app.js', 'public/js').sourceMaps()
    .js('resources/js/admin/admin.js', 'public/js/admin').sourceMaps()
    .js('resources/js/www/cognichart.com.js', 'public/js/www').sourceMaps()
    .sass('resources/sass/app.scss', 'public/css').sourceMaps()
    .sass('resources/sass/admin/admin.scss', 'public/css/admin').sourceMaps()
    .sass('resources/sass/www/cognichart.com.scss', 'public/css/www').sourceMaps()
    .copyDirectory('resources/ico', 'public/ico')
    .copyDirectory('resources/jpg', 'public/jpg')
    .copyDirectory('resources/png', 'public/png')
    .copyDirectory('resources/svg', 'public/svg');
