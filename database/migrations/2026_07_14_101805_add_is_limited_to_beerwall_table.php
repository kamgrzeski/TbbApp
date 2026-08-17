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
        Schema::table('beerwall', function (Blueprint $table) {
            $table->boolean('is_limited')->default(false)->after('is_coming_soon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beerwall', function (Blueprint $table) {
            $table->dropColumn('is_limited');
        });
    }
};
