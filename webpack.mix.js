let mix = require('laravel-mix');

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

mix.disableNotifications();

mix.options({
	processCssUrls: false,
});

if ( ! mix.inProduction()) {
	mix.webpackConfig({
		devtool: 'inline-source-map'
	})
}

mix
    .js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/admin/app.js', 'public/js/admin')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/admin/app.scss', 'public/css/admin')
    .version();
