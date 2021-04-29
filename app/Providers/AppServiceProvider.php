<?php

namespace App\Providers;

use App\ModifiableCart;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		Schema::defaultStringLength(191);
		\URL::forceScheme('https');
		$this->app['request']->server->set('HTTPS','on');
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
//		$this->app->bind('cart_modifiable', ModifiableCart::class);

        $this->app->singleton(ExchangeService::class, function()
        {
            return new ExchangeService();
        });
	}
}
