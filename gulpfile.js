//let babel = require('gulp-babel');

var elixir = require('laravel-elixir');

var originalInProduction = Elixir.inProduction;
//Elixir.inProduction = true;



/**
 * disable notifications.
 * Comment logging
 * in file laravel-elixir/Task.js
 * in Task.prototype.log method
 * after elixir update
 */
process.env.DISABLE_NOTIFIER = true;

elixir.config.css.sass.folder = '';
elixir.config.css.autoprefix.options.browsers = ['> 1%', 'Firefox >= 15', 'Opera > 11', 'ie > 8', 'last 2 versions'];
elixir.config.js.folder = '';
elixir.config.notifications = false;

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
elixir(function(mix) {


	mix.scripts([
		//libs
		'js/lib/angular/angular.js',
		'js/lib/angular/**/*.js',
		'js/lib/ui/**/*.module.js',
		'js/lib/ui/**/*.js',
		'js/lib/jquery-ui.js',
		'js/lib/jquery-ui.datepicker.js',
		'js/lib/*.js'
	], 'public/cms-assets/cms-lib.js');


	mix.scripts([
		'js/cms/cms.module.js',
		'js/cms/**/*.js',
	], 'public/cms-assets/cms.js');

	mix.sass([
		'sass/cms/**/*.scss',
	], 'public/cms-assets/cms.css');

	mix.version([
		'public/cms-assets/cms-lib.js',
		'public/cms-assets/cms.js',
		'public/cms-assets/cms.css',
	]);



});
