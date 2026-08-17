<?php

namespace App\Observers;


use App\Models\Beerwall;
use App\Services\FirebaseService;

class BeerWallObserver
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function saved(BeerWall $beer)
    {
        $this->firebaseService->syncBeers();
    }

    // Wykona się po usunięciu
    public function deleted(Beerwall $beer)
    {
        $this->firebaseService->syncBeers();
    }
}