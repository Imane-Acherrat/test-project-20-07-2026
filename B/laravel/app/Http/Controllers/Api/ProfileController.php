<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\PublicUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(
            (new UserResource($request->user()))->toArray($request)
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('profileImage')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profileImage')->store('profiles', 'public');
            $data['profile_image'] = $path;
            unset($data['profileImage']);
        }

        $user->fill($data)->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => (new UserResource($user->fresh()))->toArray($request),
        ]);
    }

    public function showPublic(Request $request, string $username): JsonResponse
    {
        $user = User::where('username', $username)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(
            (new PublicUserResource($user))->toArray($request)
        );
    }
}
