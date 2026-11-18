<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('recipe_batches')
            ->whereIn('recipe_id', DB::table('recipes')
                ->whereNull('mashed_at')
                ->whereNull('yeast_pitched_at')
                ->select('id'))
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
