<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\Notificacion;

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
        Carbon::setLocale('es');

        if (request()->hasHeader('X-Forwarded-Host')) {
            $host = request()->header('X-Forwarded-Host');
            $proto = request()->header('X-Forwarded-Proto', 'https');
            
            URL::forceRootUrl("{$proto}://{$host}");
            URL::forceScheme($proto);
        } elseif (request()->hasHeader('X-Forwarded-Proto') || str_contains(request()->header('host', ''), 'ngrok')) {
            URL::forceScheme('https');
        }

        // View Composer para Notificaciones con URLs precalculadas
        View::composer(['layouts.partials.dashboard-header', 'layouts.partials.notifications-panel'], function ($view) {
            if (auth()->check()) {
                try {
                    if (Schema::hasTable('notificaciones')) {
                        $userId = auth()->user()->id_usuario;

                        $notifs = Notificacion::with('actor')
                            ->where('id_usuario', $userId)
                            ->latest()
                            ->take(30)
                            ->get();

                        $unreadCount = Notificacion::where('id_usuario', $userId)
                            ->where('leido', 0)
                            ->count();

                        $formattedNotifs = $notifs->map(function ($n) {
                            $item = $n->toArray();
                            $item['time_ago'] = $n->created_at ? $n->created_at->locale('es')->diffForHumans() : '';
                            $item['profile_url'] = $n->id_actor ? route('profile.show', $n->id_actor) : '#';
                            $item['post_url'] = $n->id_publicacion ? route('posts.show', $n->id_publicacion) : null;

                            if ($n->actor) {
                                $item['actor']['avatar_url_formatted'] = $n->actor->avatar_url;
                            }
                            return $item;
                        });

                        $view->with('notificacionesSistema', $formattedNotifs)
                             ->with('unreadCount', $unreadCount);
                        return;
                    }
                } catch (\Exception $e) {
                    // Fallback silencioso
                }
            }

            $view->with('notificacionesSistema', collect())
                 ->with('unreadCount', 0);
        });
    }
}