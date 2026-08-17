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
        Schema::create('beerwall', function (Blueprint $table) {
            $table->id();
            $table->string('beer_name');
            $table->string('beer_style');
            $table->text('beer_description');
            $table->string('beer_blg');
            $table->string('beer_alc');
            $table->string('beer_price_small');
            $table->string('beer_price_medium');
            $table->string('beer_price_large');
            $table->boolean('is_ended')->default(false);
            $table->boolean('is_premiere')->default(false);
            $table->timestamps();
        });

        DB::table('beerwall')->insert([
            [
                'beer_name' => 'Marcowe',
                'beer_style' => 'Märzen',
                'beer_description' => 'Klasyka dolnej fermentacji. Bogaty profil słodowy i bursztynowa barwa.',
                'beer_blg' => '11.5',
                'beer_alc' => '4.7',
                'beer_price_small' => '12',
                'beer_price_medium' => '23',
                'beer_price_large' => '41',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'beer_name' => 'APA',
                'beer_style' => 'American Pale Ale',
                'beer_description' => 'Lekkie i sesyjne. Wyraźny aromat amerykańskich chmieli: cytrusy oraz mango.',
                'beer_blg' => '11.5',
                'beer_alc' => '4.9',
                'beer_price_small' => '12',
                'beer_price_medium' => '23',
                'beer_price_large' => '41',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'beer_name' => 'Miodowe',
                'beer_style' => 'Märzen',
                'beer_description' => 'Pełne i bursztynowe. Bogaty profil słodowy wzbogacony dodatkiem miodu gryczanego.',
                'beer_blg' => '11.5',
                'beer_alc' => '4.7',
                'beer_price_small' => '12',
                'beer_price_medium' => '23',
                'beer_price_large' => '41',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'beer_name' => 'REDEN IPA',
                'beer_style' => 'SESSION COLD IPA',
                'beer_description' => 'Nowoczesna Session Cold IPA – czysty profil i potężna dawka aromatycznego chmielu.',
                'beer_blg' => '11.0',
                'beer_alc' => '5.0',
                'beer_price_small' => '12',
                'beer_price_medium' => '23',
                'beer_price_large' => '41',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beerwall');
    }
};
