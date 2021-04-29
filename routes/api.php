<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api', 'admin'],], function () {

    // Planning
    Route::group(['prefix' => 'planning'], function () {
        Route::get('/', 'Admin\PlanningController@index');
        Route::get('/delivery', 'Admin\PlanningController@delivery');
        Route::get('/day/{date}', 'Admin\PlanningController@day');
        Route::post('/save', 'Admin\PlanningController@save');
        Route::get('/move/{from}/{to}', 'Admin\PlanningController@move');
        Route::get('/cancel/{date}', 'Admin\PlanningController@cancel');
        Route::get('/status/{id}/{status}', 'Admin\PlanningController@status');
    });


    // Order
    Route::post('/orders/{date}', 'Admin\OrderController@index');
    Route::get('/orders/get-total-info/{date}', 'Admin\OrderController@getTotalInfo');
    Route::get('/orders/{date}/email', 'Admin\OrderController@email');
    Route::post('/orders/update-product', 'Admin\OrderController@updateProduct');
    Route::post('/orders/delete', 'Admin\OrderController@delete');
    Route::post('/orders/update', 'Admin\OrderController@update');
    Route::post('/orders/delete-product', 'Admin\OrderController@deleteProduct');
    Route::post('/orders/add-product', 'Admin\OrderController@addProduct');

    // Users
    Route::get('/users', 'Admin\UserController@index');

    // Discounts
    Route::get('/discounts', 'Admin\DiscountController@lists');
    Route::post('/discounts', 'Admin\DiscountController@store');
    Route::get('/discounts/remove/{id}', 'Admin\DiscountController@destroy');
    Route::get('/discounts/add', 'Admin\DiscountController@add');

    // Charges
    Route::get('/charges', 'Admin\ChargesController@lists');
    Route::post('/charges', 'Admin\ChargesController@store');
    Route::get('/charges/remove/{id}', 'Admin\ChargesController@destroy');
    Route::get('/charges/add', 'Admin\ChargesController@add');

    // Category
    Route::get('/categories', 'Admin\CategoryController@lists');
    Route::get('/sub_categories', 'Admin\CategoryController@subcategories');
    Route::get('/categories/set/discount', 'Admin\CategoryController@discount');
    Route::post('/categories/change-visible', 'Admin\CategoryController@changeVisible');
    Route::post('/subcategories/change-visible', 'Admin\CategoryController@changeVisibleSubcategories');

    // Product
    Route::get('/products', 'Admin\ProductController@lists');
    Route::post('/products', 'Admin\ProductController@store');
    Route::get('/products/remove/{id}', 'Admin\ProductController@destroy');
    Route::get('/products/set/fixed', 'Admin\ProductController@fixed');
    Route::post('/products/{products}/set-in-main', 'Admin\ProductController@addToShowProduct');
    Route::post('/products/delete-from-main', 'Admin\ProductController@removeFromShowProduct');
    Route::get('/products/get-main', 'Admin\ProductController@getMain');
    Route::get('/products/search', 'Admin\ProductController@search');
    Route::post('/products/show/order', 'Admin\ProductController@changeOrderDisplayForProduct');

    // Product Visible
    Route::get('/products/visibles', 'Admin\ProductController@visibleProductWithCategoryLists');
    Route::put('/products/toggle-visible', 'Admin\ProductController@toggleVisibleProductWithCategory');

    // Parser
    Route::post('/parser/start', 'Admin\ParserController@start');
	Route::get('/stop-parser', 'Admin\ParserController@stopParsing');

    // Pages
    Route::get('/pages', 'Admin\PageController@index');
    Route::post('/pages', 'Admin\PageController@store');

    // Settings
    Route::get('/settings', 'Admin\SettingController@index');
    Route::post('/settings', 'Admin\SettingController@store');

	Route::group(['prefix' => 'promo'], function(){
		Route::get('list', 		'Admin\PromoController@getList');
		Route::post('add', 		'Admin\PromoController@addCode');
		Route::post('delete', 	'Admin\PromoController@deleteCode');
	});
});

