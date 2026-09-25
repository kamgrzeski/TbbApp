<?php

use App\Http\Controllers\BeerwallController;
use App\Http\Controllers\BrewingController;
use App\Http\Controllers\GoPosController;
use App\Http\Controllers\KegStockController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/brewing', [BrewingController::class, 'index'])->name('brewing.index');
    Route::get('/brewing/create', [BrewingController::class, 'create'])->name('brewing.create');
    Route::get('/brewing/{recipe}', [BrewingController::class, 'show'])->name('brewing.show');
    Route::post('/brewing/{recipe}/comments', [BrewingController::class, 'storeComment'])->name('comments.store');
    Route::delete('/brewing/{recipe}', [BrewingController::class, 'destroy'])->name('brewing.destroy');
    Route::post('/brewing/{recipe}/status', [BrewingController::class, 'updateStatus'])->name('brewing.status');
    Route::post('/brewing/{recipe}/gravity', [BrewingController::class, 'storeGravity'])->name('gravity.store');
    Route::get('/brewing/{recipe}/edit', [BrewingController::class, 'edit'])->name('brewing.edit');
    Route::get('/brewing/{recipe}/clone', [BrewingController::class, 'clone'])->name('brewing.clone');
    Route::patch('/brewing/{recipe}', [BrewingController::class, 'update'])->name('brewing.update');
    Route::get('/brewing/{recipe}/print', [BrewingController::class, 'print'])->name('brewing.print');
    Route::get('/brewing/{recipe}/excise-email', [BrewingController::class, 'generateEmailContentExciseForRecipe'])->name('brewing.excise-email');

    Route::prefix('admin/beerwall')->group(function () {
        Route::get('/', [BeerwallController::class, 'index'])->name('beerwall.admin.index');
        Route::get('/create', [BeerwallController::class, 'create'])->name('beerwall.admin.create');
        Route::post('/', [BeerwallController::class, 'store'])->name('beerwall.admin.store');
        Route::get('/{beerwall}/edit', [BeerwallController::class, 'edit'])->name('beerwall.admin.edit');
        Route::put('/{beerwall}', [BeerwallController::class, 'update'])->name('beerwall.admin.update');
        Route::patch('/{beerwall}/status', [BeerwallController::class, 'updateStatus'])->name('beerwall.admin.status');
        Route::delete('/{beerwall}', [BeerwallController::class, 'destroy'])->name('beerwall.admin.destroy');
        Route::post('/{beerwall}/clone', [BeerwallController::class, 'clone'])->name('beerwall.admin.clone');
    });

    Route::prefix('admin/kegs')
        ->group(function () {Route::get('/', [KegStockController::class, 'index'])->name('kegs.admin.index');
            Route::get('/production', [KegStockController::class, 'createProduction'])->name('kegs.admin.production.create');
            Route::post('/production', [KegStockController::class, 'production'])->name('kegs.admin.production.store');
            Route::post('/issue', [KegStockController::class, 'issue'])->name('kegs.admin.issue.store');
            Route::get('/movements', [KegStockController::class, 'movements'])->name('kegs.admin.movements');
            Route::post('/pool', [KegStockController::class, 'pool'])->name('kegs.admin.pool.store');
    });

    Route::prefix('admin/gopos')
        ->group(function () {Route::get('/', [GoPosController::class, 'index'])->name('gopos.admin.index');
            Route::get('/sync', [GoPosController::class, 'sync'])->name('gopos.admin.items.sync');
            Route::get('/webhooks', [GoPosController::class, 'webhooksList'])->name('gopos.admin.webhooks');
    });
});

Route::get('/bw', [BeerWallController::class, 'indexFront'])->name('beerwall.index-front');
Route::get('/gopos/webhook', [GoPosController::class, 'webhook']);
