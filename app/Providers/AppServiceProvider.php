<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\Notificacion;
use App\Models\Parche;

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

        // View Composer para Notificaciones con URLs precalculadas e imágenes de evento
        View::composer(['layouts.partials.dashboard-header', 'layouts.partials.notifications-panel'], function ($view) {
            if (auth()->check()) {
                try {
                    if (Schema::hasTable('notificaciones')) {
                        $userId = auth()->user()->id_usuario;

                        $notifs = Notificacion::with(['actor', 'parche.evento'])
                            ->where('id_usuario', $userId)
                            ->latest()
                            ->take(30)
                            ->get();

                        $unreadCount = Notificacion::where('id_usuario', $userId)
                            ->where('leido', 0)
                            ->count();

                        // Cargar parches por subject para notificaciones previas a la migración
                        $parcheSubjects = $notifs->where('tipo', 'parche')->pluck('subject')->filter()->unique();
                        $parchesMap = [];
                        if ($parcheSubjects->isNotEmpty() && Schema::hasTable('parches')) {
                            $parchesMap = Parche::with('evento')
                                ->whereIn('nombre', $parcheSubjects)
                                ->get()
                                ->keyBy('nombre');
                        }

                        $formattedNotifs = $notifs->map(function ($n) use ($parchesMap) {
                            $item = $n->toArray();
                            $item['time_ago'] = $n->created_at ? $n->created_at->locale('es')->diffForHumans() : '';
                            $item['profile_url'] = $n->id_actor ? route('profile.show', $n->id_actor) : '#';

                            $parcheObj = $n->parche ?? ($parchesMap[$n->subject] ?? null);

                            if ($n->tipo === 'parche') {
                                // Resolver imagen del evento asociado al parche
                                $eventImg = null;
                                if ($parcheObj && $parcheObj->evento) {
                                    $rawImg = $parcheObj->evento->imagen_portada ?? $parcheObj->evento->imagen ?? null;
                                    if ($rawImg) {
                                        $eventImg = str_starts_with($rawImg, 'http') ? $rawImg : asset('storage/' . $rawImg);
                                    }
                                }
                                $item['event_image'] = $eventImg;

                                $parcheId = $n->id_parche ?? ($parcheObj?->id_parche);
                                if ($parcheId) {
                                    $isChat = str_contains(strtolower($n->accion ?? ''), 'mensaje');
                                    $item['post_url'] = route('parche.show', $parcheId) . ($isChat ? '?tab=chat' : '');
                                } else {
                                    $item['post_url'] = null;
                                }
                            } else {
                                $item['event_image'] = null;
                                $item['post_url'] = $n->id_publicacion ? route('posts.show', $n->id_publicacion) : null;
                            }

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