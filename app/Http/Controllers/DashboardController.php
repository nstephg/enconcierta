<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\ComentarioLike;
use App\Models\Evento;
use App\Models\Like;
use App\Models\Notificacion;
use App\Models\Publicacion;
use App\Models\User;
use App\Models\Parche;
use App\Models\ParcheMiembro;
use App\Models\ParcheMensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class DashboardController extends Controller
{
    public function index()
    {
        $upcomingEvents = Evento::orderBy('fecha', 'asc')->take(5)->get();

        // Se filtran únicamente publicaciones de tipo 'review' para no mezclar los parches en el feed
        $posts = Publicacion::where('tipo', 'review')
            ->with(['user', 'evento', 'likes', 'comentarios' => function($q) {
                $q->whereNull('parent_id');
            }])
            ->latest('created_at')
            ->get();

        $suggestedUsers = User::where('id_usuario', '!=', Auth::id())
            ->where('id_rol', '!=', 2)
            ->withExists(['followers as is_following' => function ($q) {
                $q->where('seguidores.id_seguidor', Auth::id());
            }])
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('dashboard.feed', compact('posts', 'upcomingEvents', 'suggestedUsers'));
    }

    public function showPost($id)
    {
        $post = Publicacion::with([
            'user', 
            'evento', 
            'likes', 
            'comentarios' => function($q) {
                $q->whereNull('parent_id')->with(['user', 'likes', 'respuestas.user', 'respuestas.likes', 'respuestas.parent.user']);
            }
        ])->findOrFail($id);

        return view('dashboard.post-detail', compact('post'));
    }

    public function storePost(Request $request)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'in:review,parche'],
            'contenido' => ['nullable', 'required_without_all:imagenes,gifs', 'string', 'max:1000'],
            'show_nombre' => ['nullable', 'string', 'max:255'],
            'imagenes' => ['nullable', 'array', 'max:6'],
            'imagenes.*' => ['file', 'mimes:jpeg,png,jpg,webp,mp4,mov,webm,avi', 'max:51200'],
            'gifs' => ['nullable', 'array', 'max:4'],
            'gifs.*' => ['url'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'encuesta_pregunta' => ['nullable', 'string', 'max:255'],
            'encuesta_opciones' => ['nullable', 'array', 'min:2'],
        ]);

        $storedImages = [];
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $file) {
                $storedImages[] = $file->store('posts', 'public');
            }
        }

        $storedGifs = !empty($validated['gifs']) ? array_values(array_filter($validated['gifs'])) : [];

        $encuestaData = null;
        if (!empty($validated['encuesta_pregunta']) && !empty($validated['encuesta_opciones'])) {
            $opcionesFiltradas = array_values(array_filter($validated['encuesta_opciones']));
            if (count($opcionesFiltradas) >= 2) {
                $encuestaData = [
                    'pregunta' => $validated['encuesta_pregunta'],
                    'opciones' => array_map(fn($opt) => ['texto' => $opt, 'votos' => 0], $opcionesFiltradas)
                ];
            }
        }

        $post = Publicacion::create([
            'id_usuario' => Auth::id(),
            'show_nombre' => $validated['show_nombre'] ?? null,
            'tipo' => $validated['tipo'],
            'contenido' => $validated['contenido'] ?? '',
            'imagen' => $storedImages[0] ?? null,
            'imagenes' => !empty($storedImages) ? $storedImages : null,
            'gif_url' => $storedGifs[0] ?? null,
            'gifs' => !empty($storedGifs) ? $storedGifs : null,
            'ubicacion' => $validated['ubicacion'] ?? null,
            'encuesta' => $encuestaData,
        ]);

        // DETECCIÓN Y NOTIFICACIÓN DE MENCIONES EN PUBLICACIONES (@)
        if (!empty($validated['contenido'])) {
            preg_match_all('/@([a-zA-Z0-9_]+)/', $validated['contenido'], $matches);
            if (!empty($matches[1])) {
                $handles = array_unique(array_map('strtolower', $matches[1]));
                $mentionedUsers = User::whereIn('handle', $handles)
                    ->where('id_usuario', '!=', Auth::id())
                    ->get();

                foreach ($mentionedUsers as $mUser) {
                    Notificacion::crear(
                        idUsuario: $mUser->id_usuario,
                        idActor: Auth::id(),
                        tipo: 'mention',
                        accion: 'te pidió o mencionó en',
                        subject: $post->show_nombre ?? 'un momento',
                        idPublicacion: $post->id_publicacion
                    );
                }
            }
        }

        return redirect()->route('dashboard');
    }

    public function storeComment(Request $request, $id)
    {
        $post = Publicacion::findOrFail($id);

        $validated = $request->validate([
            'contenido' => ['nullable', 'required_without_all:imagenes,gifs', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:comentarios,id_comentario'],
            'imagenes' => ['nullable', 'array', 'max:3'],
            'imagenes.*' => ['file', 'mimes:jpeg,png,jpg,webp,mp4,mov,webm,avi', 'max:51200'],
            'gifs' => ['nullable', 'array', 'max:2'],
            'gifs.*' => ['url'],
        ]);

        $storedImages = [];
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $file) {
                $storedImages[] = $file->store('comments', 'public');
            }
        }

        $storedGifs = !empty($validated['gifs']) ? array_values(array_filter($validated['gifs'])) : [];

        $comment = Comentario::create([
            'id_publicacion' => $post->id_publicacion,
            'parent_id' => $validated['parent_id'] ?? null,
            'id_usuario' => Auth::id(),
            'contenido' => $validated['contenido'] ?? '',
            'imagenes' => !empty($storedImages) ? $storedImages : null,
            'gifs' => !empty($storedGifs) ? $storedGifs : null,
        ]);

        // Disparar Notificación de Respuesta o Comentario
        if ($comment->parent_id) {
            $parentComment = Comentario::find($comment->parent_id);
            if ($parentComment) {
                Notificacion::crear(
                    idUsuario: $parentComment->id_usuario,
                    idActor: Auth::id(),
                    tipo: 'reply',
                    accion: 'respondió a tu comentario en',
                    subject: $post->show_nombre ?? 'un momento',
                    idPublicacion: $post->id_publicacion
                );
            }
        } else {
            Notificacion::crear(
                idUsuario: $post->id_usuario,
                idActor: Auth::id(),
                tipo: 'comment',
                accion: 'comentó en tu momento de',
                subject: $post->show_nombre ?? 'un momento',
                idPublicacion: $post->id_publicacion
            );
        }

        // DETECCIÓN Y NOTIFICACIÓN DE MENCIONES EN COMENTARIOS (@)
        if (!empty($validated['contenido'])) {
            preg_match_all('/@([a-zA-Z0-9_]+)/', $validated['contenido'], $matches);
            if (!empty($matches[1])) {
                $handles = array_unique(array_map('strtolower', $matches[1]));
                $mentionedUsers = User::whereIn('handle', $handles)
                    ->where('id_usuario', '!=', Auth::id())
                    ->get();

                foreach ($mentionedUsers as $mUser) {
                    Notificacion::crear(
                        idUsuario: $mUser->id_usuario,
                        idActor: Auth::id(),
                        tipo: 'mention',
                        accion: 'te mencionó en un comentario en',
                        subject: $post->show_nombre ?? 'un momento',
                        idPublicacion: $post->id_publicacion
                    );
                }
            }
        }

        return redirect()->route('posts.show', $post->id_publicacion);
    }

    public function votePoll(Request $request, $id)
    {
        $post = Publicacion::findOrFail($id);
        $request->validate([
            'opcion_index' => ['required', 'integer']
        ]);

        $encuesta = $post->encuesta;
        if (!$encuesta || !isset($encuesta['opciones'])) {
            return response()->json(['success' => false, 'message' => 'Sin encuesta'], 400);
        }

        $userId = Auth::id();
        $opcionIndex = (int) $request->opcion_index;

        if (!isset($encuesta['votos_usuarios'])) {
            $encuesta['votos_usuarios'] = [];
        }

        if (isset($encuesta['votos_usuarios'][$userId])) {
            $prevIndex = $encuesta['votos_usuarios'][$userId];
            if ($prevIndex === $opcionIndex) {
                return response()->json(['success' => true, 'encuesta' => $encuesta]);
            }
            if (isset($encuesta['opciones'][$prevIndex])) {
                $encuesta['opciones'][$prevIndex]['votos'] = max(0, ($encuesta['opciones'][$prevIndex]['votos'] ?? 0) - 1);
            }
        }

        $encuesta['opciones'][$opcionIndex]['votos'] = ($encuesta['opciones'][$opcionIndex]['votos'] ?? 0) + 1;
        $encuesta['votos_usuarios'][$userId] = $opcionIndex;

        $post->encuesta = $encuesta;
        $post->save();

        return response()->json(['success' => true, 'encuesta' => $encuesta]);
    }

    public function toggleLike($id)
    {
        $post = Publicacion::findOrFail($id);
        $userId = Auth::id();

        $like = Like::where('id_publicacion', $post->id_publicacion)
            ->where('id_usuario', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;

            // Limpiar notificación si quita la reacción
            Notificacion::where('id_usuario', $post->id_usuario)
                ->where('id_actor', $userId)
                ->where('tipo', 'vibro')
                ->where('id_publicacion', $post->id_publicacion)
                ->delete();
        } else {
            Like::create([
                'id_usuario' => $userId,
                'id_publicacion' => $post->id_publicacion,
            ]);
            $liked = true;

            // Disparar Notificación de Like/Vibro
            Notificacion::crear(
                idUsuario: $post->id_usuario,
                idActor: $userId,
                tipo: 'vibro',
                accion: 'vibró con tu momento de',
                subject: $post->show_nombre ?? 'un momento',
                idPublicacion: $post->id_publicacion
            );
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    public function toggleCommentLike($id)
    {
        $comment = Comentario::with('publicacion')->findOrFail($id);
        $userId = Auth::id();

        $like = ComentarioLike::where('id_comentario', $comment->id_comentario)
            ->where('id_usuario', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            ComentarioLike::create([
                'id_usuario' => $userId,
                'id_comentario' => $comment->id_comentario,
            ]);
            $liked = true;

            // Disparar Notificación de Like en Comentario
            Notificacion::crear(
                idUsuario: $comment->id_usuario,
                idActor: $userId,
                tipo: 'vibro',
                accion: 'vibró con tu comentario en',
                subject: $comment->publicacion->show_nombre ?? 'un momento',
                idPublicacion: $comment->id_publicacion
            );
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $comment->likes()->count(),
        ]);
    }

    public function destroyComment($id)
    {
        $comment = Comentario::findOrFail($id);
        $userId = Auth::id();

        // Validar que ÚNICAMENTE el creador del comentario pueda eliminarlo
        if ($comment->id_usuario !== $userId) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }

    public function destroyPost($id)
    {
        $post = Publicacion::findOrFail($id);
        $userId = Auth::id();

        // Validar que ÚNICAMENTE el autor de la publicación pueda eliminarla
        if ($post->id_usuario !== $userId) {
            return response()->json(['success' => false, 'message' => 'No autorizado para eliminar este momento.'], 403);
        }

        // Eliminar archivos multimedia asociados de la memoria física
        if (!empty($post->imagenes) && is_array($post->imagenes)) {
            foreach ($post->imagenes as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
        } elseif (!empty($post->imagen)) {
            if (Storage::disk('public')->exists($post->imagen)) {
                Storage::disk('public')->delete($post->imagen);
            }
        }

        $post->delete();

        return response()->json(['success' => true, 'message' => 'Publicación eliminada correctamente.']);
    }

    public function explorar(Request $request)
    {
        $query = Evento::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('artista', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('lugar', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ciudad') && $request->ciudad !== 'Todas') {
            $query->where('ciudad', $request->ciudad);
        }

        $eventos = $query->withCount('parches')
            ->with('participantes')
            ->orderBy('fecha', 'asc')
            ->get();

        return view('dashboard.explorar', compact('eventos'));
    }

    public function showEvento($id)
    {
        $evento = Evento::with('participantes')->findOrFail($id);

        $parches = Parche::where('id_evento', $evento->id_evento)
            ->with(['creador', 'miembros.user', 'mensajes.user'])
            ->latest()
            ->get();

        return view('dashboard.explorar-show', compact('evento', 'parches'));
    }

    public function showParche($id)
    {
        $parche = Parche::with(['creador', 'evento', 'miembros.user', 'mensajes.user', 'mensajes.parent.user'])
            ->findOrFail($id);

        $evento = $parche->evento;

        return view('dashboard.parche-detail', compact('parche', 'evento'));
    }

    public function storeParche(Request $request)
    {
        $validated = $request->validate([
            'id_evento' => ['required', 'exists:eventos,id_evento'],
            'titulo' => ['nullable', 'string', 'max:255'],
            'nombre' => ['nullable', 'string', 'max:255'],
            'tipo_plan' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'hora_encuentro' => ['nullable', 'string', 'max:255'],
            'cupos' => ['nullable', 'integer', 'min:1', 'max:100'],
            'contenido' => ['nullable', 'string', 'max:1000'],
        ]);

        $nombreParche = $request->input('titulo') ?? $request->input('nombre') ?? 'Parche ' . ($request->input('tipo_plan') ?? '');

        $parche = Parche::create([
            'id_evento' => $validated['id_evento'],
            'id_usuario_creador' => Auth::id(),
            'nombre' => $nombreParche,
            'tipo_plan' => $validated['tipo_plan'] ?? 'Previa',
            'sector' => $validated['sector'] ?? 'General',
            'hora_encuentro' => $validated['hora_encuentro'] ?? '05:30 p. m.',
            'cupos' => (int) ($validated['cupos'] ?? 4),
            'contenido' => $validated['contenido'] ?? null,
        ]);

        ParcheMiembro::create([
            'id_parche' => $parche->id_parche,
            'id_usuario' => Auth::id(),
            'estado' => 'activo',
        ]);

        return redirect()->route('parche.show', $parche->id_parche);
    }

    public function updateParche(Request $request, $id)
    {
        $parche = Parche::findOrFail($id);

        if (Auth::id() !== $parche->id_usuario_creador && Auth::user()->id_rol !== 2) {
            return redirect()->back()->with('error', 'No autorizado.');
        }

        $validated = $request->validate([
            'show_nombre' => ['required', 'string', 'max:255'],
            'tipo_plan' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'hora_encuentro' => ['nullable', 'string', 'max:255'],
            'cupos' => ['nullable', 'integer', 'min:1', 'max:100'],
            'contenido' => ['nullable', 'string', 'max:1000'],
            'plan_noche' => ['nullable', 'string', 'max:2000'],
        ]);

        $parche->update([
            'nombre' => $validated['show_nombre'],
            'tipo_plan' => $validated['tipo_plan'] ?? $parche->tipo_plan,
            'sector' => $validated['sector'] ?? $parche->sector,
            'hora_encuentro' => $validated['hora_encuentro'] ?? $parche->hora_encuentro,
            'cupos' => $validated['cupos'] ?? $parche->cupos,
            'contenido' => $validated['contenido'] ?? $parche->contenido,
            'plan_noche' => $validated['plan_noche'] ?? $parche->plan_noche,
        ]);

        return redirect()->route('parche.show', $parche->id_parche)->with('success', 'Parche actualizado.');
    }

    public function destroyParche($id)
    {
        $parche = Parche::findOrFail($id);

        if (Auth::id() !== $parche->id_usuario_creador && Auth::user()->id_rol !== 2) {
            return redirect()->back()->with('error', 'No tienes permisos para eliminar este parche.');
        }

        $eventId = $parche->id_evento;
        $parche->delete();

        return redirect()->route('explorar.show', $eventId)->with('success', 'Parche eliminado correctamente.');
    }

    public function joinParche($id)
    {
        $parche = Parche::with('miembros')->findOrFail($id);
        $userId = Auth::id();

        $miembro = ParcheMiembro::where('id_parche', $parche->id_parche)
            ->where('id_usuario', $userId)
            ->first();

        if ($miembro) {
            if ($miembro->estado === 'expulsado') {
                return redirect()->back()->with('error', 'Has sido expulsado de este parche y no puedes volver a unirte.');
            }
            if ($miembro->estado === 'activo') {
                return redirect()->route('parche.show', $parche->id_parche);
            }
        }

        $activosCount = $parche->miembros->where('estado', 'activo')->count();
        if ($activosCount >= $parche->cupos) {
            return redirect()->back()->with('error', 'El parche ya no tiene cupos disponibles.');
        }

        ParcheMiembro::create([
            'id_parche' => $parche->id_parche,
            'id_usuario' => $userId,
            'estado' => 'activo',
        ]);

        // NOTIFICACIÓN: Se unió un integrante al parche
        if ($parche->id_usuario_creador !== $userId) {
            Notificacion::crear(
                idUsuario: $parche->id_usuario_creador,
                idActor: $userId,
                tipo: 'parche',
                accion: 'se unió a tu parche',
                subject: $parche->nombre,
                idParche: $parche->id_parche
            );
        }

        return redirect()->route('parche.show', $parche->id_parche);
    }

    public function leaveParche($id)
    {
        $parche = Parche::findOrFail($id);
        $userId = Auth::id();

        if ($userId === $parche->id_usuario_creador) {
            return redirect()->back()->with('error', 'Como anfitrión no puedes salir, solo eliminar el parche.');
        }

        ParcheMiembro::where('id_parche', $parche->id_parche)
            ->where('id_usuario', $userId)
            ->delete();

        return redirect()->route('explorar.show', $parche->id_evento)->with('success', 'Has salido del parche.');
    }

    public function kickMiembro($id, $userId)
    {
        $parche = Parche::findOrFail($id);

        if (Auth::id() !== $parche->id_usuario_creador && Auth::user()->id_rol !== 2) {
            return redirect()->back()->with('error', 'No autorizado.');
        }

        if ((int)$userId === $parche->id_usuario_creador) {
            return redirect()->back()->with('error', 'No puedes expulsar al anfitrión.');
        }

        ParcheMiembro::updateOrCreate(
            ['id_parche' => $parche->id_parche, 'id_usuario' => $userId],
            ['estado' => 'expulsado']
        );

        return redirect()->route('parche.show', $parche->id_parche)->with('success', 'Miembro expulsado y vetado del parche.');
    }

    public function storeParcheComment(Request $request, $id)
    {
        $parche = Parche::with('evento')->findOrFail($id);
        $user = Auth::user();
        $userId = $user->id_usuario ?? $user->id;

        $miembro = ParcheMiembro::where('id_parche', $parche->id_parche)
            ->where('id_usuario', $userId)
            ->first();

        if ($miembro && $miembro->estado === 'expulsado') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'No puedes enviar mensajes en este parche.'], 403);
            }
            return redirect()->back()->with('error', 'No puedes enviar mensajes en este parche.');
        }

        $texto = $request->input('mensaje') ?? $request->input('contenido');

        if (empty(trim($texto ?? '')) && empty($request->input('gif_url')) && !$request->hasFile('imagenes')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'El mensaje no puede estar vacío.'], 422);
            }
            return redirect()->back()->with('error', 'El mensaje no puede estar vacío.');
        }

        $imagenesPaths = [];
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $file) {
                $imagenesPaths[] = $file->store('parches/chat', 'public');
            }
        }

        $mensaje = ParcheMensaje::create([
            'id_parche' => $parche->id_parche,
            'id_usuario' => $userId,
            'mensaje' => $texto ?? '',
            'parent_id' => $request->input('parent_id') ?: null,
            'gif_url' => $request->input('gif_url') ?: null,
            'imagenes' => !empty($imagenesPaths) ? $imagenesPaths : null,
        ]);

        $mensaje->load(['user', 'parent.user']);

        // NOTIFICACIONES UNIFICADAS Y ACUMULATIVAS POR PARCHE
        $miembrosNotificar = ParcheMiembro::where('id_parche', $parche->id_parche)
            ->where('estado', 'activo')
            ->where('id_usuario', '!=', $userId)
            ->pluck('id_usuario');

        foreach ($miembrosNotificar as $idMiembro) {
            $notifExistente = Notificacion::where('id_usuario', $idMiembro)
                ->where('tipo', 'parche')
                ->where('subject', $parche->nombre)
                ->where('leido', false)
                ->first();

            if ($notifExistente) {
                // Acumular cantidad de nuevos mensajes
                if (preg_match('/\((\d+) nuevos\)/', $notifExistente->accion, $matches)) {
                    $count = ((int) $matches[1]) + 1;
                } else {
                    $count = 2;
                }

                $notifExistente->update([
                    'id_actor' => $userId,
                    'id_parche' => $parche->id_parche,
                    'accion' => "tiene nueva actividad ({$count} nuevos)",
                    'created_at' => now(),
                ]);
            } else {
                Notificacion::crear(
                    idUsuario: $idMiembro,
                    idActor: $userId,
                    tipo: 'parche',
                    accion: 'tiene nuevos mensajes',
                    subject: $parche->nombre,
                    idParche: $parche->id_parche
                );
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'comentario' => [
                    'id' => $mensaje->id_mensaje ?? $mensaje->id,
                    'id_usuario' => $mensaje->id_usuario,
                    'is_me' => true,
                    'nombre' => $mensaje->user->nombre ?? 'Usuario',
                    'avatar' => $mensaje->user->avatar ? asset('storage/' . $mensaje->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($mensaje->user->nombre ?? 'U'),
                    'mensaje' => $mensaje->mensaje,
                    'gif_url' => $mensaje->gif_url,
                    'imagenes' => $mensaje->imagenes,
                    'has_parent' => !empty($mensaje->parent_id) && $mensaje->parent,
                    'parent_user' => $mensaje->parent?->user?->nombre ?? 'Usuario',
                    'parent_mensaje' => $mensaje->parent?->mensaje ?? '',
                    'created_at' => $mensaje->created_at ? $mensaje->created_at->format('h:i A') : date('h:i A'),
                ]
            ]);
        }

        return redirect()->route('parche.show', ['parche' => $id, 'tab' => 'chat']);
    }

    public function getParcheMensajes(Request $request, $id)
    {
        $lastId = $request->query('last_id', 0);

        $nuevosMensajes = ParcheMensaje::where('id_parche', $id)
            ->where('id_mensaje', '>', $lastId)
            ->with(['user', 'parent.user'])
            ->orderBy('id_mensaje', 'asc')
            ->get();

        $userId = Auth::id();

        $formatted = $nuevosMensajes->map(function ($msg) use ($userId) {
            return [
                'id' => $msg->id_mensaje ?? $msg->id,
                'id_usuario' => $msg->id_usuario,
                'is_me' => $msg->id_usuario === $userId,
                'nombre' => $msg->user->nombre ?? 'Usuario',
                'avatar' => $msg->user->avatar ? asset('storage/' . $msg->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->nombre ?? 'U'),
                'mensaje' => $msg->mensaje,
                'gif_url' => $msg->gif_url,
                'imagenes' => $msg->imagenes,
                'has_parent' => !empty($msg->parent_id) && $msg->parent,
                'parent_user' => $msg->parent?->user?->nombre ?? 'Usuario',
                'parent_mensaje' => $msg->parent?->mensaje ?? '',
                'created_at' => $msg->created_at ? $msg->created_at->format('h:i A') : date('h:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'mensajes' => $formatted
        ]);
    }

    public function readAndRedirect($id)
    {
        $notif = Notificacion::where('id_notificacion', $id)
            ->where('id_usuario', Auth::id())
            ->first();

        if ($notif) {
            $notif->update(['leido' => true]);

            // 1. Redirección para momentos / publicaciones
            if ($notif->id_publicacion) {
                return redirect()->route('posts.show', $notif->id_publicacion);
            }

            // 2. Redirección directa por ID de parche
            if ($notif->id_parche) {
                return redirect()->route('parche.show', ['parche' => $notif->id_parche, 'tab' => 'chat']);
            }

            // 3. Fallback de resolución de parche por nombre en subject
            if (($notif->tipo === 'parche' || str_contains(strtolower($notif->accion), 'parche')) && $notif->subject) {
                $parche = Parche::where('nombre', $notif->subject)->first();
                if ($parche) {
                    return redirect()->route('parche.show', ['parche' => $parche->id_parche, 'tab' => 'chat']);
                }
            }

            // 4. Redirección para seguimiento de usuarios
            if ($notif->tipo === 'follow' && $notif->id_actor) {
                return redirect()->route('profile.show', $notif->id_actor);
            }
        }

        return redirect()->route('dashboard');
    }
}