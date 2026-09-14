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
        Schema::create('keg_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recipe_id')
                ->constrained('recipes')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('full_kegs')->default(0);

            $table->boolean('is_archived')->default(0);

            $table->timestamps();

            $table->unique('recipe_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keg_stocks');
    }
};
