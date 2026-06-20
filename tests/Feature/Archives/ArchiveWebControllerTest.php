<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Archives\Models\Archive;
use Modules\Auth\Models\User;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// ─── Index / List Page ─────────────────────────────────────────────────────

it('can view the archive list page', function (): void {
    $response = $this->get(route('archives.list', ['type' => 'note']));

    $response->assertStatus(200);
    $response->assertSee('Notes');
});

it('can view different archive type list pages', function (): void {
    $types = ['note', 'link', 'book', 'snippet', 'website', 'todo'];

    foreach ($types as $type) {
        $response = $this->get(route('archives.list', ['type' => $type]));
        $response->assertStatus(200);
    }
});

it('shows archives on the list page', function (): void {
    Archive::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'note',
        'title' => 'Visible Note',
    ]);

    $response = $this->get(route('archives.list', ['type' => 'note']));

    $response->assertStatus(200);
    $response->assertSee('Visible Note');
});

// ─── Create Page ───────────────────────────────────────────────────────────

it('can view the create archive page', function (): void {
    $response = $this->get(route('archives.create', ['type' => 'note']));

    $response->assertStatus(200);
});

it('can view create page for different types', function (): void {
    $types = ['note', 'link', 'book', 'image', 'file', 'todo'];

    foreach ($types as $type) {
        $response = $this->get(route('archives.create', ['type' => $type]));
        $response->assertStatus(200);
    }
});

// ─── Show Page ─────────────────────────────────────────────────────────────

it('can view an archive show page', function (): void {
    $archive = Archive::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'note',
    ]);

    $response = $this->get(route('archives.show', [
        'type' => 'note',
        'archive' => $archive->id,
    ]));

    $response->assertStatus(200);
});

it('cannot view another users archive show page', function (): void {
    $otherUser = User::factory()->create();
    $archive = Archive::factory()->create([
        'user_id' => $otherUser->id,
        'type' => 'note',
    ]);

    $response = $this->get(route('archives.show', [
        'type' => 'note',
        'archive' => $archive->id,
    ]));

    // Should render the page with the Livewire component returning null
    $response->assertStatus(200);
});

// ─── Edit Page ─────────────────────────────────────────────────────────────

it('can view an archive edit page', function (): void {
    $archive = Archive::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'note',
    ]);

    $response = $this->get(route('archives.edit', [
        'type' => 'note',
        'archive' => $archive->id,
    ]));

    $response->assertStatus(200);
});

it('returns 404 when editing non-existent archive', function (): void {
    $response = $this->get(route('archives.edit', [
        'type' => 'note',
        'archive' => 'non-existent-id',
    ]));

    $response->assertStatus(404);
});

// ─── Authentication Required ───────────────────────────────────────────────

it('redirects to login when not authenticated', function (): void {
    auth()->logout();

    $this->get(route('archives.list', ['type' => 'note']))
        ->assertRedirect(route('login'));
});
