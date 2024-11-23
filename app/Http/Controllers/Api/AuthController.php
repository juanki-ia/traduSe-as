<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response([
                    'message' => 'Credenciales Inválidas'
                ], 403);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('authToken')->plainTextToken;
            $user->token = $token;

            return response($user->only('id', 'name', 'email', 'token'), 200);
        } catch (\Exception $e) {
            return response(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response(['message' => 'Sesión cerrada'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->token = $user->createToken('authToken')->plainTextToken;

            return response($user->only('id', 'name', 'email', 'token'), 200);
        } catch (\Exception $e) {
            return response(['message' => 'Error interno del servidor'], 500);
        }
    }
}
