<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Archives\Models\Archive;
use Modules\Archives\Policies\ArchivePolicy;
use Modules\Auth\Models\User;

uses(RefreshDatabase::class);

// ─── Helpers ────────────────────────────────────────────────────────────

function createPolicy(): ArchivePolicy
{
    return new ArchivePolicy;
}

// ─── viewAny ─────────────────────────────────────────────────────────────

it('allows any authenticated user to view any archive index', function (): void {
    $user = User::factory()->create();

    $result = createPolicy()->viewAny($user);

    expect($result)->toBeTrue();
});

// ─── view ────────────────────────────────────────────────────────────────

it('allows owner to view their archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);

    $result = createPolicy()->view($user, $archive);

    expect($result)->toBeTrue();
});

it('denies non-owner from viewing another users archive', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $other->id]);

    $result = createPolicy()->view($user, $archive);

    expect($result)->toBeFalse();
});

// ─── create ──────────────────────────────────────────────────────────────

it('allows any authenticated user to create archives', function (): void {
    $user = User::factory()->create();

    $result = createPolicy()->create($user);

    expect($result)->toBeTrue();
});

// ─── update ──────────────────────────────────────────────────────────────

it('allows owner to update their archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);

    $result = createPolicy()->update($user, $archive);

    expect($result)->toBeTrue();
});

it('denies non-owner from updating another users archive', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $other->id]);

    $result = createPolicy()->update($user, $archive);

    expect($result)->toBeFalse();
});

// ─── delete ──────────────────────────────────────────────────────────────

it('allows owner to delete their archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);

    $result = createPolicy()->delete($user, $archive);

    expect($result)->toBeTrue();
});

it('denies non-owner from deleting another users archive', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $other->id]);

    $result = createPolicy()->delete($user, $archive);

    expect($result)->toBeFalse();
});

// ─── restore ─────────────────────────────────────────────────────────────

it('allows owner to restore their archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);

    $result = createPolicy()->restore($user, $archive);

    expect($result)->toBeTrue();
});

it('denies non-owner from restoring another users archive', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $other->id]);

    $result = createPolicy()->restore($user, $archive);

    expect($result)->toBeFalse();
});

// ─── forceDelete ─────────────────────────────────────────────────────────

it('allows owner to force-delete their archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);

    $result = createPolicy()->forceDelete($user, $archive);

    expect($result)->toBeTrue();
});

it('denies non-owner from force-deleting another users archive', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $other->id]);

    $result = createPolicy()->forceDelete($user, $archive);

    expect($result)->toBeFalse();
});

// ─── toggleFavorite ──────────────────────────────────────────────────────

it('allows owner to toggle favorite on their archive', function (): void {
    $user = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $user->id]);

    $result = createPolicy()->toggleFavorite($user, $archive);

    expect($result)->toBeTrue();
});

it('denies non-owner from toggling favorite on another users archive', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $archive = Archive::factory()->create(['user_id' => $other->id]);

    $result = createPolicy()->toggleFavorite($user, $archive);

    expect($result)->toBeFalse();
});
