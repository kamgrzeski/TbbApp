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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('number');
            $table->integer('tank_number');
            $table->string('name');
            $table->float('volume'); // objętość
            $table->float('efficiency'); // wydajność
            $table->timestamp('yeast_pitched_at')->nullable();
            $table->timestamp('primary_fermentation_start')->nullable();
            $table->timestamp('secondary_fermentation_start')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
