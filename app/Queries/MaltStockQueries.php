<?php

namespace App\Queries;

use App\Models\MaltStock;
use App\Queries\Contracts\MaltStockQueriesInterface;
use Illuminate\Support\Collection;

class MaltStockQueries implements MaltStockQueriesInterface
{
    public function findAllOrderedByName(array $columns = ['*']): Collection
    {
        return MaltStock::orderBy('name')->get($columns);
    }

    public function findAllOrderedByKgDesc(): Collection
    {
        return MaltStock::orderBy('kg', 'desc')->get();
    }

    public function findById(int|string $id): ?MaltStock
    {
        return MaltStock::find($id);
    }

    public function pluckKgById(array $ids): Collection
    {
        return MaltStock::whereIn('id', $ids)->pluck('kg', 'id');
    }
}
