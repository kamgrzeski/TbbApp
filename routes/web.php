<?php

use App\Http\Controllers\BeerReportController;
use App\Http\Controllers\BeerwallController;
use App\Http\Controllers\BrewingController;
use App\Http\Controllers\GoPosController;
use App\Http\Controllers\KegStockController;
use App\Http\Controllers\MaltStockController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialPostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('brewing.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/raport-piwa', [BeerReportController::class, 'index'])->name('beer.report.index');
    Route::post('/raport-piwa', [BeerReportController::class, 'upload'])->name('beer.report.upload');

    Route::get('/brewing', [BrewingController::class, 'index'])->name('brewing.index');
    Route::get('/brewing/create', [BrewingController::class, 'create'])->name('brewing.create');
    Route::get('/brewing/{recipe}', [BrewingController::class, 'show'])->name('brewing.show');
    Route::post('/brewing/{recipe}/comments', [BrewingController::class, 'storeComment'])->name('comments.store');
    Route::delete('/brewing/{recipe}', [BrewingController::class, 'destroy'])->name('brewing.destroy');
    Route::post('/brewing/{recipe}/status', [BrewingController::class, 'updateStatus'])->name('brewing.status');
    Route::post('/brewing/{recipe}/archive', [BrewingController::class, 'toggleArchive'])->name('brewing.archive');
    Route::post('/brewing/{recipe}/gravity', [BrewingController::class, 'storeGravity'])->name('gravity.store');
    Route::get('/brewing/{recipe}/edit', [BrewingController::class, 'edit'])->name('brewing.edit');
    Route::get('/brewing/{recipe}/clone', [BrewingController::class, 'clone'])->name('brewing.clone');
    Route::patch('/brewing/{recipe}', [BrewingController::class, 'update'])->name('brewing.update');
    Route::get('/brewing/{recipe}/print', [BrewingController::class, 'print'])->name('brewing.print');
    Route::get('/brewing/{recipe}/excise-email', [BrewingController::class, 'generateEmailContentExciseForRecipe'])->name('brewing.excise-email');

    Route::prefix('admin/beerwall')->group(function () {
        Route::get('/', [BeerwallController::class, 'index'])->name('beerwall.admin.index');
        Route::get('/settings/edit', [BeerwallController::class, 'editSettings'])->name('beerwall.admin.settings.edit');
        Route::post('/settings/update', [BeerwallController::class, 'updateSettings'])->name('beerwall.admin.settings.update');
        Route::get('/create', [BeerwallController::class, 'create'])->name('beerwall.admin.create');
        Route::post('/', [BeerwallController::class, 'store'])->name('beerwall.admin.store');
        Route::get('/{beerwall}/edit', [BeerwallController::class, 'edit'])->name('beerwall.admin.edit');
        Route::put('/{beerwall}', [BeerwallController::class, 'update'])->name('beerwall.admin.update');
        Route::patch('/{beerwall}/status', [BeerwallController::class, 'updateStatus'])->name('beerwall.admin.status');
        Route::delete('/{beerwall}', [BeerwallController::class, 'destroy'])->name('beerwall.admin.destroy');
        Route::post('/{beerwall}/duplicate', [BeerwallController::class, 'duplicate'])->name('beerwall.admin.duplicate');
        Route::post('/{beerwall}/clone', [BeerwallController::class, 'clone'])->name('beerwall.admin.clone');
    });

    Route::prefix('admin/kegs')
        ->group(function () {Route::get('/', [KegStockController::class, 'index'])->name('kegs.admin.index');
            Route::post('/issue', [KegStockController::class, 'issue'])->name('kegs.admin.issue.store');
            Route::get('/movements', [KegStockController::class, 'movements'])->name('kegs.admin.movements');
            Route::post('/pool', [KegStockController::class, 'pool'])->name('kegs.admin.pool.store');
            Route::post('/remove', [KegStockController::class, 'remove'])->name('kegs.admin.remove.store');
            Route::patch('/movements/{movement}', [KegStockController::class, 'updateMovement'])->name('kegs.admin.movements.update');
    });

    Route::prefix('admin/gopos')
        ->group(function () {
            Route::get('/', [GoPosController::class, 'index'])->name('gopos.admin.index');
            Route::get('/sync', [GoPosController::class, 'sync'])->name('gopos.admin.items.sync');
            Route::get('/webhooks', [GoPosController::class, 'webhooksList'])->name('gopos.admin.webhooks');
            Route::post('/webhooks/sync', [GoPosController::class, 'syncWebhooks'])->name('gopos.admin.webhooks.sync');
        });

    Route::prefix('admin/malts')
        ->group(function () {
            Route::get('/', [MaltStockController::class, 'index'])->name('malts.admin.index');
            Route::get('/create', [MaltStockController::class, 'create'])->name('malts.admin.create');
            Route::post('/', [MaltStockController::class, 'store'])->name('malts.admin.store');
            Route::get('/{maltStock}/edit', [MaltStockController::class, 'edit'])->name('malts.admin.edit');
            Route::put('/{maltStock}', [MaltStockController::class, 'update'])->name('malts.admin.update');
            Route::post('/{maltStock}/subtract-bag', [MaltStockController::class, 'subtractBag'])->name('malts.admin.subtract-bag');
            Route::delete('/{maltStock}', [MaltStockController::class, 'destroy'])->name('malts.admin.destroy');
        });

    Route::prefix('admin/social-posts')
        ->group(function () {
            Route::get('/', [SocialPostController::class, 'index'])->name('social-posts.index');
            Route::get('/create', [SocialPostController::class, 'create'])->name('social-posts.create');
            Route::post('/', [SocialPostController::class, 'store'])->name('social-posts.store');
            Route::get('/{socialPost}', [SocialPostController::class, 'show'])->name('social-posts.show');
            Route::put('/{socialPost}', [SocialPostController::class, 'update'])->name('social-posts.update');
            Route::post('/{socialPost}/regenerate', [SocialPostController::class, 'regenerate'])->name('social-posts.regenerate');
            Route::patch('/{socialPost}/status', [SocialPostController::class, 'updateStatus'])->name('social-posts.status');
            Route::delete('/{socialPost}', [SocialPostController::class, 'destroy'])->name('social-posts.destroy');
        });

});

Route::get('/bw', [BeerWallController::class, 'indexFront'])->name('beerwall.index-front');
Route::get('/bw-halloween', [BeerWallController::class, 'indexHalloween'])->name('beerwall.index-hallowen');
Route::get('/bw-christmas', [BeerWallController::class, 'indexChristmas'])->name('beerwall.index-christmas');
Route::any('/gopos/webhook', [GoPosController::class, 'webhook']);

