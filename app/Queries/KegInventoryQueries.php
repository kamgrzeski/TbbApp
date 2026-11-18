<?php

namespace App\Queries;

use App\Models\KegInventories;
use App\Services\KegStockService;

class KegInventoryQueries
{

    public static function singleton(bool $lock = false): KegInventories
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


    public static function lockedFirstOrFail(): KegInventories
    {
        return KegInventories::query()->lockForUpdate()->firstOrFail();
    }
}
