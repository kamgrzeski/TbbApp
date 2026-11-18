<?php

namespace App\Queries;

use App\Models\GoPosItems;
use App\Queries\Contracts\GoPosItemsQueriesInterface;
use Illuminate\Support\Collection;

class GoPosItemsQueries implements GoPosItemsQueriesInterface
{
    public function findAll(): Collection
    {
        return GoPosItems::query()->get();
    }
}
