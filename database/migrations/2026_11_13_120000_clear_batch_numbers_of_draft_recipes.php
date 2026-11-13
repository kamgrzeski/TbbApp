<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Wersje robocze (bez wykonanego zasypu) nie powinny zajmować numerów warek.
     * Numery zostaną nadane przy wykonaniu zasypu.
     */
    public function up(): void
    {
        // Wersja robocza = brak zasypu ORAZ brak zadania drożdży
        // (starsze uwarzone receptury nie mają mashed_at, bo kolumna powstała później)
        DB::table('recipe_batches')
            ->whereIn('recipe_id', DB::table('recipes')
                ->whereNull('mashed_at')
                ->whereNull('yeast_pitched_at')
                ->select('id'))
            ->delete();
    }

    /**
     * Brak możliwości odtworzenia dawnych numerów – migracja nieodwracalna.
     */
    public function down(): void
    {
        //
    }
};
