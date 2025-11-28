<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // email ou username
            'password' => 'required|string|min:4',
            'device_name' => 'nullable|string|max:100',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Encontrar usuário por email ou username
        $userQuery = User::query();
        if (str_contains($login, '@')) {
            $userQuery->where('email', $login);
        } else {
            $userQuery->where('username', $login)->orWhere('email', $login);
        }
        /** @var User|null $user */
        $user = $userQuery->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Usuário inativo'], 403);
        }

        // Gerar token Sanctum
        $tokenName = $request->input('device_name', 'mobile');
        $token = $user->createToken($tokenName, ['*'])->plainTextToken;
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['success' => true]);
    }
}
