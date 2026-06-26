<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.rajaongkir.com/starter';
    protected string $originCityId;

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY', '');
        // Default origin: Kota Yogyakarta (city_id: 501)
        $this->originCityId = env('RAJAONGKIR_ORIGIN_CITY', '501');
    }

    /**
     * Get list of provinces.
     */
    public function getProvinces(): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockProvinces();
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get("{$this->baseUrl}/province");

            if ($response->successful()) {
                return $response->json()['rajaongkir']['results'] ?? [];
            }
            
            Log::warning('RajaOngkir API failed. Using mock provinces. Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('RajaOngkir connection error: ' . $e->getMessage());
        }

        return $this->getMockProvinces();
    }

    /**
     * Get list of cities by province ID.
     */
    public function getCities(int $provinceId): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockCities($provinceId);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get("{$this->baseUrl}/city", [
                'province' => $provinceId
            ]);

            if ($response->successful()) {
                return $response->json()['rajaongkir']['results'] ?? [];
            }

            Log::warning("RajaOngkir API failed for cities in province {$provinceId}. Using mock cities.");
        } catch (\Exception $e) {
            Log::error('RajaOngkir connection error for cities: ' . $e->getMessage());
        }

        return $this->getMockCities($provinceId);
    }

    /**
     * Calculate shipping cost.
     */
    public function calculateCost(int $destinationCityId, int $weightGrams, string $courier): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockCost($destinationCityId, $weightGrams, $courier);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->post("{$this->baseUrl}/cost", [
                'origin' => $this->originCityId,
                'destination' => $destinationCityId,
                'weight' => $weightGrams,
                'courier' => strtolower($courier),
            ]);

            if ($response->successful()) {
                return $response->json()['rajaongkir']['results'] ?? [];
            }

            Log::warning("RajaOngkir API cost calculation failed. Using mock cost.");
        } catch (\Exception $e) {
            Log::error('RajaOngkir cost connection error: ' . $e->getMessage());
        }

        return $this->getMockCost($destinationCityId, $weightGrams, $courier);
    }

    /* =========================================================================
       MOCK DATA FALLBACKS (Guarantees functionality even without API Key)
       ========================================================================= */

    private function getMockProvinces(): array
    {
        return [
            ['province_id' => '1', 'province' => 'Bali'],
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '9', 'province' => 'Jawa Barat'],
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '11', 'province' => 'DI Yogyakarta'],
            ['province_id' => '12', 'province' => 'Jawa Timur'],
            ['province_id' => '21', 'province' => 'Nanggroe Aceh Darussalam (NAD)'],
            ['province_id' => '34', 'province' => 'Sumatera Utara'],
        ];
    }

    private function getMockCities(int $provinceId): array
    {
        $cities = [
            1 => [
                ['city_id' => '17', 'city_name' => 'Badung', 'type' => 'Kabupaten', 'postal_code' => '80351'],
                ['city_id' => '114', 'city_name' => 'Denpasar', 'type' => 'Kota', 'postal_code' => '80223'],
            ],
            6 => [
                ['city_id' => '152', 'city_name' => 'Jakarta Barat', 'type' => 'Kota', 'postal_code' => '11220'],
                ['city_id' => '153', 'city_name' => 'Jakarta Pusat', 'type' => 'Kota', 'postal_code' => '10110'],
                ['city_id' => '154', 'city_name' => 'Jakarta Selatan', 'type' => 'Kota', 'postal_code' => '12110'],
                ['city_id' => '155', 'city_name' => 'Jakarta Timur', 'type' => 'Kota', 'postal_code' => '13110'],
                ['city_id' => '156', 'city_name' => 'Jakarta Utara', 'type' => 'Kota', 'postal_code' => '14110'],
            ],
            9 => [
                ['city_id' => '23', 'city_name' => 'Bandung', 'type' => 'Kota', 'postal_code' => '40111'],
                ['city_id' => '54', 'city_name' => 'Bekasi', 'type' => 'Kota', 'postal_code' => '17121'],
                ['city_id' => '78', 'city_name' => 'Bogor', 'type' => 'Kota', 'postal_code' => '16119'],
                ['city_id' => '115', 'city_name' => 'Depok', 'type' => 'Kota', 'postal_code' => '16411'],
            ],
            10 => [
                ['city_id' => '399', 'city_name' => 'Semarang', 'type' => 'Kota', 'postal_code' => '50125'],
                ['city_id' => '445', 'city_name' => 'Surakarta', 'type' => 'Kota', 'postal_code' => '57111'],
            ],
            11 => [
                ['city_id' => '419', 'city_name' => 'Sleman', 'type' => 'Kabupaten', 'postal_code' => '55511'],
                ['city_id' => '501', 'city_name' => 'Yogyakarta', 'type' => 'Kota', 'postal_code' => '55111'],
            ],
            12 => [
                ['city_id' => '256', 'city_name' => 'Malang', 'type' => 'Kota', 'postal_code' => '65111'],
                ['city_id' => '444', 'city_name' => 'Surabaya', 'type' => 'Kota', 'postal_code' => '60111'],
            ],
            21 => [
                ['city_id' => '43', 'city_name' => 'Banda Aceh', 'type' => 'Kota', 'postal_code' => '23111'],
            ],
            34 => [
                ['city_id' => '278', 'city_name' => 'Medan', 'type' => 'Kota', 'postal_code' => '20111'],
            ],
        ];

        return $cities[$provinceId] ?? [];
    }

    private function getMockCost(int $destinationCityId, int $weightGrams, string $courier): array
    {
        $weightKg = max(0.1, $weightGrams / 1000);
        $weightCeil = ceil($weightKg);

        // Simple cost calculator simulation based on courier and destination
        // Base rate depending on destination city ID region
        $baseRate = 12000; // default for Sleman/Jogja
        
        if ($destinationCityId == 501 || $destinationCityId == 419) {
            $baseRate = 8000; // Local Jogja
        } elseif (in_array($destinationCityId, [152, 153, 154, 155, 156])) {
            $baseRate = 15000; // Jakarta
        } elseif ($destinationCityId == 23 || $destinationCityId == 54 || $destinationCityId == 78 || $destinationCityId == 115) {
            $baseRate = 13000; // Jabar (Bandung/Bekasi/etc)
        } elseif ($destinationCityId == 399 || $destinationCityId == 445) {
            $baseRate = 10000; // Jateng
        } elseif ($destinationCityId == 444 || $destinationCityId == 256) {
            $baseRate = 11000; // Jatim
        } elseif ($destinationCityId == 17 || $destinationCityId == 114) {
            $baseRate = 22000; // Bali
        } elseif ($destinationCityId == 43 || $destinationCityId == 278) {
            $baseRate = 35000; // Sumatra/Aceh
        }

        // Adjust based on courier
        $courierName = strtoupper($courier);
        
        $services = [];
        if ($courierName === 'JNE') {
            $services = [
                [
                    'service' => 'REG',
                    'description' => 'Layanan Reguler',
                    'cost' => [
                        [
                            'value' => (int)($baseRate * $weightCeil),
                            'etd' => '2-3 HARI',
                            'note' => ''
                        ]
                    ]
                ],
                [
                    'service' => 'OKE',
                    'description' => 'Ongkos Kirim Ekonomis',
                    'cost' => [
                        [
                            'value' => (int)(($baseRate - 2000) * $weightCeil),
                            'etd' => '4-5 HARI',
                            'note' => ''
                        ]
                    ]
                ]
            ];
        } elseif ($courierName === 'POS') {
            $services = [
                [
                    'service' => 'Pos Reguler',
                    'description' => 'Pos Reguler',
                    'cost' => [
                        [
                            'value' => (int)(($baseRate - 1000) * $weightCeil),
                            'etd' => '3-5 HARI',
                            'note' => ''
                        ]
                    ]
                ]
            ];
        } else { // TIKI
            $services = [
                [
                    'service' => 'REG',
                    'description' => 'Regular Service',
                    'cost' => [
                        [
                            'value' => (int)($baseRate * $weightCeil),
                            'etd' => '2-3 HARI',
                            'note' => ''
                        ]
                    ]
                ]
            ];
        }

        return [
            [
                'code' => strtolower($courier),
                'name' => $courierName === 'JNE' ? 'Jalur Nugraha Ekakurir (JNE)' : ($courierName === 'POS' ? 'POS Indonesia (POS)' : 'Citra Van Titipan Kilat (TIKI)'),
                'costs' => $services
            ]
        ];
    }
}