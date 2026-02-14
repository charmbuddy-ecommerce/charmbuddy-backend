<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private readonly RajaOngkirService $rajaOngkirService)
    {
    }

    public function provinces(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkirService->provinces(),
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'province_id' => ['required', 'integer'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkirService->cities($validated['province_id']),
        ]);
    }

    public function cost(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'origin' => ['required', 'integer'],
            'destination' => ['required', 'integer'],
            'weight' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkirService->cost(
                $validated['origin'],
                $validated['destination'],
                $validated['weight'],
                $validated['courier']
            ),
        ]);
    }
}

