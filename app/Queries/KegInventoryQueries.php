<?php

namespace App\Queries;

use App\Models\KegInventories;
use App\Queries\Contracts\KegInventoryQueriesInterface;
use App\Services\KegStockService;

class KegInventoryQueries implements KegInventoryQueriesInterface
{
    public function findOrCreateSingleton(bool $lock = false): KegInventories
    {
        $query = KegInventories::query();

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->firstOrCreate(
            [],
            [
                'total_kegs' => KegStockService::TOTAL_KEGS,
                'empty_kegs' => KegStockService::TOTAL_KEGS,
            ]
        );
    }

    public function findLockedOrFail(): KegInventories
    {
        return KegInventories::query()->lockForUpdate()->firstOrFail();
    }
}
