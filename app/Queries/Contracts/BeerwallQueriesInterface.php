<?php

namespace App\Queries\Contracts;

use Illuminate\Support\Collection;

interface BeerwallQueriesInterface
{
    public function findAllOrderedByPosition(): Collection;

    public function findDrafts(): Collection;

    public function findPublishedOrderedByPosition(): Collection;
}
