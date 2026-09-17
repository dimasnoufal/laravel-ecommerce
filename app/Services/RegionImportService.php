<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Enums\RegencyType;
use App\Enums\VillageType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegionImportService
{
    /**
     * Import regions from CSV file.
     *
     * @param string|null $filePath
     * @param bool $fresh
     * @param int $batchSize
     * @param callable|null $progressCallback fn(string $stage, int $current, int $total)
     * @return array
     */
    public function import(?string $filePath = null, bool $fresh = false, int $batchSize = 1000, ?callable $progressCallback = null): array
    {
        $startTime = microtime(true);
        $filePath = $filePath ?: database_path('sc_csv_20260820110729_131_gjpenduduk_detil.csv');

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("CSV file not found at: {$filePath}");
        }

        // 1. If fresh requested, truncate in cascade order
        if ($fresh) {
            $this->reportProgress($progressCallback, 'Truncating existing region tables...', 0, 1);
            DB::statement('TRUNCATE TABLE villages, districts, regencies, provinces CASCADE;');
        }

        // 2. Ensure Indonesia exists
        $this->reportProgress($progressCallback, 'Checking default country (Indonesia)...', 0, 1);
        $indonesia = Country::firstOrCreate([
            'code' => 'ID',
        ], [
            'name' => 'Indonesia',
            'phone_code' => '62',
        ]);

        // 3. Parse CSV in stream
        $this->reportProgress($progressCallback, 'Reading CSV file...', 0, 100);
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Unable to open CSV file at: {$filePath}");
        }

        $rawProvinces = [];
        $rawRegencies = [];
        $rawDistricts = [];
        $rawVillages = [];

        $skippedRows = [];
        $duplicateCodeCount = 0;
        $seenVillageCodes = [];
        $repairedCodeCount = 0;
        $rowCount = 0;

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $rowCount++;
            $provRaw = trim($data[0] ?? '');
            $regRaw = trim($data[1] ?? '');
            $distRaw = trim($data[2] ?? '');
            $typeRaw = trim($data[3] ?? '');
            $codeRaw = trim($data[4] ?? '');
            $nameRaw = trim($data[5] ?? '');

            // Skip empty or dummy rows
            if (empty($provRaw) || empty($regRaw) || empty($distRaw) || empty($nameRaw) || strtoupper($nameRaw) === 'DUMMY') {
                $skippedRows[] = ['row' => $rowCount, 'reason' => 'empty_or_dummy', 'data' => $data];
                continue;
            }

            // Normalization
            $provName = $this->normalizeProvinceName($provRaw);
            [$regName, $regType] = $this->normalizeRegency($regRaw);
            $distName = $this->normalizeDistrictName($distRaw);
            $vilName = $this->normalizeVillageName($nameRaw);
            $vilType = (strtoupper($typeRaw) === 'KELURAHAN') ? VillageType::URBAN_VILLAGE->value : VillageType::VILLAGE->value;

            // Handle village code
            $vilCode = $codeRaw;
            if (strlen($vilCode) !== 10 || !ctype_digit($vilCode)) {
                // Check if it's a 4-digit village suffix
                if (strlen($vilCode) === 4 && ctype_digit($vilCode)) {
                    // Try to deduce district code from known districts or province
                    $repaired = false;
                    foreach ($rawDistricts as $dCode => $dInfo) {
                        if ($dInfo['name'] === $distName && $rawRegencies[$dInfo['regency_code']]['name'] === $regName) {
                            $vilCode = $dCode . $vilCode;
                            $repaired = true;
                            $repairedCodeCount++;
                            break;
                        }
                    }
                    if (!$repaired) {
                        $skippedRows[] = ['row' => $rowCount, 'reason' => 'invalid_code', 'code' => $codeRaw, 'data' => array_slice($data, 0, 6)];
                        continue;
                    }
                } else {
                    $skippedRows[] = ['row' => $rowCount, 'reason' => 'invalid_code', 'code' => $codeRaw, 'data' => array_slice($data, 0, 6)];
                    continue;
                }
            }

            $pCode = substr($vilCode, 0, 2);
            $rCode = substr($vilCode, 0, 4);
            $dCode = substr($vilCode, 0, 6);

            // Special check: ensure province code matches established code
            if (!isset($rawProvinces[$pCode])) {
                $rawProvinces[$pCode] = $provName;
            }

            // Regency
            if (!isset($rawRegencies[$rCode])) {
                $rawRegencies[$rCode] = [
                    'province_code' => $pCode,
                    'name' => $regName,
                    'type' => $regType,
                ];
            }

            // District
            if (!isset($rawDistricts[$dCode])) {
                $rawDistricts[$dCode] = [
                    'regency_code' => $rCode,
                    'name' => $distName,
                ];
            }

            // Village duplicate check in same district
            $uniqueKey = $dCode . '_' . $vilCode;
            if (isset($seenVillageCodes[$uniqueKey])) {
                $seenVillageCodes[$uniqueKey]++;
                $vilCode = $vilCode . '-' . $seenVillageCodes[$uniqueKey];
                $duplicateCodeCount++;
            } else {
                $seenVillageCodes[$uniqueKey] = 1;
            }

            $rawVillages[] = [
                'district_code' => $dCode,
                'code' => $vilCode,
                'name' => $vilName,
                'type' => $vilType,
            ];
        }
        fclose($handle);

        $now = now();

        // 4. Upsert Provinces
        $totalProvs = count($rawProvinces);
        $this->reportProgress($progressCallback, "Importing {$totalProvs} Provinces...", 0, $totalProvs);
        $provData = [];
        foreach ($rawProvinces as $pCode => $pName) {
            $provData[] = [
                'country_id' => $indonesia->id,
                'code' => $pCode,
                'name' => $pName,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('provinces')->upsert(
            $provData,
            ['country_id', 'code'],
            ['name', 'updated_at']
        );
        $this->reportProgress($progressCallback, "Provinces imported.", $totalProvs, $totalProvs);

        // Fetch Province ID mapping
        $provinceIdMap = Province::where('country_id', $indonesia->id)->pluck('id', 'code')->toArray();

        // 5. Upsert Regencies
        $totalRegs = count($rawRegencies);
        $this->reportProgress($progressCallback, "Importing {$totalRegs} Regencies / Cities...", 0, $totalRegs);
        $regData = [];
        foreach ($rawRegencies as $rCode => $rInfo) {
            $pId = $provinceIdMap[$rInfo['province_code']] ?? null;
            if (!$pId) {
                continue;
            }
            $regData[] = [
                'province_id' => $pId,
                'code' => $rCode,
                'name' => $rInfo['name'],
                'type' => $rInfo['type'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('regencies')->upsert(
            $regData,
            ['province_id', 'code'],
            ['name', 'type', 'updated_at']
        );
        $this->reportProgress($progressCallback, "Regencies / Cities imported.", $totalRegs, $totalRegs);

        // Fetch Regency ID mapping
        $regencyIdMap = Regency::pluck('id', 'code')->toArray();

        // 6. Upsert Districts in chunks
        $totalDists = count($rawDistricts);
        $this->reportProgress($progressCallback, "Importing {$totalDists} Districts...", 0, $totalDists);
        $distData = [];
        foreach ($rawDistricts as $dCode => $dInfo) {
            $rId = $regencyIdMap[$dInfo['regency_code']] ?? null;
            if (!$rId) {
                continue;
            }
            $distData[] = [
                'regency_id' => $rId,
                'code' => $dCode,
                'name' => $dInfo['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $distChunks = array_chunk($distData, $batchSize);
        $distProcessed = 0;
        foreach ($distChunks as $chunk) {
            DB::table('districts')->upsert(
                $chunk,
                ['regency_id', 'code'],
                ['name', 'updated_at']
            );
            $distProcessed += count($chunk);
            $this->reportProgress($progressCallback, "Importing Districts...", $distProcessed, $totalDists);
        }

        // Fetch District ID mapping
        $districtIdMap = District::pluck('id', 'code')->toArray();

        // 7. Upsert Villages in chunks
        $totalVils = count($rawVillages);
        $this->reportProgress($progressCallback, "Importing {$totalVils} Villages...", 0, $totalVils);
        $vilData = [];
        $vilProcessed = 0;

        foreach ($rawVillages as $vInfo) {
            $dId = $districtIdMap[$vInfo['district_code']] ?? null;
            if (!$dId) {
                continue;
            }
            $vilData[] = [
                'district_id' => $dId,
                'code' => $vInfo['code'],
                'name' => $vInfo['name'],
                'type' => $vInfo['type'],
                'postal_code' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($vilData) >= $batchSize) {
                DB::table('villages')->upsert(
                    $vilData,
                    ['district_id', 'code'],
                    ['name', 'type', 'updated_at']
                );
                $vilProcessed += count($vilData);
                $this->reportProgress($progressCallback, "Importing Villages...", $vilProcessed, $totalVils);
                $vilData = [];
            }
        }

        if (!empty($vilData)) {
            DB::table('villages')->upsert(
                $vilData,
                ['district_id', 'code'],
                ['name', 'type', 'updated_at']
            );
            $vilProcessed += count($vilData);
            $this->reportProgress($progressCallback, "Importing Villages...", $vilProcessed, $totalVils);
        }

        $duration = microtime(true) - $startTime;

        return [
            'total_rows_scanned' => $rowCount,
            'provinces_count' => count($rawProvinces),
            'regencies_count' => count($rawRegencies),
            'districts_count' => count($rawDistricts),
            'villages_count' => $vilProcessed,
            'duplicates_resolved' => $duplicateCodeCount,
            'codes_repaired' => $repairedCodeCount,
            'skipped_rows' => count($skippedRows),
            'elapsed_seconds' => round($duration, 2),
        ];
    }

    /**
     * Normalize Province Name.
     */
    public function normalizeProvinceName(string $name): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $name));
        $upper = strtoupper($clean);

        if ($upper === 'P A P U A' || $upper === 'PAPUA') {
            return 'Papua';
        }
        if ($upper === 'DKI JAKARTA') {
            return 'DKI Jakarta';
        }
        if ($upper === 'DAERAH ISTIMEWA YOGYAKARTA' || $upper === 'DI YOGYAKARTA') {
            return 'DI Yogyakarta';
        }
        if ($upper === 'KEPULAUAN BANGKA BELITUNG') {
            return 'Kepulauan Bangka Belitung';
        }
        if ($upper === 'KEPULAUAN RIAU') {
            return 'Kepulauan Riau';
        }

        return mb_convert_case($clean, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Normalize Regency Name & determine type.
     */
    public function normalizeRegency(string $name): array
    {
        $clean = trim(preg_replace('/\s+/', ' ', $name));
        $upper = strtoupper($clean);

        if (str_starts_with($upper, 'KOTA ADM.')) {
            $regName = 'Kota Administrasi ' . mb_convert_case(trim(substr($clean, 9)), MB_CASE_TITLE, 'UTF-8');
            return [$regName, RegencyType::CITY->value];
        }

        if (str_starts_with($upper, 'KOTA')) {
            $regName = 'Kota ' . mb_convert_case(trim(substr($clean, 4)), MB_CASE_TITLE, 'UTF-8');
            return [$regName, RegencyType::CITY->value];
        }

        if (str_starts_with($upper, 'KAB.')) {
            $regName = 'Kabupaten ' . mb_convert_case(trim(substr($clean, 4)), MB_CASE_TITLE, 'UTF-8');
            return [$regName, RegencyType::REGENCY->value];
        }

        if (str_starts_with($upper, 'KABUPATEN')) {
            $regName = 'Kabupaten ' . mb_convert_case(trim(substr($clean, 9)), MB_CASE_TITLE, 'UTF-8');
            return [$regName, RegencyType::REGENCY->value];
        }

        return [mb_convert_case($clean, MB_CASE_TITLE, 'UTF-8'), RegencyType::REGENCY->value];
    }

    /**
     * Normalize District Name.
     */
    public function normalizeDistrictName(string $name): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $name));
        return mb_convert_case($clean, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Normalize Village Name.
     */
    public function normalizeVillageName(string $name): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $name));
        return mb_convert_case($clean, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Helper to invoke progress reporting.
     */
    protected function reportProgress(?callable $callback, string $stage, int $current, int $total): void
    {
        if ($callback) {
            $callback($stage, $current, $total);
        }
    }
}
