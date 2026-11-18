<?php

namespace App\Providers;

use App\Models\Beerwall;
use App\Observers\BeerWallObserver;
use App\Queries\BeerwallQueries;
use App\Queries\Contracts\BeerwallQueriesInterface;
use App\Queries\Contracts\GoPosItemsQueriesInterface;
use App\Queries\Contracts\KegInventoryQueriesInterface;
use App\Queries\Contracts\KegMovementQueriesInterface;
use App\Queries\Contracts\KegStockQueriesInterface;
use App\Queries\Contracts\MaltStockQueriesInterface;
use App\Queries\Contracts\RecipeBatchesQueriesInterface;
use App\Queries\Contracts\RecipeQueriesInterface;
use App\Queries\Contracts\SocialPostQueriesInterface;
use App\Queries\GoPosItemsQueries;
use App\Queries\KegInventoryQueries;
use App\Queries\KegMovementQueries;
use App\Queries\KegStockQueries;
use App\Queries\MaltStockQueries;
use App\Queries\RecipeBatchesQueries;
use App\Queries\RecipeQueries;
use App\Queries\SocialPostQueries;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BeerwallQueriesInterface::class, BeerwallQueries::class);
        $this->app->bind(GoPosItemsQueriesInterface::class, GoPosItemsQueries::class);
        $this->app->bind(KegInventoryQueriesInterface::class, KegInventoryQueries::class);
        $this->app->bind(KegMovementQueriesInterface::class, KegMovementQueries::class);
        $this->app->bind(KegStockQueriesInterface::class, KegStockQueries::class);
        $this->app->bind(MaltStockQueriesInterface::class, MaltStockQueries::class);
        $this->app->bind(RecipeQueriesInterface::class, RecipeQueries::class);
        $this->app->bind(RecipeBatchesQueriesInterface::class, RecipeBatchesQueries::class);
        $this->app->bind(SocialPostQueriesInterface::class, SocialPostQueries::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Beerwall::observe(BeerWallObserver::class);
    }
}
