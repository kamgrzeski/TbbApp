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
            $table->renameColumn('is_active', 'is_enzymatic_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('malt_stocks', function (Blueprint $table) {
            $table->renameColumn('is_enzymatic_active', 'is_active');
        });
    }
};
