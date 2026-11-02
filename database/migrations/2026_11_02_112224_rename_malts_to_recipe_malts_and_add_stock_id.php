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
        Schema::rename('malts', 'recipe_malts');
        
        Schema::table('recipe_malts', function (Blueprint $table) {
            $table->foreignId('malt_stock_id')->after('id')->nullable()->constrained('malt_stocks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_malts', function (Blueprint $table) {
            $table->dropForeign(['malt_stock_id']);
            $table->dropColumn('malt_stock_id');
        });
        
        Schema::rename('recipe_malts', 'malts');
    }
};
