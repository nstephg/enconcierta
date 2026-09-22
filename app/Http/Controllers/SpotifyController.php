<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    protected SpotifyService $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function redirect()
    {
        return redirect()->away($this->spotifyService->getAuthUrl());
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('profile.show')->with('error', 'Autorización cancelada por el usuario.');
        }

        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('profile.show')->with('error', 'Código de autorización no recibido.');
        }

        $tokenData = $this->spotifyService->getAccessToken($code);

        if (!$tokenData || !isset($tokenData['access_token'])) {
            return redirect()->route('profile.show')->with('error', 'Error al obtener el token de Spotify.');
        }

        // Obtener el perfil de Spotify
        $spotifyProfile = $this->spotifyService->getUserProfile($tokenData['access_token']);

        if ($spotifyProfile) {
            $user = auth()->user();
            $user->spotify = $spotifyProfile['id'] ?? null;
            $user->spotify_name = $spotifyProfile['display_name'] ?? $spotifyProfile['id'] ?? null;
            $user->save();
        }

        session([
            'spotify_access_token' => $tokenData['access_token'],
            'spotify_refresh_token' => $tokenData['refresh_token'] ?? null,
            'spotify_token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
        ]);

        return redirect()->route('profile.show')->with('success', 'Conectado con Spotify correctamente.');
    }

    public function disconnect()
    {
        $user = auth()->user();

        if ($user) {
            $user->spotify = null;
            $user->spotify_name = null;
            $user->save();
        }

        session()->forget([
            'spotify_access_token',
            'spotify_refresh_token',
            'spotify_token_expires_at'
        ]);

        return redirect()->route('profile.show')->with('success', 'Cuenta de Spotify desvinculada correctamente.');
    }
}