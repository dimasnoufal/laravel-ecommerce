<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingCarrier;
use App\Models\ShippingService;

class ShippingCarrierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carriers = [
            [
                'code' => 'JNE',
                'name' => 'JNE Express',
                'tracking_url_template' => 'https://track.jne.co.id/?awb={tracking_number}',
                'is_active' => true,
                'services' => [
                    [
                        'code' => 'REG',
                        'name' => 'Layanan Reguler',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 3,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'YES',
                        'name' => 'Yakin Esok Sampai',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 1,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'OKE',
                        'name' => 'Ongkos Kirim Ekonomis',
                        'estimated_min_days' => 3,
                        'estimated_max_days' => 6,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'JTR',
                        'name' => 'JNE Trucking (Cargo)',
                        'estimated_min_days' => 3,
                        'estimated_max_days' => 7,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'code' => 'JNT',
                'name' => 'J&T Express',
                'tracking_url_template' => 'https://jet.co.id/track?awb={tracking_number}',
                'is_active' => true,
                'services' => [
                    [
                        'code' => 'EZ',
                        'name' => 'Reguler Service',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 3,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'ECO',
                        'name' => 'Economy Service',
                        'estimated_min_days' => 3,
                        'estimated_max_days' => 5,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'SUPER',
                        'name' => 'Super / Next Day',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 1,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'code' => 'SICEPAT',
                'name' => 'SiCepat Ekspres',
                'tracking_url_template' => 'https://www.sicepat.com/checkAwb/{tracking_number}',
                'is_active' => true,
                'services' => [
                    [
                        'code' => 'SIUNT',
                        'name' => 'SiUntung (Reguler)',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 2,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'BEST',
                        'name' => 'Besok Sampai Tujuan',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 1,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'GOKIL',
                        'name' => 'Cargo Kilat Minimal 10kg',
                        'estimated_min_days' => 3,
                        'estimated_max_days' => 6,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'HALO',
                        'name' => 'SiCepat HALO',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 3,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'code' => 'POS',
                'name' => 'POS Indonesia',
                'tracking_url_template' => 'https://www.posindonesia.co.id/id/tracking?awb={tracking_number}',
                'is_active' => true,
                'services' => [
                    [
                        'code' => 'POS_REG',
                        'name' => 'Pos Reguler',
                        'estimated_min_days' => 2,
                        'estimated_max_days' => 4,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'POS_NEXT',
                        'name' => 'Pos Nextday',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 1,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'POS_JUMBO',
                        'name' => 'Pos Jumbo Ekonomi',
                        'estimated_min_days' => 4,
                        'estimated_max_days' => 8,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'code' => 'ANTERAJA',
                'name' => 'Anteraja',
                'tracking_url_template' => 'https://anteraja.id/tracking?awb={tracking_number}',
                'is_active' => true,
                'services' => [
                    [
                        'code' => 'REG',
                        'name' => 'Anteraja Reguler',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 3,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'ND',
                        'name' => 'Anteraja Next Day',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 1,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'SD',
                        'name' => 'Anteraja Same Day',
                        'estimated_min_days' => 0,
                        'estimated_max_days' => 0,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'CARGO',
                        'name' => 'Anteraja Cargo',
                        'estimated_min_days' => 3,
                        'estimated_max_days' => 7,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'code' => 'TIKI',
                'name' => 'TIKI',
                'tracking_url_template' => 'https://www.tiki.id/id/tracking?awb={tracking_number}',
                'is_active' => true,
                'services' => [
                    [
                        'code' => 'REG',
                        'name' => 'Regular Service',
                        'estimated_min_days' => 2,
                        'estimated_max_days' => 3,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'ONS',
                        'name' => 'Over Night Service',
                        'estimated_min_days' => 1,
                        'estimated_max_days' => 1,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'ECO',
                        'name' => 'Economy Service',
                        'estimated_min_days' => 3,
                        'estimated_max_days' => 6,
                        'is_active' => true,
                    ],
                    [
                        'code' => 'SDS',
                        'name' => 'Same Day Service',
                        'estimated_min_days' => 0,
                        'estimated_max_days' => 0,
                        'is_active' => true,
                    ],
                ],
            ],
        ];

        foreach ($carriers as $carrierData) {
            $services = $carrierData['services'];
            unset($carrierData['services']);

            $carrier = ShippingCarrier::firstOrCreate(
                ['code' => $carrierData['code']],
                $carrierData
            );

            foreach ($services as $serviceData) {
                ShippingService::firstOrCreate(
                    [
                        'carrier_id' => $carrier->id,
                        'code' => $serviceData['code'],
                    ],
                    $serviceData
                );
            }
        }
    }
}
