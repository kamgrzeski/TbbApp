<?php

namespace App\Queries\Contracts;

use App\Models\MaltStock;
use Illuminate\Database\Eloquent\Collection;

interface MaltStockQueriesInterface
{
    public function findAllOrderedByName(array $columns = ['*']): Collection;

    public function findAllOrderedByKgDesc(): Collection;

    public function findById(int|string $id): ?MaltStock;

    public function pluckKgById(array $ids): Collection;
}
