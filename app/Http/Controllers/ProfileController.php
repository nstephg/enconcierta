<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Publicacion;
use App\Models\Participacion;
use App\Models\User;
use App\Models\Blog;

class ProfileController extends Controller
{
    /**
     * Muestra la pantalla de configuración inicial del perfil (Onboarding).
     */
    public function showSetup()
    {
        $user = Auth::user();
        return view('profile.setup', compact('user'));
    }

    /**
     * Guarda la configuración inicial del perfil (Onboarding).
     */
    public function saveSetup(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'handle' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,handle,' . $user->id_usuario . ',id_usuario'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'custom_ciudad' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'portada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],
        ]);

        $finalCiudad = $validated['ciudad'] === 'Otra' ? ($validated['custom_ciudad'] ?? null) : $validated['ciudad'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('portada')) {
            if ($user->portada && Storage::disk('public')->exists($user->portada)) {
                Storage::disk('public')->delete($user->portada);
            }
            $user->portada = $request->file('portada')->store('portadas', 'public');
        }

        $user->nombre = $validated['nombre'];
        $user->handle = strtolower($validated['handle']);
        $user->ciudad = $finalCiudad;
        $user->perfil_completado = true;
        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '¡Perfil configurado con éxito!',
                'redirect_url' => route('dashboard'),
            ]);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Muestra la vista principal del perfil de un usuario.
     */
    public function show($id = null)
    {
        /** @var \App\Models\User $user */
        $user = $id ? User::findOrFail($id) : Auth::user();
        $authUserId = Auth::id();
        $isOwnProfile = $authUserId && $authUserId == $user->id_usuario;

        // Cargar publicaciones creadas por el usuario objetivo
        $posts = Publicacion::with(['evento', 'likes'])
            ->where('id_usuario', $user->id_usuario)
            ->orderBy('fecha', 'desc')
            ->get();

        // Cargar blogs creados por el usuario objetivo
        $blogs = $user->blogs()
            ->select(['id_blog', 'id_usuario', 'titulo', 'subtitulo', 'portada', 'vistas', 'created_at'])
            ->latest()
            ->get();

        // Conteo seguro de shows
        $showsCount = 0;
        if (Schema::hasTable('participaciones')) {
            try {
                $showsCount = Participacion::where('id_usuario', $user->id_usuario)->count();
            } catch (\Throwable $e) {
                $showsCount = 0;
            }
        }

        // Obtener listas y conteo de seguidores / seguidos con estado de seguimiento del usuario autenticado
        $followingList = [];
        $followersList = [];
        $isFollowingAuthor = false;

        if (Schema::hasTable('seguidores')) {
            try {
                // IDs que sigue actualmente el usuario autenticado
                $authFollowingIds = DB::table('seguidores')
                    ->where('id_seguidor', $authUserId)
                    ->pluck('id_seguido')
                    ->toArray();

                $followingList = DB::table('seguidores')
                    ->join('users', 'users.id_usuario', '=', 'seguidores.id_seguido')
                    ->where('seguidores.id_seguidor', $user->id_usuario)
                    ->select('users.id_usuario', 'users.nombre', 'users.handle', 'users.avatar')
                    ->get()
                    ->map(function ($person) use ($authFollowingIds) {
                        $person->is_following = in_array($person->id_usuario, $authFollowingIds);
                        return $person;
                    })
                    ->toArray();

                $followersList = DB::table('seguidores')
                    ->join('users', 'users.id_usuario', '=', 'seguidores.id_seguidor')
                    ->where('seguidores.id_seguido', $user->id_usuario)
                    ->select('users.id_usuario', 'users.nombre', 'users.handle', 'users.avatar')
                    ->get()
                    ->map(function ($person) use ($authFollowingIds) {
                        $person->is_following = in_array($person->id_usuario, $authFollowingIds);
                        return $person;
                    })
                    ->toArray();

                $isFollowingAuthor = DB::table('seguidores')
                    ->where('id_seguidor', $authUserId)
                    ->where('id_seguido', $user->id_usuario)
                    ->exists();
            } catch (\Throwable $e) {
                // Silenciar en caso de mantenimiento
            }
        }

        $viewName = view()->exists('profile.show') 
            ? 'profile.show' 
            : (view()->exists('profile.profile') ? 'profile.profile' : 'profile');

        return view($viewName, compact(
            'user', 
            'posts', 
            'blogs',
            'showsCount', 
            'followingList', 
            'followersList', 
            'isFollowingAuthor',
            'isOwnProfile'
        ));
    }

    /**
     * Alterna el estado de seguimiento a un usuario (Seguir / Dejar de seguir).
     */
    public function toggleFollow($id)
    {
        if (!Schema::hasTable('seguidores')) {
            return response()->json([
                'success' => false, 
                'message' => 'Tabla de seguidores no encontrada.'
            ], 400);
        }

        $targetUser = User::findOrFail($id);
        $authUserId = Auth::id();

        if ($authUserId == $targetUser->id_usuario) {
            return response()->json(['success' => false, 'message' => 'No puedes seguirte a ti mismo'], 400);
        }

        $exists = DB::table('seguidores')
            ->where('id_seguidor', $authUserId)
            ->where('id_seguido', $targetUser->id_usuario)
            ->exists();

        if ($exists) {
            DB::table('seguidores')
                ->where('id_seguidor', $authUserId)
                ->where('id_seguido', $targetUser->id_usuario)
                ->delete();
            $isFollowing = false;
        } else {
            DB::table('seguidores')->insert([
                'id_seguidor' => $authUserId,
                'id_seguido'  => $targetUser->id_usuario,
                'created_at'  => now(),
            ]);
            $isFollowing = true;
        }

        $followersCount = DB::table('seguidores')
            ->where('id_seguido', $targetUser->id_usuario)
            ->count();

        return response()->json([
            'success'         => true,
            'following'       => $isFollowing,
            'followers_count' => $followersCount,
        ]);
    }

    /**
     * Actualiza la información detallada del perfil de usuario.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'handle' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,handle,' . $user->id_usuario . ',id_usuario'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:160'],
            'spotify' => ['nullable', 'string', 'max:255'],
            'spotify_name' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'portada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],
            'generos' => ['nullable', 'array'],
            'artistas' => ['nullable', 'array'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('portada')) {
            if ($user->portada && Storage::disk('public')->exists($user->portada)) {
                Storage::disk('public')->delete($user->portada);
            }
            $user->portada = $request->file('portada')->store('portadas', 'public');
        }

        // Limpieza de formato para Instagram
        $instagram = !empty($validated['instagram']) ? ltrim($validated['instagram'], '@') : null;

        // Asignación directa limpia a las columnas de la tabla users
        $user->nombre = $validated['nombre'];
        $user->handle = strtolower($validated['handle']);
        $user->ciudad = $validated['ciudad'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->instagram = $instagram;
        $user->spotify = $validated['spotify'] ?? $user->spotify;
        $user->spotify_name = $validated['spotify_name'] ?? $user->spotify_name;
        $user->generos = $validated['generos'] ?? [];
        $user->artistas = $validated['artistas'] ?? [];

        $user->save();

        return redirect()->route('profile.show')->with('success', '¡Perfil actualizado con éxito!');
    }
}