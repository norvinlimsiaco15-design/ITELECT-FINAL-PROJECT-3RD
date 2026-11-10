<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        if (! $user->status) {
            throw ValidationException::withMessages([
                'email' => ['This account is inactive.'],
            ]);
        }

        $token = $user->createToken('orotrack')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $data = $request->safe()->only(['name', 'phone']);

        if ($request->email !== $user->email && ! $user->hasRole('admin')) {
            throw ValidationException::withMessages([
                'email' => ['Only administrators can change email addresses.'],
            ]);
        }

        $data['email'] = $request->email;

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return new UserResource($user->fresh());
    }
}
