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
        Schema::table('malt_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('malt_stocks', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('extract');
            }
        });

        Schema::table('recipe_malts', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_malts', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });

        Schema::table('malt_stocks', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
