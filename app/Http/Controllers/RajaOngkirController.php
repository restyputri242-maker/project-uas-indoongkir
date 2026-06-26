<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * Get list of provinces.
     */
    public function getProvinces(): JsonResponse
    {
        $provinces = $this->rajaOngkir->getProvinces();
        return response()->json($provinces);
    }

    /**
     * Get list of cities in a province.
     */
    public function getCities(int $provinceId): JsonResponse
    {
        $cities = $this->rajaOngkir->getCities($provinceId);
        return response()->json($cities);
    }

    /**
     * Calculate cost.
     */
    public function calculateCost(Request $request): JsonResponse
    {
        $request->validate([
            'destination' => ['required', 'integer'],
            'weight' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string', 'in:jne,pos,tiki'],
        ]);

        $costs = $this->rajaOngkir->calculateCost(
            $request->destination,
            $request->weight,
            $request->courier
        );

        return response()->json($costs);
    }
}