<?php

namespace Tests\Unit\Services;

use App\Services\MediaStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaStorageServiceTest extends TestCase
{
    protected MediaStorageService $mediaService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config(['filesystems.media_disk' => 'public']);
        $this->mediaService = new MediaStorageService();
    }

    public function test_upload_stores_file_and_returns_relative_path(): void
    {
        $file = UploadedFile::fake()->image('test_product.jpg');

        $path = $this->mediaService->upload($file, 'products');

        $this->assertStringStartsWith('products/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_delete_removes_file_from_disk(): void
    {
        $file = UploadedFile::fake()->image('to_delete.png');
        $path = $this->mediaService->upload($file, 'products');

        Storage::disk('public')->assertExists($path);

        $deleted = $this->mediaService->delete($path);

        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_replace_deletes_old_file_and_uploads_new(): void
    {
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $oldPath = $this->mediaService->upload($oldFile, 'products');

        $newFile = UploadedFile::fake()->image('new.jpg');
        $newPath = $this->mediaService->replace($oldPath, $newFile, 'products');

        $this->assertNotEquals($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_url_returns_asset_path_when_file_exists(): void
    {
        $file = UploadedFile::fake()->image('sample.jpg');
        $path = $this->mediaService->upload($file, 'products');

        $url = $this->mediaService->url($path);

        $this->assertNotNull($url);
        $this->assertStringContainsString('storage/' . $path, $url);
    }

    public function test_url_returns_null_when_file_does_not_exist(): void
    {
        $url = $this->mediaService->url('products/nonexistent.jpg');

        $this->assertNull($url);
    }
}
