<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Archives\DTOs\ArchiveData;
use Modules\Archives\Models\Archive;
use Modules\Archives\Services\ArchiveService;
use Modules\Auth\Models\User;

uses(RefreshDatabase::class);

// ─── Create ────────────────────────────────────────────────────────────────

it('can create an archive via service', function (): void {
    $user = User::factory()->create();
    $service = app(ArchiveService::class);

    $archive = $service->create($user, new ArchiveData(
        type: 'note',
        title: 'Test Note',
        description: 'A test note',
        isFavorite: false,
        tags: [],
    ));

    expect($archive)->toBeInstanceOf(Archive::class);
    expect($archive->title)->toBe('Test Note');
    expect($archive->type)->toBe('note');
});

it('can create an archive with tags', function (): void {
    $user = User::factory()->create();
    $service = app(ArchiveService::class);

    $archive = $service->create($user, new ArchiveData(
        type: 'note',
        title: 'Tagged Note',
        description: null,
        isFavorite: false,
        tags: ['php', 'laravel', 'testing'],
    ));

    expect($archive->tags->count())->toBe(3);
    expect($archive->tags->pluck('name')->toArray())->toBe(['php', 'laravel', 'testing']);
});

it('can create an archive with extension data', function (): void {
    $user = User::factory()->create();
    $service = app(ArchiveService::class);

    $archive = $service->create($user, new ArchiveData(
        type: 'link',
        title: 'Example Link',
        description: null,
        isFavorite: false,
        tags: [],
        typeData: ['url' => 'https://example.com', 'domain' => 'example.com'],
    ));

    expect($archive->type)->toBe('link');
    $extension = $archive->extension()->first();
    expect($extension)->not->toBeNull();
    expect($extension->url)->toBe('https://example.com');
});

// ─── Find ──────────────────────────────────────────────────────────────────

it('can find an archive owned by the user', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);
    $service = app(ArchiveService::class);

    $found = $service->find($user, $archive->id);

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($archive->id);
});

it('returns null when finding another users archive', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $other->id,
        'type' => 'note',
    ]);
    $service = app(ArchiveService::class);

    $found = $service->find($user, $archive->id);

    expect($found)->toBeNull();
});

// ─── Update ────────────────────────────────────────────────────────────────

it('can update an archive via service', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
        'title' => 'Original',
    ]);
    $service = app(ArchiveService::class);

    $service->update($archive, new ArchiveData(
        type: 'note',
        title: 'Updated Title',
        description: 'Updated description',
        isFavorite: true,
        tags: [],
    ));

    expect($archive->fresh()->title)->toBe('Updated Title');
    expect($archive->fresh()->is_favorite)->toBeTrue();
});

// ─── Delete ────────────────────────────────────────────────────────────────

it('can soft-delete an archive via service', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);
    $service = app(ArchiveService::class);

    $service->delete($archive);

    expect($archive->fresh()->trashed())->toBeTrue();
});

it('can restore a soft-deleted archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);
    $archive->delete();
    $service = app(ArchiveService::class);

    $restored = $service->restore($user, $archive->id);

    expect($restored)->not->toBeNull();
    expect($restored->trashed())->toBeFalse();
});

it('returns null when restoring non-existent archive', function (): void {
    $user = User::factory()->create();
    $service = app(ArchiveService::class);

    $result = $service->restore($user, 'non-existent-id');

    expect($result)->toBeNull();
});

// ─── Toggle Favorite ───────────────────────────────────────────────────────

it('can toggle favorite on an archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
        'is_favorite' => false,
    ]);
    $service = app(ArchiveService::class);

    $result = $service->toggleFavorite($archive);

    expect($result['is_favorite'])->toBeTrue();
    expect($archive->fresh()->is_favorite)->toBeTrue();

    // Toggle back
    $result = $service->toggleFavorite($archive->fresh());

    expect($result['is_favorite'])->toBeFalse();
});

// ─── List ──────────────────────────────────────────────────────────────────

it('can list archives for a user', function (): void {
    $user = User::factory()->create();
    Archive::factory()->count(3)->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);
    $service = app(ArchiveService::class);

    $archives = $service->list($user, 'note');

    expect($archives->total())->toBe(3);
});

it('only returns owned archives when listing', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Archive::factory()->count(2)->create(['user_id' => $user->id, 'type' => 'note']);
    Archive::factory()->count(3)->create(['user_id' => $other->id, 'type' => 'note']);
    $service = app(ArchiveService::class);

    $archives = $service->list($user, 'note');

    expect($archives->total())->toBe(2);
});

// ─── Trashed ───────────────────────────────────────────────────────────────

it('can list trashed archives', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);
    $archive->delete();
    $service = app(ArchiveService::class);

    $trashed = $service->trashed($user, 'note');

    expect($trashed->total())->toBe(1);
});
