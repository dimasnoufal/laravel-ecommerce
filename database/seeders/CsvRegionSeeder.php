<?php

namespace Database\Seeders;

use App\Services\RegionImportService;
use Illuminate\Database\Seeder;

class CsvRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(RegionImportService $service): void
    {
        $this->command->info('Starting Indonesian regions import from CSV...');

        $stats = $service->import(null, false, 1000, function (string $stage, int $current, int $total) {
            if ($total > 0 && ($current === 0 || $current === $total || $current % 5000 === 0)) {
                $this->command->line("{$stage} [{$current}/{$total}]");
            }
        });

        $this->command->info("Finished importing regions in {$stats['elapsed_seconds']}s.");
        $this->command->info("Provinces: {$stats['provinces_count']}, Regencies: {$stats['regencies_count']}, Districts: {$stats['districts_count']}, Villages: {$stats['villages_count']}");
    }
}
