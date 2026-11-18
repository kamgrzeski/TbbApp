<?php

namespace App\Queries;

use App\Models\RecipeBatches;
use App\Queries\Contracts\RecipeBatchesQueriesInterface;

class RecipeBatchesQueries implements RecipeBatchesQueriesInterface
{
    public function findMaxBatchNumber(): int
    {
        return (int) (RecipeBatches::max('batch_number') ?? 0);
    }
}
