<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Archives\Models\Archive;
use Modules\Archives\Services\ArchiveService;
use Modules\Auth\Models\User;
use Modules\Dashboard\Models\ActivityLog;

uses(RefreshDatabase::class);

// ─── Activity Log Creation on Create ─────────────────────────────────────────

it('logs activity when an archive is created', function (): void {
    $user = User::factory()->create();
    $service = app(ArchiveService::class);

    $archive = $service->create($user, new \Modules\Archives\DTOs\ArchiveData(
        type: 'note',
        title: 'Activity Test Note',
    ));

    $log = ActivityLog::where('archive_id', $archive->id)->first();

    expect($log)->not->toBeNull();
    expect($log->action)->toBe('created');
    expect($log->user_id)->toBe($user->id);
    expect($log->description)->toContain('Activity Test Note');
});

// ─── Activity Log Creation on Update ─────────────────────────────────────────

it('logs activity when an archive is updated', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
        'title' => 'Original Title',
    ]);
    $service = app(ArchiveService::class);

    $service->update($archive, new \Modules\Archives\DTOs\ArchiveData(
        type: 'note',
        title: 'Updated Title',
    ));

    $log = ActivityLog::where('archive_id', $archive->id)
        ->where('action', 'updated')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->description)->toContain('Updated Title');
});

// ─── Activity Log Creation on Delete ─────────────────────────────────────────

it('logs activity when an archive is soft-deleted', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);
    $service = app(ArchiveService::class);

    $service->delete($archive);

    $log = ActivityLog::where('archive_id', $archive->id)
        ->where('action', 'deleted')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->action)->toBe('deleted');
    expect($log->description)->toContain('moved to trash');
});

// ─── Activity Log Creation on Restore ────────────────────────────────────────

it('logs activity when an archive is restored', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);
    $archive->delete();
    $service = app(ArchiveService::class);

    $service->restore($user, $archive->id);

    $log = ActivityLog::where('archive_id', $archive->id)
        ->where('action', 'restored')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->description)->toContain('restored from trash');
});

// ─── Activity Log Creation on Favorite Toggle ────────────────────────────────

it('logs activity when an archive is favorited', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
        'is_favorite' => false,
    ]);
    $service = app(ArchiveService::class);

    $service->toggleFavorite($archive);

    $log = ActivityLog::where('archive_id', $archive->id)
        ->where('action', 'favorited')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->description)->toContain('added to favorites');
});

it('logs activity when an archive is unfavorited', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $user->id,
        'type' => 'note',
        'is_favorite' => true,
    ]);
    $service = app(ArchiveService::class);

    $service->toggleFavorite($archive);

    $log = ActivityLog::where('archive_id', $archive->id)
        ->where('action', 'unfavorited')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->description)->toContain('removed from favorites');
});

// ─── Activity Log Has Correct Timestamp ──────────────────────────────────────

it('sets created_at automatically on activity logs', function (): void {
    $user = User::factory()->create();
    $service = app(ArchiveService::class);

    $archive = $service->create($user, new \Modules\Archives\DTOs\ArchiveData(
        type: 'note',
        title: 'Timestamp Test',
    ));

    $log = ActivityLog::where('archive_id', $archive->id)->first();

    expect($log->created_at)->not->toBeNull();
    expect($log->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

// ─── Activity Log Does Not Have Updated At ───────────────────────────────────

it('does not set updated_at on activity logs (immutable)', function (): void {
    $user = User::factory()->create();
    $service = app(ArchiveService::class);

    $archive = $service->create($user, new \Modules\Archives\DTOs\ArchiveData(
        type: 'note',
        title: 'Immutable Test',
    ));

    $log = ActivityLog::where('archive_id', $archive->id)->first();

    expect($log->updated_at)->toBeNull();
});
