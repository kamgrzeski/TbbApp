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
        Schema::table('keg_movements', function (Blueprint $table) {
            $table->boolean('is_current')->default(false)->after('note');
            $table->integer('capacity')->default(0)->after('is_current');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keg_movements', function (Blueprint $table) {
            $table->dropColumn([
                'gopos_item_ids'
            ]);
        });
    }
};
