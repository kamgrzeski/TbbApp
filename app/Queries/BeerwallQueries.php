<?php

namespace App\Queries;

use App\Models\Beerwall;
use Illuminate\Database\Eloquent\Collection;

class BeerwallQueries
{

    public static function ordered(): Collection
    {
        return Beerwall::orderBy('position')->get();
    }


    public static function drafts(): Collection
    {
        return Beerwall::where('is_draft', true)->orderBy('created_at', 'desc')->get();
    }


    public static function published(): Collection
    {
        return Beerwall::where('is_draft', 0)->orderBy('position')->get();
    }
}
