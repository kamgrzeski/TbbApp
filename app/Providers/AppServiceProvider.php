<?php

namespace App\Providers;

use App\Models\Beerwall;
use App\Observers\BeerWallObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Beerwall::observe(BeerWallObserver::class);
    }
}
