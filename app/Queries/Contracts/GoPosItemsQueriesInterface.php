<?php

namespace App\Queries\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface GoPosItemsQueriesInterface
{
    public function findAll(): Collection;
}
