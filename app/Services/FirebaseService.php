<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Beerwall;

class FirebaseService
{
    public function syncBeers()
    {
        $url = config('services.firebase.url') . 'beers.json';
        $secret = config('services.firebase.secret');

        // Pobieramy wszystkie piwa i mapujemy je na format JSON dla Firebase
        $beers = Beerwall::get()->map(function ($beer) {
            return [
                'beer_name'         => (string)$beer->beer_name,
                'beer_style'        => (string)$beer->beer_style,
                'beer_description'  => (string)$beer->beer_description,
                'beer_blg'          => (string)$beer->beer_blg,
                'beer_alc'          => (string)$beer->beer_alc,
                'beer_price_small'  => (float)$beer->beer_price_small,
                'beer_price_medium' => (float)$beer->beer_price_medium,
                'beer_price_large'  => (float)$beer->beer_price_large,
                'is_ended'          => (bool)$beer->is_ended,
                'is_coming_soon'    => (bool)$beer->is_coming_soon,
                'is_premiere'       => (bool)$beer->is_premiere,
            ];
        })->toArray();

        // Debugowanie: Odkomentuj poniższą linię, aby zobaczyć w logach co wysyłasz
        // \Log::info('Syncing to Firebase:', $beers);

        // Wyślij dane
        $response = Http::put($url . '?auth=' . $secret, $beers);

        return $response->successful();
    }
}