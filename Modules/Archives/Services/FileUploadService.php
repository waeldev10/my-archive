<?php

declare(strict_types=1);

namespace Modules\Archives\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Archives\Models\Archive;
use Modules\Auth\Models\User;

class FileUploadService
{
    /**
     * The disk to store files on.
     */
    private string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default', 'local');
    }

    /**
     * Upload an image file for an archive.
     *
     * @return string The stored file path
     */
    public function uploadImage(UploadedFile $file, User $user, Archive $archive): string
    {
        $path = $file->storeAs(
            "archives/{$user->id}/images",
            "{$archive->id}.{$file->extension()}",
            $this->disk,
        );

        return $path;
    }

    /**
     * Upload a generic file for an archive.
     *
     * @return string The stored file path
     */
    public function uploadFile(UploadedFile $file, User $user, Archive $archive): string
    {
        $path = $file->storeAs(
            "archives/{$user->id}/files",
            "{$archive->id}.{$file->extension()}",
            $this->disk,
        );

        return $path;
    }

    /**
     * Delete a stored file.
     */
    public function delete(string $path): bool
    {
        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Get the full URL for a stored file.
     */
    public function url(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Validate an image upload.
     *
     * @return array<string, mixed>
     */
    public static function imageValidationRules(): array
    {
        return [
            'file' => ['required', 'file', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'],
        ];
    }

    /**
     * Validate a file upload.
     *
     * @return array<string, mixed>
     */
    public static function fileValidationRules(): array
    {
        return [
            'file' => ['required', 'file', 'max:25600'], // 25 MB
        ];
    }
}
