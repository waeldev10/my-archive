<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Archives\Models\Archive;
use Modules\Auth\Models\User;

uses(RefreshDatabase::class);

// ─── Helper: Create an authenticated user ───────────────────────────────

function authenticatedUser(): array
{
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    return ['user' => $user, 'token' => $token];
}

function authHeaders(string $token): array
{
    return ['Authorization' => "Bearer {$token}"];
}

// ─── Create Archive ─────────────────────────────────────────────────────

it('can create an archive via API', function (): void {
    $auth = authenticatedUser();

    $response = $this->withHeaders(authHeaders($auth['token']))
        ->postJson('/api/v1/archives/note', [
            'title' => 'Test Note',
            'description' => 'This is a test note.',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id', 'type', 'title', 'description',
                'is_favorite', 'created_at',
            ],
        ]);

    expect(Archive::where('title', 'Test Note')->exists())->toBeTrue();
});

it('can create a link archive with URL', function (): void {
    $auth = authenticatedUser();

    $response = $this->withHeaders(authHeaders($auth['token']))
        ->postJson('/api/v1/archives/link', [
            'title' => 'Example Link',
            'url' => 'https://example.com',
        ]);

    $response->assertStatus(201);

    $archive = Archive::where('title', 'Example Link')->first();
    expect($archive)->not->toBeNull();
    expect($archive->type)->toBe('link');
});

it('fails archive creation without authentication', function (): void {
    $this->postJson('/api/v1/archives/note', [
        'title' => 'Unauthorized Note',
    ])->assertStatus(401);
});

it('fails archive creation with invalid type', function (): void {
    $auth = authenticatedUser();

    $this->withHeaders(authHeaders($auth['token']))
        ->postJson('/api/v1/archives/invalid-type', [
            'title' => 'Invalid',
        ])->assertStatus(422);
});

it('fails archive creation with missing title', function (): void {
    $auth = authenticatedUser();

    $this->withHeaders(authHeaders($auth['token']))
        ->postJson('/api/v1/archives/note', [
            'description' => 'Missing title',
        ])->assertStatus(422);
});

// ─── List Archives ──────────────────────────────────────────────────────

it('can list archives of a type via API', function (): void {
    $auth = authenticatedUser();
    $user = $auth['user'];

    Archive::factory()->count(3)->create([
        'user_id' => $user->id,
        'type' => 'note',
    ]);

    $response = $this->withHeaders(authHeaders($auth['token']))
        ->getJson('/api/v1/archives/note');

    $response->assertStatus(200)
        ->assertJsonStructure(['data', 'meta']);
});

it('only returns owned archives in list', function (): void {
    $auth = authenticatedUser();
    $otherUser = User::factory()->create();

    // Create archives for both users
    Archive::factory()->count(2)->create([
        'user_id' => $auth['user']->id,
        'type' => 'note',
    ]);
    Archive::factory()->count(3)->create([
        'user_id' => $otherUser->id,
        'type' => 'note',
    ]);

    $response = $this->withHeaders(authHeaders($auth['token']))
        ->getJson('/api/v1/archives/note');

    $response->assertStatus(200);
    expect(count($response->json('data')))->toBe(2);
});

it('returns empty list for type with no archives', function (): void {
    $auth = authenticatedUser();

    $response = $this->withHeaders(authHeaders($auth['token']))
        ->getJson('/api/v1/archives/book');

    $response->assertStatus(200);
    expect($response->json('data'))->toBe([]);
});

// ─── Show Archive ───────────────────────────────────────────────────────

