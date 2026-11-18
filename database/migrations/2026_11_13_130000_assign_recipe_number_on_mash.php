<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->integer('number')->nullable()->change();
        });

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

        DB::table('recipes')
            ->whereNull('mashed_at')
            ->whereNull('yeast_pitched_at')
            ->update(['number' => null]);
    }


    public function down(): void
    {
        //
    }
};
