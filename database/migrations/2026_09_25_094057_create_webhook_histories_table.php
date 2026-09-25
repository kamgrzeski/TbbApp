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
        Schema::create('webhook_histories', function (Blueprint $table) {
            $table->id();

            $table->string('event_type')->nullable()->index();
            $table->string('type')->nullable()->index();
            $table->string('organization_id')->nullable()->index();
            $table->string('resource_id')->nullable()->index();
            $table->timestamp('occurred_at')->nullable();

            $table->json('payload');

            $table->string('status')->default('received')->index();
            $table->text('error_message')->nullable();

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_histories');
    }
};
