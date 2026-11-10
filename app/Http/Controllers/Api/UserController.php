<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::with('roles')->latest()->get());
    }

    public function store(UserStoreRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'status' => $request->boolean('status', true),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        if ($request->user()->id === $user->id && $request->role !== $user->getRoleNames()->first()) {
            throw ValidationException::withMessages([
                'role' => ['You cannot change your own role.'],
            ]);
        }

        if ($request->user()->id === $user->id && (bool) $request->status !== (bool) $user->status) {
            throw ValidationException::withMessages([
                'status' => ['You cannot change your own status.'],
            ]);
        }

        if ($user->id === 1 && $request->role !== 'admin') {
            throw ValidationException::withMessages([
                'role' => ['The first user must always have the admin role.'],
            ]);
        }

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $user->id === 1 ? 1 : $request->boolean('status'),
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->password;
        }

        $user->update($payload);

        $user->syncRoles([$request->role]);

        return new UserResource($user->fresh());
    }

    public function destroy(User $user)
    {
        if ($user->id === 1 || auth()->id() === $user->id) {
            return response()->json(['message' => 'This account cannot be deleted.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User moved to trash.']);
    }
}
