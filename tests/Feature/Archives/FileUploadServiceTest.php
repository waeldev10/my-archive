<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Archives\Models\Archive;
use Modules\Archives\Services\FileUploadService;
use Modules\Auth\Models\User;

uses(RefreshDatabase::class);

// ─── uploadImage ─────────────────────────────────────────────────────────

it('can upload an image and return the stored path', function (): void {
    Storage::fake('local');
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->image('photo.jpg');
    $service = new FileUploadService;

    $path = $service->uploadImage($file, $user, $archive);

    expect($path)->toBeString();
    expect($path)->toContain("archives/{$user->id}/images/{$archive->id}.");
    Storage::disk('local')->assertExists($path);
});

it('uploads image with correct file extension', function (): void {
    Storage::fake('local');
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->image('photo.png');
    $service = new FileUploadService;

    $path = $service->uploadImage($file, $user, $archive);

    expect($path)->toEndWith('.png');
});

// ─── uploadFile ──────────────────────────────────────────────────────────

it('can upload a generic file and return the stored path', function (): void {
    Storage::fake('local');
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->create('document.pdf', 100);
    $service = new FileUploadService;

    $path = $service->uploadFile($file, $user, $archive);

    expect($path)->toBeString();
    expect($path)->toContain("archives/{$user->id}/files/{$archive->id}.");
    Storage::disk('local')->assertExists($path);
});

it('uploads file with correct extension', function (): void {
    Storage::fake('local');
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->create('notes.txt', 50);
    $service = new FileUploadService;

    $path = $service->uploadFile($file, $user, $archive);

    expect($path)->toEndWith('.txt');
});

// ─── delete ──────────────────────────────────────────────────────────────

it('deletes a stored file and returns true', function (): void {
    Storage::fake('local');
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->create('delete-me.pdf', 100);
    $service = new FileUploadService;
    $path = $service->uploadFile($file, $user, $archive);

    $result = $service->delete($path);

    expect($result)->toBeTrue();
    Storage::disk('local')->assertMissing($path);
});

it('returns false when deleting a non-existent file', function (): void {
    Storage::fake('local');
    $service = new FileUploadService;

    $result = $service->delete('non-existent/path/file.pdf');

    expect($result)->toBeFalse();
});

// ─── url ─────────────────────────────────────────────────────────────────

it('can generate a URL for a stored file', function (): void {
    Storage::fake('local');
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->create('readme.pdf', 100);
    $service = new FileUploadService;
    $path = $service->uploadFile($file, $user, $archive);

    $url = $service->url($path);

    expect($url)->toBeString();
    expect($url)->not->toBeEmpty();
});

// ─── validation rules (static) ───────────────────────────────────────────

it('returns image validation rules with correct structure', function (): void {
    $rules = FileUploadService::imageValidationRules();

    expect($rules)->toHaveKey('file');
    expect($rules['file'])->toContain('required');
    expect($rules['file'])->toContain('image');
});

it('returns file validation rules with correct structure', function (): void {
    $rules = FileUploadService::fileValidationRules();

    expect($rules)->toHaveKey('file');
    expect($rules['file'])->toContain('required');
    expect($rules['file'])->toContain('file');
});
