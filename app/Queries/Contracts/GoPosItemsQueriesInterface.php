<?php

namespace App\Queries\Contracts;

use Illuminate\Support\Collection;

interface GoPosItemsQueriesInterface
{
    public function findAll(): Collection;
}
