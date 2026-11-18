<?php

namespace App\Services;

use App\Queries\Contracts\BeerwallQueriesInterface;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    public function __construct(
        private readonly BeerwallQueriesInterface $beerwallQueries,
    ) {}

    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public function syncBeers()
    {
        $url = config('services.firebase.url') . 'beers.json';
        $secret = config('services.firebase.secret');

        $beers = $this->beerwallQueries->findPublishedOrderedByPosition()->map(function ($beer) {
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

        // \Log::info('Syncing to Firebase:', $beers);

        $response = Http::put($url . '?auth=' . $secret, $beers);

        return $response->successful();
    }


    public function getSettings(): array
    {
        $url = config('services.firebase.url') . 'settings.json';
        $secret = config('services.firebase.secret');

        $response = Http::get($url . '?auth=' . $secret);

        $data = $response->successful() && is_array($response->json()) ? $response->json() : [];

        $settings = [];
        foreach (self::DAYS as $day) {
            $settings[$day . '_close_time'] = $data[$day . '_close_time'] ?? null;
        }

        return $settings;
    }


    public function setSettings(array $input): bool
    {
        $url = config('services.firebase.url') . 'settings.json';
        $secret = config('services.firebase.secret');

        $payload = [];
        foreach (self::DAYS as $day) {
            $value = $input[$day . '_close_time'] ?? null;
            $payload[$day . '_close_time'] = $value ? substr($value, 0, 5) : null;
        }

        $response = Http::put($url . '?auth=' . $secret, $payload);

        if ($response->failed()) {
            \Log::error('Firebase setSettings failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return $response->successful();
    }
}
