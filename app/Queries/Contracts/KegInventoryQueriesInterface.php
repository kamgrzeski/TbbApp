<?php

namespace App\Queries\Contracts;

use App\Models\KegInventories;

interface KegInventoryQueriesInterface
{
    public function findOrCreateSingleton(bool $lock = false): KegInventories;

    public function findLockedOrFail(): KegInventories;
}
