<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RajaOngkirService
{
    /**
     * Mengambil daftar provinsi (Hierarchical Method).
     */
    public function provinces(): array
    {
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($this->headers())
            ->get($this->baseUrl().'/destination/province')
            ->throw()
            ->json();

        return data_get($response, 'data', []);
    }

    /**
     * Mengambil daftar kota berdasarkan ID provinsi.
     */
    public function cities(int $provinceId): array
    {
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($this->headers())
            ->get($this->baseUrl().'/destination/city', [
                'province' => $provinceId
            ])
            ->throw()
            ->json();

        return data_get($response, 'data', []);
    }

    /**
     * Menghitung ongkos kirim domestik (Hierarchical Method).
     * Endpoint: /calculate/domestic-cost
     */
    public function cost(int $origin, int $destination, int $weight, string $courier): array
    {
    // Menggunakan asForm() sesuai dokumentasi 'application/x-www-form-urlencoded'
    $response = Http::asForm() 
        ->withOptions(['verify' => false])
        ->withHeaders($this->headers())
        ->post($this->baseUrl().'/calculate/district/domestic-cost', [ // Jalur sesuai image_10cebe.png
            'origin'      => $origin,
            'destination' => $destination,
            'weight'      => $weight,
            'courier'     => $courier,
        ])
        ->throw()
        ->json();

    return data_get($response, 'data', []);
   }

    protected function headers(): array
    {
        $apiKey = config('services.rajaongkir.api_key');

        if (! $apiKey) {
            throw ValidationException::withMessages([
                'rajaongkir' => 'API key is not configured.',
            ]);
        }

        return [
            'key'    => $apiKey, // Komerce menggunakan header 'key'
            'Accept' => 'application/json',
        ];
    }

    protected function baseUrl(): string
    {
        // Pastikan .env: https://rajaongkir.komerce.id/api/v1
        return rtrim(config('services.rajaongkir.base_url'), '/');
    }
}   