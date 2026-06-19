<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\SocialiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Create a new AuthController.
     */
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * Register a new user.
     *
     * @response 201 {"message": "Account created. Please verify your email.", "user": {...}, "token": "..."}
     * @response 422 {"message": "Validation failed.", "errors": {...}}
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Account created. Please verify your email.',
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    /**
     * Authenticate a user.
     *
     * @response 200 {"token": "...", "user": {...}}
     * @response 401 {"message": "Invalid credentials."}
     * @response 423 {"message": "Too many login attempts..."}
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // The LoginRateLimiter middleware handles rate limit enforcement
        $result = $this->authService->login($credentials);

        if (! $result) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return response()->json([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ]);
    }

    /**
     * Authenticate or register with Google OAuth.
     *
     * @response 200 {"token": "...", "user": {...}, "is_new": false}
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $request->validate([
            'google_token' => ['required', 'string'],
        ]);

        try {
            $service = app(SocialiteService::class);
            $result = $service->handleGoogleToken($request->input('google_token'));

            $token = $result['user']->createToken('auth-token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => new UserResource($result['user']),
                'is_new' => $result['is_new'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Google authentication failed.'], 401);
        }
    }

    /**
     * Revoke the current user's token.
     *
     * @authenticated
     * @response 200 {"message": "Logged out."}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * Get the authenticated user.
     *
     * @authenticated
     * @response 200 {"id": "...", "name": "...", "email": "...", "role": "...", ...}
     */
    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Send a password reset link.
     *
     * Always returns 200 to prevent email enumeration.
     *
     * @response 200 {"message": "Reset link sent."}
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->sendPasswordResetLink($request->input('email'));

        // Always return success to prevent email enumeration
        return response()->json(['message' => 'Reset link sent.']);
    }

    /**
     * Reset the user's password.
     *
     * @response 200 {"message": "Password reset successfully."}
     * @response 422 {"message": "Validation failed.", "errors": {...}}
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.']);
        }

        return response()->json(['message' => 'Invalid or expired reset token.'], 422);
    }

    /**
     * Resend the email verification notification.
     *
     * @authenticated
     * @response 200 {"message": "Verification email sent."}
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $this->authService->resendVerification($user);

        return response()->json(['message' => 'Verification email sent.']);
    }
}
