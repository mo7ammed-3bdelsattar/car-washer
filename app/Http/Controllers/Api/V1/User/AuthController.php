<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'type' => $validated['type'] ?? UserType::CUSTOMER->value,
            'fcm_token' => $request->input('fcm'),
            'is_active' => true,
        ]);

        $token = $user->createToken('car_washer_token', ['*']);
        $plaintext = $token->plainTextToken;
        $token->accessToken->update([
            'expires_at' => now()->addMonths(6),
        ]);

        return $this->createdResponse([
            'user' => new UserResource($user),
            'token' => $plaintext,
        ], __('User registered successfully'));
    }

    /**
     * Authenticate user and issue token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = $request->user();

        if ($request->filled('fcm')) {
            $user->update([
                'fcm_token' => $request->input('fcm'),
            ]);
        }

        $token = $user->createToken('car_washer_token', ['*']);
        $plaintext = $token->plainTextToken;
        $token->accessToken->update([
            'expires_at' => now()->addMonths(6),
        ]);

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $plaintext,
        ], __('Login Successfully'));
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse([], __('Logged Out Successfully'));
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()));
    }
}
