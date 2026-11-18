<?php

namespace App\Queries\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface BeerwallQueriesInterface
{
    public function findAllOrderedByPosition(): Collection;

    public function findDrafts(): Collection;

    public function findPublishedOrderedByPosition(): Collection;
}
