<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->successResponse(new UserResource($user), __('Profile updated successfully'));
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return $this->successResponse([], __('Password changed successfully'));
    }

    /**
     * Soft delete the authenticated user's account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // Mark user inactive
        $user->update(['is_active' => false]);

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete user record (sets deleted_at timestamp)
        $user->delete();

        return $this->successResponse([], __('Account deleted successfully'));
    }
}
