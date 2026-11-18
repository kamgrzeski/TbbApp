<?php

namespace App\Queries;

use App\Models\Beerwall;
use App\Queries\Contracts\BeerwallQueriesInterface;
use Illuminate\Support\Collection;

class BeerwallQueries implements BeerwallQueriesInterface
{
    public function findAllOrderedByPosition(): Collection
    {
        return Beerwall::orderBy('position')->get();
    }

    public function findDrafts(): Collection
    {
        return Beerwall::where('is_draft', true)->orderBy('created_at', 'desc')->get();
    }

    public function findPublishedOrderedByPosition(): Collection
    {
        return Beerwall::where('is_draft', 0)->orderBy('position')->get();
    }
}
