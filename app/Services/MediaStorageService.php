<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorageService
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = (string) config('filesystems.media_disk', env('MEDIA_DISK', 'public'));
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function upload(UploadedFile $file, string $directory = 'products'): string
    {
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $path = trim($directory, '/') . '/' . $filename;

        Storage::disk($this->disk)->putFileAs(trim($directory, '/'), $file, $filename);

        return $path;
    }

    public function replace(?string $oldPath, UploadedFile $newFile, string $directory = 'products'): string
    {
        $this->delete($oldPath);

        return $this->upload($newFile, $directory);
    }

    public function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    public function url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $driver = config("filesystems.disks.{$this->disk}.driver", 'local');

        if ($driver === 'local') {
            if (! Storage::disk($this->disk)->exists($path)) {
                return null;
            }

            return asset('storage/' . ltrim($path, '/'));
        }

        // Object storage (S3 / R2): return direct URL without making remote HTTP HEAD request
        return Storage::disk($this->disk)->url($path);
    }
}
