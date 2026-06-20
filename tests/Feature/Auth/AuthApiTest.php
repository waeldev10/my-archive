<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Auth\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

// ─── Registration ───────────────────────────────────────────────────────

it('can register a new user via API', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token',
        ]);

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

it('fails registration with invalid data', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'not-matching',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('fails registration with duplicate email', function (): void {
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Another User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('sends verification email on registration', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(201);

    $user = User::where('email', 'test@example.com')->first();
    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

// ─── Login ──────────────────────────────────────────────────────────────

it('can login with valid credentials via API', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email'],
        ]);
});

it('fails login with invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials.']);
});

it('fails login with non-existent email', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertStatus(401);
});

// ─── Authenticated User ─────────────────────────────────────────────────

it('can retrieve the authenticated user', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/v1/auth/user');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

it('fails to retrieve user without authentication', function (): void {
    $this->getJson('/api/v1/auth/user')
        ->assertStatus(401);
});

// ─── Logout ─────────────────────────────────────────────────────────────

it('can logout and revoke token', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/auth/logout');

    $response->assertStatus(200);

    // Token should be revoked
    expect($user->tokens()->count())->toBe(0);
});

// ─── Password Reset ─────────────────────────────────────────────────────

it('can request a password reset link', function (): void {
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/v1/auth/password/forgot', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message']);
});

it('does not reveal if email exists on password reset request', function (): void {
    $response = $this->postJson('/api/v1/auth/password/forgot', [
        'email' => 'nonexistent@example.com',
    ]);

    // Always returns 200 to prevent email enumeration
    $response->assertStatus(200);
});

// ─── Email Verification ─────────────────────────────────────────────────

it('can resend verification email', function (): void {
    $user = User::factory()->unverified()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/auth/email/verify/resend');

    $response->assertStatus(200);

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

it('returns success even if already verified (idempotent)', function (): void {
    $user = User::factory()->create(); // verified by default
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/auth/email/verify/resend');

    // Returns 200 with message — idempotent, no error thrown
    $response->assertStatus(200)
        ->assertJson(['message' => 'Email already verified.']);
});
