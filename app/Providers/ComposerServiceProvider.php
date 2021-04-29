<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\LatestCmsPagesComposer;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['cms.blocks.latestPages'], LatestCmsPagesComposer::class);
    }
}
