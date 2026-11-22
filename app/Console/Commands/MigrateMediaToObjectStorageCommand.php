<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateMediaToObjectStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:migrate-to-cloud 
                            {--dry-run : Simulasi migrasi tanpa mengunggah file nyata}
                            {--target=r2 : Disk target penyimpanan cloud (r2 atau s3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasikan seluruh media lokal (products/ dan promotions/) ke Object Storage cloud (R2/S3)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $targetDisk = (string) $this->option('target');
        $sourceDisk = 'public';

        $this->info("Memulai proses migrasi media dari disk '{$sourceDisk}' ke disk cloud '{$targetDisk}'...");

        if ($dryRun) {
            $this->warn('Mode DRY-RUN aktif: file tidak akan diunggah atau diubah.');
        }

        $directories = ['products', 'promotions'];
        $totalMigrated = 0;
        $totalSkipped = 0;

        foreach ($directories as $dir) {
            $files = Storage::disk($sourceDisk)->allFiles($dir);

            if (empty($files)) {
                $this->line("Tidak ada file ditemukan di direktori '{$dir}'.");
                continue;
            }

            $this->info("Menemukan " . count($files) . " file pada direktori '{$dir}'.");

            foreach ($files as $file) {
                if ($dryRun) {
                    $this->line("[DRY-RUN] Akan dimigrasi: {$file} -> {$targetDisk}:/{$file}");
                    $totalMigrated++;
                    continue;
                }

                try {
                    $content = Storage::disk($sourceDisk)->get($file);
                    Storage::disk($targetDisk)->put($file, $content);
                    $this->info("Berhasil migrasi: {$file}");
                    $totalMigrated++;
                } catch (\Throwable $e) {
                    $this->error("Gagal migrasi {$file}: " . $e->getMessage());
                    $totalSkipped++;
                }
            }
        }

        $this->newLine();
        $this->info("Selesai! Total file termigrasi: {$totalMigrated}, Gagal: {$totalSkipped}");

        return Command::SUCCESS;
    }
}
