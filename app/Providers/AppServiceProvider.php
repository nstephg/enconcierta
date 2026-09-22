<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar la URL raíz y el protocolo HTTPS dinámicamente si la petición proviene de Ngrok o un Proxy
        if (request()->hasHeader('X-Forwarded-Host')) {
            $host = request()->header('X-Forwarded-Host');
            $proto = request()->header('X-Forwarded-Proto', 'https');
            
            URL::forceRootUrl("{$proto}://{$host}");
            URL::forceScheme($proto);
        } elseif (request()->hasHeader('X-Forwarded-Proto') || str_contains(request()->header('host', ''), 'ngrok')) {
            URL::forceScheme('https');
        }
    }
}