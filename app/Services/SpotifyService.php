<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SpotifyService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id', env('SPOTIFY_CLIENT_ID'));
        $this->clientSecret = config('services.spotify.client_secret', env('SPOTIFY_CLIENT_SECRET'));
        $this->redirectUri = config('services.spotify.redirect_uri', env('SPOTIFY_REDIRECT_URI'));
    }

    public function getAuthUrl(): string
    {
        $state = Str::random(16);
        session(['spotify_auth_state' => $state]);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => 'user-read-private user-read-email',
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
        ]);

        return 'https://accounts.spotify.com/authorize?' . $query;
    }

    public function getAccessToken(string $code): ?array
    {
        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
        ])->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        return $response->successful() ? $response->json() : null;
    }

    public function getUserProfile(string $accessToken): ?array
    {
        $response = Http::withToken($accessToken)->get('https://api.spotify.com/v1/me');

        return $response->successful() ? $response->json() : null;
    }
}