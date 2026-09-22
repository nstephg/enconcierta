<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService
{
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id') ?? env('SPOTIFY_CLIENT_ID', '');
        $this->clientSecret = config('services.spotify.client_secret') ?? env('SPOTIFY_CLIENT_SECRET', '');
    }

    public function getRedirectUri(): string
    {
        $configuredUri = config('services.spotify.redirect_uri');
        
        // Si la URL de configuración coincide con el dominio actual, la usamos; si no, la generamos dinámicamente
        if (!empty($configuredUri) && str_starts_with($configuredUri, request()->schemeAndHttpHost())) {
            return $configuredUri;
        }

        return route('spotify.callback');
    }

    public function getAuthUrl(): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->getRedirectUri(),
            'scope' => 'user-read-private user-read-email',
            'show_dialog' => 'true',
        ]);

        return 'https://accounts.spotify.com/authorize?' . $query;
    }

    public function getAccessToken(string $code): ?array
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->getRedirectUri(),
            ]);

        return $response->successful() ? $response->json() : null;
    }

    public function getUserProfile(string $accessToken): ?array
    {
        $response = Http::withToken($accessToken)
            ->get('https://api.spotify.com/v1/me');

        return $response->successful() ? $response->json() : null;
    }
}