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
        Schema::create('gopos_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('gopos_id');
            $table->string('gopos_name');
            $table->decimal('gopos_price', 10, 2);
            $table->unsignedBigInteger('gopos_catgory_id');
            $table->string('gopos_status');

            $table->timestamps();

            $table->unique('gopos_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gopos_items');
    }
};
