<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RajaOngkirService
{
    public function provinces(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl().'/province')
            ->throw()
            ->json();

        return data_get($response, 'rajaongkir.results', []);
    }

    public function cities(int $provinceId): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl().'/city', ['province' => $provinceId])
            ->throw()
            ->json();

        return data_get($response, 'rajaongkir.results', []);
    }

    public function cost(int $origin, int $destination, int $weight, string $courier): array
    {
        $response = Http::asForm()
            ->withHeaders($this->headers())
            ->post($this->baseUrl().'/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ])
            ->throw()
            ->json();

        return data_get($response, 'rajaongkir.results', []);
    }

    protected function headers(): array
    {
        $apiKey = config('services.rajaongkir.api_key');

        if (! $apiKey) {
            throw ValidationException::withMessages([
                'rajaongkir' => 'RajaOngkir API key is not configured.',
            ]);
        }

        return ['key' => $apiKey];
    }

    protected function baseUrl(): string
    {
        return rtrim(config('services.rajaongkir.base_url', 'https://api.rajaongkir.com/starter'), '/');
    }
}

