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
        Schema::create('beerwall_settings', function (Blueprint $table) {
            $table->id();
            $table->time('monday_close_time')->nullable();
            $table->time('tuesday_close_time')->nullable();
            $table->time('wednesday_close_time')->nullable();
            $table->time('thursday_close_time')->nullable();
            $table->time('friday_close_time')->nullable();
            $table->time('saturday_close_time')->nullable();
            $table->time('sunday_close_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beerwall_settings');
    }
};
