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
        Schema::create('keg_inventories', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('total_kegs')->default(20);
            $table->unsignedTinyInteger('empty_kegs')->default(20);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keg_inventories');
    }
};
