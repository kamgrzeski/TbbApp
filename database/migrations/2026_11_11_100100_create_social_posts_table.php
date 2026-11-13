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
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->nullable()->constrained('recipes')->nullOnDelete();
            $table->string('topic_type', 50);
            $table->string('custom_topic', 255)->nullable();
            $table->text('custom_guidelines')->nullable();
            $table->longText('prompt_used');
            $table->longText('generated_content');
            $table->string('platform', 10)->default('all');
            $table->string('status', 20)->default('draft');
            $table->dateTime('event_at')->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
