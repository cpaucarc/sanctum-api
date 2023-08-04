<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'User not found, please check your email or password'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $userAccessType = $user->tokens()->first()?->name ?? 'basic';
        $user->tokens()->delete();
        $token = $user->createToken($userAccessType, config('constants.UserAccessType')[$userAccessType])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'tokenType' => 'Bearer'
        ], Response::HTTP_OK);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:3|confirmed',
        ]);

        $user = User::create($request->only('name', 'email', 'password'));

        $token = '';

        if (!$request->has('userType') || $request->userType == 'basic' || !in_array($request->userType, array_keys(config('constants.UserAccessType')))) {
            $token = $user->createToken('basic', config('constants.UserAccessType.basic'))->plainTextToken;
        } else {
            $token = $user->createToken($request->userType, config('constants.UserAccessType')[$request->userType])->plainTextToken;
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
            'tokenType' => 'Bearer'
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }
}
