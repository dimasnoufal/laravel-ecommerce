<?php

namespace App\Console\Commands;

use App\Services\RegionImportService;
use Illuminate\Console\Command;

class ImportRegionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regions:import 
                            {--file= : Path to the CSV file (default: database/sc_csv_20260820110729_131_gjpenduduk_detil.csv)}
                            {--fresh : Wipe/truncate existing region tables before importing}
                            {--batch=1000 : Batch size for chunked database upserts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Indonesian administrative regions (Provinsi, Kab/Kota, Kecamatan, Desa/Kelurahan) from CSV';

    /**
     * Execute the console command.
     */
    public function handle(RegionImportService $service): int
    {
        $filePath = $this->option('file');
        $fresh = (bool) $this->option('fresh');
        $batchSize = (int) $this->option('batch') ?: 1000;

        $targetFile = $filePath ?: database_path('sc_csv_20260820110729_131_gjpenduduk_detil.csv');

        $this->info('=====================================================');
        $this->info(' Indonesian Administrative Regions CSV Importer');
        $this->info('=====================================================');
        $this->line("Target file : <comment>{$targetFile}</comment>");
        $this->line("Fresh mode  : " . ($fresh ? '<fg=red;options=bold>YES (truncate existing)</>' : '<info>NO (safe upsert)</info>'));
        $this->line("Batch size  : <comment>{$batchSize}</comment>");
        $this->newLine();

        if ($fresh && !$this->confirm('Are you sure you want to truncate existing provinces, regencies, districts, and villages?', true)) {
            $this->warn('Import cancelled by user.');
            return self::SUCCESS;
        }

        $progressBar = null;
        $currentStage = '';

        try {
            $stats = $service->import($targetFile, $fresh, $batchSize, function (string $stage, int $current, int $total) use (&$progressBar, &$currentStage) {
                if ($stage !== $currentStage) {
                    if ($progressBar) {
                        $progressBar->finish();
                        $this->newLine();
                    }
                    $currentStage = $stage;
                    $this->info($stage);
                    if ($total > 0) {
                        $progressBar = $this->output->createProgressBar($total);
                        $progressBar->start();
                    } else {
                        $progressBar = null;
                    }
                }

                if ($progressBar && $total > 0) {
                    $progressBar->setProgress($current);
                }
            });

            if ($progressBar) {
                $progressBar->finish();
                $this->newLine();
            }

            $this->newLine();
            $this->info('✓ Region import completed successfully!');
            $this->newLine();

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Rows Scanned', number_format($stats['total_rows_scanned'])],
                    ['Provinces (Provinsi)', number_format($stats['provinces_count'])],
                    ['Regencies / Cities (Kab/Kota)', number_format($stats['regencies_count'])],
                    ['Districts (Kecamatan)', number_format($stats['districts_count'])],
                    ['Villages (Desa / Kelurahan)', number_format($stats['villages_count'])],
                    ['Duplicate Codes Resolved', number_format($stats['duplicates_resolved'])],
                    ['Codes Repaired', number_format($stats['codes_repaired'])],
                    ['Skipped / Dummy Rows', number_format($stats['skipped_rows'])],
                    ['Elapsed Time', $stats['elapsed_seconds'] . ' seconds'],
                ]
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            if ($progressBar) {
                $this->newLine();
            }
            $this->error('An error occurred during region import: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            return self::FAILURE;
        }
    }
}
