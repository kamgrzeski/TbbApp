<?php

namespace App\Queries;

use App\Models\MaltStock;
use Illuminate\Database\Eloquent\Collection;

class MaltStockQueries
{
    
    public static function orderedByName(array $columns = ['*']): Collection
    {
        return MaltStock::orderBy('name')->get($columns);
    }

    
    public static function orderedByKgDesc(): Collection
    {
        return MaltStock::orderBy('kg', 'desc')->get();
    }

    public static function find(int|string $id): ?MaltStock
    {
        return MaltStock::find($id);
    }

    
    public static function kgByIds(array $ids): Collection
    {
        return MaltStock::whereIn('id', $ids)->pluck('kg', 'id');
    }
}
