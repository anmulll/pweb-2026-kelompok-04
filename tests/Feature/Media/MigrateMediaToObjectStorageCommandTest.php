<?php

namespace Tests\Feature\Media;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MigrateMediaToObjectStorageCommandTest extends TestCase
{
    public function test_dry_run_command_simulates_without_uploading(): void
    {
        Storage::fake('public');
        Storage::fake('r2');

        Storage::disk('public')->put('products/sample.jpg', 'fake-image-bytes');

        $this->artisan('media:migrate-to-cloud --dry-run --target=r2')
            ->expectsOutputToContain('Mode DRY-RUN aktif')
            ->expectsOutputToContain('[DRY-RUN] Akan dimigrasi: products/sample.jpg -> r2:/products/sample.jpg')
            ->assertExitCode(0);

        Storage::disk('r2')->assertMissing('products/sample.jpg');
    }

    public function test_real_migration_uploads_files_to_target_cloud_disk(): void
    {
        Storage::fake('public');
        Storage::fake('r2');

        Storage::disk('public')->put('products/sample_product.jpg', 'image-content-here');
        Storage::disk('public')->put('promotions/sample_promo.jpg', 'promo-banner-content');

        $this->artisan('media:migrate-to-cloud --target=r2')
            ->expectsOutputToContain('Berhasil migrasi: products/sample_product.jpg')
            ->expectsOutputToContain('Berhasil migrasi: promotions/sample_promo.jpg')
            ->assertExitCode(0);

        Storage::disk('r2')->assertExists('products/sample_product.jpg');
        Storage::disk('r2')->assertExists('promotions/sample_promo.jpg');
        $this->assertEquals('image-content-here', Storage::disk('r2')->get('products/sample_product.jpg'));
    }
}
