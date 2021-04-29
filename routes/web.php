<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('socialite')->group(function(){
	Route::post('redirect', 'SocialAuthController@redirect');
	Route::get('facebook', 'SocialAuthController@facebook');
	Route::get('google', 'SocialAuthController@google');
	Route::get('vkontakte', 'SocialAuthController@vkontakte');
});

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/', 'CatalogController@home');

if(!App::isProduction()) {
	Route::get('/test', 'TestController@test');
}

Route::get('/clear-table', 'TestController@clearTable');

Route::match(['get', 'post'], '/callback', 'OrdersController@callbackRequest');

Route::any('/deploy', 'DeployController@deploy');

/*страница продукта*/
Route::get('/catalog/product/{vendor}', "CatalogController@product")->name('product');

/* категории */
Route::get('/catalog', 'CatalogController@catalog');
Route::get('/catalog/{category}', 'CatalogController@categoryOrSubcategory');
Route::get('/catalog/{category}/{subcategory}', 'CatalogController@categoryOrSubcategory');
//Route::get('/catalog/{category}', 'CatalogController@category');
Route::get('/available', 'CatalogController@available')->name('available');
Route::get('/new', 'CatalogController@showNew')->name('new');
Route::get('/activate', 'Auth\RegisterController@activate');

/* заказ */
Route::get('/order', 'OrdersController@order')->name('order');

Route::get('/order_email', 'OrdersController@renderEmail');

/* профиль */
Route::group(['middleware' => 'auth'], function () {
	Route::get('/account', 'AccountController@index');
	Route::post('/account', 'AccountController@save')->name('account');
});

Route::get('/check', 'CatalogController@showCheck')->name('check');

/* инфо страинцы */
Route::get('/info', "PagesController@info");
Route::get('/{alias}', "PagesController@page")->where('alias', 'payment|guarantee|faq');
Route::get('/contacts', 'PagesController@contacts')->name('contacts');
Route::get('/deliverance', 'PagesController@deliverance')->name('deliverance');


/*ajax*/
Route::get('/cart', 'OrdersController@getCartContent');
Route::get('/cart/cities', 'OrdersController@getCities');
Route::match(['get', 'post'],'/cart/add', 'OrdersController@addToCard');
Route::match(['get', 'post'],'/cart/change_qty', 'OrdersController@changeQty');
Route::match(['get', 'post'],'/cart/remove_item', 'OrdersController@removeItemFromCart');
Route::get('/cart/clear', 'OrdersController@clearCart');

Route::post('/cart/order', 'OrdersController@create');
Route::post('/cart/get_discount', 'OrdersController@getDiscount');

Route::post('/order_request', 'OrdersController@sendOrderRequest');

Route::get('/search', 'CatalogController@search');
Route::get('/get-discount', 'OrdersController@getDiscount')->name('get-discount');
Route::post('/contact-message', 'PagesController@message')->name('contact-message');

Route::group(['middleware' => ['auth', 'admin',],], function () {

//	Route::get('/admin', 'AdminController@index')->where('any', '.*');
	Route::group(['prefix' => 'admin'], function () {
		// Vue router
		Route::get('/', 'AdminController@index');
		Route::get('/error', 'AdminController@index');
		Route::get('/orders', 'AdminController@index');
		Route::get('/users', 'AdminController@index');
		Route::get('/information', 'AdminController@index');
		Route::get('/catalog', 'AdminController@index');
		Route::get('/promo', 'AdminController@index');
		Route::get('/mainpage', 'AdminController@index');

		Route::get('/shedule', 'AdminController@shedule')->name('shedule');
		Route::get('/customers', 'AdminController@customers')->name('customers');
		Route::get('/products', 'AdminController@products')->name('products');
		Route::get('/info', 'AdminController@info')->name('info');

        Route::post('/db/reset', 'DbController@resetProducts')->name('db.resetProducts');

		Route::group(['prefix' => 'slides'], function () {
			Route::post('list', 'MainPageSlidesController@list');
			Route::post('upload', 'MainPageSlidesController@upload');
			Route::post('delete', 'MainPageSlidesController@delete');
			Route::post('move', 'MainPageSlidesController@move');
		});

		Route::group(['prefix' => 'pluses'], function () {
			Route::get('list', 'MainPagePlusesController@list');
			Route::post('save', 'MainPagePlusesController@save');
			Route::post('delete', 'MainPagePlusesController@delete');
			Route::post('move', 'MainPagePlusesController@move');
		});

		Route::group(['prefix' => 'warning'], function () {
			Route::get('get', 'MainPageWarningController@get');
			Route::post('save', 'MainPageWarningController@save');
		});

	});


	Route::match(['get', 'post'],'/parser/start', 'Admin\ParserController@start');
	Route::get('/status-parser', 'Admin\ParserController@status');
	Route::match(['get', 'post'],'/parser/categories', 'Admin\ParserController@parseCategories');
	Route::get('/stop-parser', 'Admin\ParserController@stopParsing');

	Route::get('/delq', 'Admin\ProductController@collectOrdersData');
	Route::post('/exchange', "AjaxController@saveExchange");
	Route::get('/exchange', "AjaxController@getExchange");
});

Route::post('/callback','CallbackContrtoller@send');


/** Cms */
Route::group([
	'prefix' => 'cms',
	'middleware' => ['web', 'auth', 'admin'],
], function () {
	Route::get('/', function () {
		return view(
			'cms.index', [
				'angularModule' => 'app.cms',
				'class' => 'container-fluid',
				'title' => 'Блог'
			]
		);
	})->name('cms');

	Route::get('sitemap', 'Cms\PagesController@buildSitemap');

	Route::post('page/available', 		'Cms\PagesController@aliasIsAvailable');
	Route::post('directory/available', 	'Cms\DirectoriesController@aliasIsAvailable');
	Route::get('tree/root', 			'Cms\DirectoriesController@rootDir');
	Route::get('tree/{directory}', 		'Cms\DirectoriesController@getTree');
	Route::post('tree/search', 			'Cms\DirectoriesController@searchWithinTree');

	Route::post('directory/find',				'Cms\DirectoriesController@findDirectory');
	Route::post('page/find',					'Cms\PagesController@findPage');
	Route::post('page/get-translation',			'Cms\PagesController@getTranslation');
	Route::post('page/save-translation',		'Cms\PagesController@saveTranslation');
	Route::post('page/create-promo', 			'Cms\PagesController@createPromo');
	Route::post('page/create-special-offer', 	'Cms\PagesController@createSpecialOffer');

	Route::resource('page', 'Cms\PagesController', [
		'except' => [
			'index',
			'create',
			'destroy'
		]
	]);

	Route::resource('directory', 'Cms\DirectoriesController', [
		'except' => [
			'index',
			'create',
			'destroy'
		]
	]);

	Route::group(['middleware' => 'bindings'], function () {
		Route::get('/redirects/list','Cms\PagesRedirectsController@list');

		Route::resource('redirects', 'Cms\PagesRedirectsController', [
			'except' => [
				'create',
			]
		])->names(['index' => 'redirects']);

	});

});

/*CMS Fall back routes - should be placed last*/

//Redirect /dir/index pages to /dir
Route::get('{path}/index', function ($path) {
	return Redirect::to($path, 301);
})->where('slug', '.+')->middleware(['web']);

//Page Routes (catch-all fallback to cms)
Route::get('{slug}', [
	'as'   => 'path',
	'uses' => 'Cms\PagesController@renderPage',
])->where('slug', '.+')->middleware(['web']);
