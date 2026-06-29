<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $apiKey;

    // RajaOngkir API V2 (by Komerce) - domain & struktur endpoint baru,
    // menggantikan domain lama api.rajaongkir.com/starter yang sudah non-aktif.
    protected string $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    // Origin diisi dengan ID subdistrict/destination hasil pencarian di API V2.
    // Default: Yogyakarta. Ganti via .env (RAJAONGKIR_ORIGIN_ID) kalau toko
    // berlokasi di kota lain. ID ini BUKAN city_id lama (mis. 501), karena
    // API V2 punya skema ID baru yang hanya bisa didapat lewat endpoint
    // domestic-destination?search=.
    protected string $originId;

    // Mapping nama kota mock -> kata kunci pencarian di endpoint
    // domestic-destination, supaya cost asli bisa dihitung tanpa mengubah
    // dropdown provinsi/kota mock yang sudah dipakai di view & controller.
    private const CITY_SEARCH_KEYWORDS = [
        17  => 'Badung',
        114 => 'Denpasar',
        152 => 'Jakarta Barat',
        153 => 'Jakarta Pusat',
        154 => 'Jakarta Selatan',
        155 => 'Jakarta Timur',
        156 => 'Jakarta Utara',
        23  => 'Bandung',
        54  => 'Bekasi',
        78  => 'Bogor',
        115 => 'Depok',
        399 => 'Semarang',
        445 => 'Surakarta',
        419 => 'Sleman',
        501 => 'Yogyakarta',
        256 => 'Malang',
        444 => 'Surabaya',
        43  => 'Banda Aceh',
        278 => 'Medan',
    ];

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY', '');
        $this->originId = env('RAJAONGKIR_ORIGIN_ID', '');
    }

    /**
     * Get list of provinces.
     *
     * CATATAN PENTING: RajaOngkir API V2 (Komerce) TIDAK lagi menyediakan
     * endpoint /province & /city seperti versi Starter klasik. API V2 hanya
     * punya satu endpoint pencarian destinasi (domestic-destination?search=)
     * yang mengembalikan kombinasi kecamatan+kota+provinsi sekaligus dalam
     * satu hasil, bukan struktur provinsi->kota bertingkat.
     *
     * Supaya alur dropdown Provinsi -> Kota di checkout (yang sudah dipakai
     * di view & JS) tetap berfungsi tanpa perlu dirombak total, daftar
     * provinsi & kota di bawah ini tetap memakai data tetap (bukan dummy
     * acak - ini daftar provinsi & kota asli Indonesia). Yang diambil dari
     * API ASLI adalah harga ongkirnya (lihat calculateCost()).
     */
    public function getProvinces(): array
    {
        return $this->getMockProvinces();
    }

    /**
     * Get list of cities by province ID.
     */
    public function getCities(int $provinceId): array
    {
        return $this->getMockCities($provinceId);
    }

    /**
     * Calculate shipping cost.
     *
     * Alur untuk API ASLI (API V2 / Komerce):
     *  1) Cari ID tujuan asli via endpoint pencarian domestic-destination,
     *     menggunakan nama kota dari CITY_SEARCH_KEYWORDS sebagai kata kunci
     *     (karena city_id mock di atas bukan ID yang dikenal API V2).
     *  2) Pakai ID hasil pencarian itu untuk panggil domestic-cost.
     * Kalau salah satu langkah gagal (key kosong, kota tak ditemukan,
     * request error, dll), otomatis fallback ke mock supaya checkout tetap
     * bisa jalan saat demo ke dosen.
     */
    public function calculateCost(int $destinationCityId, int $weightGrams, string $courier): array
    {
        if (empty($this->apiKey) || empty($this->originId)) {
            Log::info('RajaOngkir: API key/origin ID kosong, pakai mock cost.');
            return $this->getMockCost($destinationCityId, $weightGrams, $courier);
        }

        try {
            $destinationId = $this->findDestinationId($destinationCityId);

            if (empty($destinationId)) {
                Log::warning("RajaOngkir: kota '{$destinationCityId}' tidak ditemukan di API V2. Pakai mock cost.");
                return $this->getMockCost($destinationCityId, $weightGrams, $courier);
            }

            $response = Http::asForm()
                ->withHeaders(['key' => $this->apiKey])
                ->post("{$this->baseUrl}/calculate/domestic-cost", [
                    'origin'      => $this->originId,
                    'destination' => $destinationId,
                    'weight'      => $weightGrams,
                    'courier'     => strtolower($courier),
                    'price'       => 'lowest',
                ]);

            if ($response->successful()) {
                $results = $response->json()['data'] ?? [];

                if (!empty($results)) {
                    return $this->mapV2CostToLegacyFormat($results, $courier);
                }
            }

            Log::warning('RajaOngkir API V2 cost gagal: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('RajaOngkir API V2 connection error: ' . $e->getMessage());
        }

        return $this->getMockCost($destinationCityId, $weightGrams, $courier);
    }

    /**
     * Cari ID destinasi asli di API V2 berdasarkan nama kota (mock city_id
     * lama dipetakan ke nama kota lewat CITY_SEARCH_KEYWORDS, lalu nama itu
     * dicari ke endpoint domestic-destination milik RajaOngkir).
     */
    private function findDestinationId(int $mockCityId): ?string
    {
        $keyword = self::CITY_SEARCH_KEYWORDS[$mockCityId] ?? null;

        if (empty($keyword)) {
            return null;
        }

        $response = Http::withHeaders(['key' => $this->apiKey])
            ->get("{$this->baseUrl}/destination/domestic-destination", [
                'search' => $keyword,
                'limit'  => 1,
                'offset' => 0,
            ]);

        if (!$response->successful()) {
            Log::warning("RajaOngkir: pencarian destinasi '{$keyword}' gagal: " . $response->body());
            return null;
        }

        $data = $response->json()['data'] ?? [];

        return $data[0]['id'] ?? null;
    }

    /**
     * Konversi format response API V2 (flat array per layanan kurir) ke
     * format lama costs[].cost[0].value yang sudah dipakai controller &
     * mock data, supaya RajaOngkirController tidak perlu diubah sama sekali.
     */
    private function mapV2CostToLegacyFormat(array $v2Results, string $courier): array
    {
        $services = [];

        foreach ($v2Results as $item) {
            $services[] = [
                'service'     => $item['service'] ?? strtoupper($courier),
                'description' => $item['description'] ?? '',
                'cost' => [[
                    'value' => (int) ($item['cost'] ?? 0),
                    'etd'   => $item['etd'] ?? '-',
                    'note'  => '',
                ]],
            ];
        }

        $courierName = $v2Results[0]['name'] ?? strtoupper($courier);

        return [[
            'code'  => strtolower($courier),
            'name'  => $courierName,
            'costs' => $services,
        ]];
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