<?php

namespace App\Queries;

use App\Models\GoPosItems;
use Illuminate\Database\Eloquent\Collection;

class GoPosItemsQueries
{
    public static function all(): Collection
    {
        return GoPosItems::query()->get();
    }
}
