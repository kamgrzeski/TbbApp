<?php

namespace App\Queries;

use App\Models\RecipeBatches;

class RecipeBatchesQueries
{
    
    public static function maxBatchNumber(): int
    {
        return (int) (RecipeBatches::max('batch_number') ?? 0);
    }
}
