<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\ComentarioLike;
use App\Models\Evento;
use App\Models\Like;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $upcomingEvents = Evento::orderBy('fecha', 'asc')->take(5)->get();

        $posts = Publicacion::with(['user', 'evento', 'likes', 'comentarios' => function($q) {
            $q->whereNull('parent_id');
        }])->latest('created_at')->get();

        $suggestedUsers = User::where('id_usuario', '!=', Auth::id())
            ->where('id_rol', '!=', 2)
            ->take(4)
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

        Publicacion::create([
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

        Comentario::create([
            'id_publicacion' => $post->id_publicacion,
            'parent_id' => $validated['parent_id'] ?? null,
            'id_usuario' => Auth::id(),
            'contenido' => $validated['contenido'] ?? '',
            'imagenes' => !empty($storedImages) ? $storedImages : null,
            'gifs' => !empty($storedGifs) ? $storedGifs : null,
        ]);

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
        } else {
            Like::create([
                'id_usuario' => $userId,
                'id_publicacion' => $post->id_publicacion,
            ]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    public function toggleCommentLike($id)
    {
        $comment = Comentario::findOrFail($id);
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
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $comment->likes()->count(),
        ]);
    }
}