it('can show a single archive via API', function (): void {
    $auth = authenticatedUser();
    $archive = Archive::factory()->create([
        'user_id' => $auth['user']->id,
        'type' => 'note',
        'title' => 'Specific Archive',
    ]);

    $response = $this->withHeaders(authHeaders($auth['token']))
        ->getJson("/api/v1/archives/note/{$archive->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Specific Archive');
});

it('cannot show another users archive', function (): void {
    $auth = authenticatedUser();
    $otherUser = User::factory()->create();

    $archive = Archive::factory()->create([
        'user_id' => $otherUser->id,
        'type' => 'note',
    ]);

    $this->withHeaders(authHeaders($auth['token']))
        ->getJson("/api/v1/archives/note/{$archive->id}")
        ->assertStatus(404); // Ownership scoped in repository — returns 404, not 403
});

it('returns 404 for non-existent archive', function (): void {
    $auth = authenticatedUser();

    $this->withHeaders(authHeaders($auth['token']))
        ->getJson('/api/v1/archives/note/nonexistent-id')
        ->assertStatus(404);
});

// ─── Update Archive ─────────────────────────────────────────────────────

it('can update an archive via API', function (): void {
    $auth = authenticatedUser();
    $archive = Archive::factory()->create([
        'user_id' => $auth['user']->id,
        'type' => 'note',
        'title' => 'Original Title',
    ]);

    $response = $this->withHeaders(authHeaders($auth['token']))
        ->putJson("/api/v1/archives/note/{$archive->id}", [
            'title' => 'Updated Title',
        ]);

    $response->assertStatus(200);
    expect($archive->fresh()->title)->toBe('Updated Title');
});

it('cannot update another users archive', function (): void {
    $auth = authenticatedUser();
    $otherUser = User::factory()->create();

    $archive = Archive::factory()->create([
        'user_id' => $otherUser->id,
        'type' => 'note',
    ]);

    $this->withHeaders(authHeaders($auth['token']))
        ->putJson("/api/v1/archives/note/{$archive->id}", [
            'title' => 'Hacked Title',
        ])->assertStatus(404); // Ownership scoped in repository
});

// ─── Delete / Restore / Force Delete ────────────────────────────────────

it('can soft-delete an archive', function (): void {
    $auth = authenticatedUser();
    $archive = Archive::factory()->create([
        'user_id' => $auth['user']->id,
        'type' => 'note',
    ]);

    $this->withHeaders(authHeaders($auth['token']))
        ->deleteJson("/api/v1/archives/note/{$archive->id}")
        ->assertStatus(200);

    expect($archive->fresh()->trashed())->toBeTrue();
});

it('can restore a soft-deleted archive', function (): void {
    $auth = authenticatedUser();
    $archive = Archive::factory()->create([
        'user_id' => $auth['user']->id,
        'type' => 'note',
    ]);
    $archive->delete();

    $this->withHeaders(authHeaders($auth['token']))
        ->postJson("/api/v1/archives/note/{$archive->id}/restore")
        ->assertStatus(200);

    expect($archive->fresh()->trashed())->toBeFalse();
});

it('can force-delete an archive', function (): void {
    $auth = authenticatedUser();
    $archive = Archive::factory()->create([
        'user_id' => $auth['user']->id,
        'type' => 'note',
    ]);
    $archive->delete();

    $this->withHeaders(authHeaders($auth['token']))
        ->deleteJson("/api/v1/archives/note/{$archive->id}/force")
        ->assertStatus(200);

    expect(Archive::withTrashed()->find($archive->id))->toBeNull();
});

// ─── Favorite Toggle ────────────────────────────────────────────────────

it('can toggle favorite on an archive', function (): void {
    $auth = authenticatedUser();
    $archive = Archive::factory()->create([
        'user_id' => $auth['user']->id,
        'type' => 'note',
        'is_favorite' => false,
    ]);

    // Toggle on
    $this->withHeaders(authHeaders($auth['token']))
        ->postJson("/api/v1/archives/note/{$archive->id}/favorite")
        ->assertStatus(200);

    expect($archive->fresh()->is_favorite)->toBeTrue();

    // Toggle off
    $this->withHeaders(authHeaders($auth['token']))
        ->postJson("/api/v1/archives/note/{$archive->id}/favorite")
        ->assertStatus(200);

    expect($archive->fresh()->is_favorite)->toBeFalse();
});
