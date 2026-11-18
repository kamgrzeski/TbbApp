<?php

namespace App\Queries\Contracts;

interface RecipeBatchesQueriesInterface
{
    public function findMaxBatchNumber(): int;
}
