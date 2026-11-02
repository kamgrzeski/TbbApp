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
        Schema::create('malt_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('kg', 10, 2);
            $table->decimal('extract', 10, 2)->nullable();
            $table->string('invoice_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('malt_stocks');
    }
};
