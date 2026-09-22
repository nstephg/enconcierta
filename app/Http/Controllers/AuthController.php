<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            $nextUrl = $user->perfil_completado ? route('dashboard') : route('profile.setup');

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Bienvenido de nuevo, ' . $user->nombre . '!',
                    'redirect_url' => $nextUrl,
                    'user' => $user
                ]);
            }

            return redirect()->intended($nextUrl);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No existe una cuenta vinculada a este correo o la contraseña es incorrecta. Por favor regístrate primero.'
            ], 422);
        }

        return back()->withErrors([
            'email' => 'No existe una cuenta vinculada a este correo o la contraseña es incorrecta.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'id_rol' => 1,
            'perfil_completado' => false,
        ]);

        Auth::login($user);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '¡Registro exitoso ' . $user->nombre . '! Configura tu perfil a continuación.',
                'redirect_url' => route('profile.setup'),
                'user' => $user
            ]);
        }

        return redirect()->route('profile.setup');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}