<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Numer warki jest unikalny w skali całej bazy – baza sama odrzuci duplikat.
     */
    public function up(): void
    {
        Schema::table('recipe_batches', function (Blueprint $table) {
            $table->unique('batch_number');
        });
    }

    public function down(): void
    {
        Schema::table('recipe_batches', function (Blueprint $table) {
            $table->dropUnique(['batch_number']);
        });
    }
};
