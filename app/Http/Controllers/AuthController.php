<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileAvatarRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) throw new Exception('Email not found', HttpFoundationResponse::HTTP_NOT_FOUND);

            if (!Hash::check($request->password, $user->password)) throw new Exception('Password wrong');

            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            return Response::success(['token' => $token]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            User::create([
                'email' => $request->email,
                'username' => $request->username,
                'name' => $request->name,
                'password' => Hash::make($request->password)
            ]);
            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();

            $data = [
                'name' => $request->name,
                'username' => $request->username,
            ];
            $user->update($data);
            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function updateProfileAvatar(UpdateProfileAvatarRequest $request)
    {
        try {
            $user = $request->user();

            if ($user->image_path && Storage::disk('public')->exists($user->image_path)) {
                Storage::disk('public')->delete($user->image_path);
            }

            $path = $request->file('image')->store('profiles', 'public');

            $user->update([
                'image_path' => $path
            ]);
            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $user->loadCount(['followers', 'followings', 'posts']);
            return Response::success($user);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return Response::success(null);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }
}
