<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recipe_malts', function (Blueprint $table) {
            $table->dropColumn('extract');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_malts', function (Blueprint $table) {
            $table->decimal('extract', 8, 2)->nullable();
        });
    }
};
