<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Enums\RegencyType;
use App\Enums\VillageType;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Countries
        $indonesia = Country::firstOrCreate([
            'code' => 'ID'
        ], [
            'name' => 'Indonesia',
            'phone_code' => '62'
        ]);

        Country::firstOrCreate([
            'code' => 'MY'
        ], [
            'name' => 'Malaysia',
            'phone_code' => '60'
        ]);

        Country::firstOrCreate([
            'code' => 'SG'
        ], [
            'name' => 'Singapore',
            'phone_code' => '65'
        ]);

        // 2. Provinces
        $dki = Province::firstOrCreate([
            'country_id' => $indonesia->id,
            'code' => '31'
        ], [
            'name' => 'DKI Jakarta'
        ]);

        $jabar = Province::firstOrCreate([
            'country_id' => $indonesia->id,
            'code' => '32'
        ], [
            'name' => 'Jawa Barat'
        ]);

        $jateng = Province::firstOrCreate([
            'country_id' => $indonesia->id,
            'code' => '33'
        ], [
            'name' => 'Jawa Tengah'
        ]);

        $jatim = Province::firstOrCreate([
            'country_id' => $indonesia->id,
            'code' => '35'
        ], [
            'name' => 'Jawa Timur'
        ]);

        $bali = Province::firstOrCreate([
            'country_id' => $indonesia->id,
            'code' => '51'
        ], [
            'name' => 'Bali'
        ]);

        // 3. Regencies / Cities
        $jaksel = Regency::firstOrCreate([
            'province_id' => $dki->id,
            'code' => '3174'
        ], [
            'name' => 'Kota Jakarta Selatan',
            'type' => RegencyType::CITY->value
        ]);

        $jakpus = Regency::firstOrCreate([
            'province_id' => $dki->id,
            'code' => '3171'
        ], [
            'name' => 'Kota Jakarta Pusat',
            'type' => RegencyType::CITY->value
        ]);

        $bandung = Regency::firstOrCreate([
            'province_id' => $jabar->id,
            'code' => '3273'
        ], [
            'name' => 'Kota Bandung',
            'type' => RegencyType::CITY->value
        ]);

        $bogorKab = Regency::firstOrCreate([
            'province_id' => $jabar->id,
            'code' => '3201'
        ], [
            'name' => 'Kabupaten Bogor',
            'type' => RegencyType::REGENCY->value
        ]);

        $surabaya = Regency::firstOrCreate([
            'province_id' => $jatim->id,
            'code' => '3578'
        ], [
            'name' => 'Kota Surabaya',
            'type' => RegencyType::CITY->value
        ]);

        $denpasar = Regency::firstOrCreate([
            'province_id' => $bali->id,
            'code' => '5171'
        ], [
            'name' => 'Kota Denpasar',
            'type' => RegencyType::CITY->value
        ]);

        // 4. Districts
        $kebayoranBaru = District::firstOrCreate([
            'regency_id' => $jaksel->id,
            'code' => '317401'
        ], [
            'name' => 'Kebayoran Baru'
        ]);

        $cilandak = District::firstOrCreate([
            'regency_id' => $jaksel->id,
            'code' => '317406'
        ], [
            'name' => 'Cilandak'
        ]);

        $coblong = District::firstOrCreate([
            'regency_id' => $bandung->id,
            'code' => '327311'
        ], [
            'name' => 'Coblong'
        ]);

        $gubeng = District::firstOrCreate([
            'regency_id' => $surabaya->id,
            'code' => '357808'
        ], [
            'name' => 'Gubeng'
        ]);

        // 5. Villages
        Village::firstOrCreate([
            'district_id' => $kebayoranBaru->id,
            'code' => '3174011001'
        ], [
            'name' => 'Senayan',
            'type' => VillageType::URBAN_VILLAGE->value,
            'postal_code' => '12190'
        ]);

        Village::firstOrCreate([
            'district_id' => $kebayoranBaru->id,
            'code' => '3174011002'
        ], [
            'name' => 'Selong',
            'type' => VillageType::URBAN_VILLAGE->value,
            'postal_code' => '12110'
        ]);

        Village::firstOrCreate([
            'district_id' => $cilandak->id,
            'code' => '3174061001'
        ], [
            'name' => 'Cilandak Barat',
            'type' => VillageType::URBAN_VILLAGE->value,
            'postal_code' => '12430'
        ]);

        Village::firstOrCreate([
            'district_id' => $coblong->id,
            'code' => '3273111001'
        ], [
            'name' => 'Dago',
            'type' => VillageType::URBAN_VILLAGE->value,
            'postal_code' => '40135'
        ]);

        Village::firstOrCreate([
            'district_id' => $gubeng->id,
            'code' => '3578081001'
        ], [
            'name' => 'Gubeng',
            'type' => VillageType::URBAN_VILLAGE->value,
            'postal_code' => '60281'
        ]);
    }
}
