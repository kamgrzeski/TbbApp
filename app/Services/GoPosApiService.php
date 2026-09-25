<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoPosApiService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $organizationId;

    private const TOKEN_CACHE_KEY = 'gopos_api.access_token';
    private const HTTP_TIMEOUT = 15;

    public function __construct()
    {
        $this->baseUrl = 'https://app.gopos.io';
        $this->clientId = '428bc2bd-c3cb-46f4-97e2-164b00510f73';
        $this->clientSecret = '4447dafe-28d8-4f52-a1f2-7e241b246254';
        $this->organizationId = '15066';
    }

    public function generateAccessToken(): array
    {
        return $this->sendRequest(function () {
            return Http::timeout(self::HTTP_TIMEOUT)
                ->asForm()
                ->post($this->baseUrl . '/oauth/token', [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'organization',
                    'organization_id' => $this->organizationId
                ]);
        });
    }

    protected function getAccessToken(): string
    {
        $cachedToken = Cache::get(self::TOKEN_CACHE_KEY);

        if ($cachedToken) {
            return $cachedToken;
        }

        $tokenData = $this->generateAccessToken();

        Cache::put(
            self::TOKEN_CACHE_KEY,
            $tokenData['access_token'],
            now()->addSeconds($tokenData['expires_in'])
        );

        return $tokenData['access_token'];
    }

    protected function sendRequest(callable $requestCallback): array
    {
        try {
            /** @var Response $response */
            $response = $requestCallback();
            $response->throw();

            return $response->json() ?? [];

        } catch (ConnectionException $e) {
            throw new Exception(
                'Nie udało się nawiązać połączenia z API BDO (Timeout lub błąd sieci).',
                0,
                $e
            );
        } catch (RequestException $e) {
            $status = $e->response->status();
            $message = $e->response->json('message') ?? 'Błąd zapytania HTTP';

            throw new Exception(
                "API BDO zwróciło błąd [HTTP {$status}]: {$message}",
                $status,
                $e
            );
        }
    }

    public function makeGetItemsRequest()
    {
        return Http::timeout(self::HTTP_TIMEOUT)
            ->withToken($this->getAccessToken())
            ->acceptJson()
            ->get(
                $this->baseUrl . '/api/v3/' . $this->organizationId . '/items',
                [
                    'include' => 'category',
                    'size' => 100
                ]
            );
    }

    public function makeGetCategoriesRequest()
    {
        return Http::timeout(self::HTTP_TIMEOUT)
            ->withToken($this->getAccessToken())
            ->acceptJson()
            ->get($this->baseUrl . '/api/v3/'.$this->organizationId.'/categories');
    }

    public function makeGetOrderRequest($resourceId)
    {
        return Http::timeout(self::HTTP_TIMEOUT)
            ->withToken($this->getAccessToken())
            ->acceptJson()
            ->get(
                $this->baseUrl . '/api/v3/' . $this->organizationId . '/orders/' . $resourceId,
                [
                    'include' => 'custom_fields,fiscalization,items,items.tax,items.direction,items.product,items.absolute_quantity,tax.items,removed_items,removed_items.tax,removed_items.direction,removed_items.product,removed_items.absolute_quantity',
                    'size' => 100
                ]
            );
    }

    public function getItems()
    {
        return $this->sendRequest(function () {
            $response = $this->makeGetItemsRequest();

            if ($response->status() === 401) {
                Cache::forget(self::TOKEN_CACHE_KEY);
                $response = $this->makeGetItemsRequest();
            }

            return $response;
        });
    }

    public function getCategories()
    {
        return $this->sendRequest(function () {
            $response = $this->makeGetCategoriesRequest();

            if ($response->status() === 401) {
                Cache::forget(self::TOKEN_CACHE_KEY);
                $response = $this->makeGetCategoriesRequest();
            }

            return $response;
        });
    }

    public function getOrder($resourceId)
    {
        return $this->sendRequest(function () use($resourceId) {
            $response = $this->makeGetOrderRequest($resourceId);

            if ($response->status() === 401) {
                Cache::forget(self::TOKEN_CACHE_KEY);
                $response = $this->makeGetOrderRequest($resourceId);
            }

            return $response;
        });
    }
}