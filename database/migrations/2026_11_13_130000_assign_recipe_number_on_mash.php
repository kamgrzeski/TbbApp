<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Numer receptury (number) nadawany jest dopiero przy wykonaniu zasypu,
     * tak jak numery warek. Wersje robocze nie mają numeru.
     */
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->integer('number')->nullable()->change();
        });

        // Uwarzone receptury (zasyp lub drożdże) numerujemy kolejno wg ID (kolejność utworzenia)
        $brewed = DB::table('recipes')
            ->where(function ($q) {
                $q->whereNotNull('mashed_at')->orWhereNotNull('yeast_pitched_at');
            })
            ->orderBy('id')
            ->get(['id', 'user_id']);

        $counters = [];
        foreach ($brewed as $recipe) {
            $counters[$recipe->user_id] = ($counters[$recipe->user_id] ?? 0) + 1;
            DB::table('recipes')->where('id', $recipe->id)->update(['number' => $counters[$recipe->user_id]]);
        }

        // Wersje robocze – bez numeru
        DB::table('recipes')
            ->whereNull('mashed_at')
            ->whereNull('yeast_pitched_at')
            ->update(['number' => null]);
    }

    /**
     * Migracja nieodwracalna (numery zostałyby utracone).
     */
    public function down(): void
    {
        //
    }
};
