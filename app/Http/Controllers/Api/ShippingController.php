<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private readonly RajaOngkirService $rajaOngkirService)
    {
    }

    public function provinces(): JsonResponse
    {
        try {
            $provinces = $this->rajaOngkirService->provinces();
            return response()->json(['success' => true, 'data' => $provinces]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cities(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'province_id' => ['required', 'integer'],
        ]);

        try {
            $cities = $this->rajaOngkirService->cities($validated['province_id']);
            return response()->json(['success' => true, 'data' => $cities]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cost(Request $request): JsonResponse
    {
        // Validasi input wajib
        $validated = $request->validate([
            'origin'      => ['required', 'integer'], // Harus ID Kecamatan
            'destination' => ['required', 'integer'], // Harus ID Kecamatan
            'weight'      => ['required', 'integer', 'min:1'],
            'courier'     => ['required', 'string'],
        ]);

        try {
            $costs = $this->rajaOngkirService->cost(
                $validated['origin'],
                $validated['destination'],
                $validated['weight'],
                $validated['courier']
            );

            return response()->json(['success' => true, 'data' => $costs]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}