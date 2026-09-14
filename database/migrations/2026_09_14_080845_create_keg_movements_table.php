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
        Schema::create('keg_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recipe_id')
                ->nullable()
                ->constrained('recipes')
                ->nullOnDelete();

            $table->enum('type', [
                'production',
                'issue',
                'return',
            ]);

            $table->foreignId('keg_stock_id')
                ->constrained('keg_stocks')
                ->cascadeOnDelete();

            $table->integer('quantity');

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['recipe_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keg_movements');
    }
};
