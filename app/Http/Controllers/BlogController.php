<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Muestra el detalle de un blog en pantalla completa.
     */
    public function show($id)
    {
        $blog = Blog::findOrFail($id);

        if (isset($blog->vistas)) {
            $blog->increment('vistas');
        }

        $isOwner = Auth::check() && Auth::id() === $blog->id_usuario;

        return view('blogs.show', compact('blog', 'isOwner'));
    }

    /**
     * Muestra el editor reactivo para crear un nuevo blog.
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * Almacena el blog en la base de datos MySQL.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'    => ['required', 'string', 'max:255'],
            'subtitulo' => ['nullable', 'string', 'max:255'],
            'artista'   => ['nullable', 'string', 'max:255'],
            'venue'     => ['nullable', 'string', 'max:255'],
            'contenido' => ['required'],
            'estilos'   => ['nullable'],
            'portada'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],
        ]);

        $portadaPath = null;
        if ($request->hasFile('portada')) {
            $portadaPath = $request->file('portada')->store('blogs/portadas', 'public');
        }

        $contenido = is_string($validated['contenido']) 
            ? json_decode($validated['contenido'], true) 
            : $validated['contenido'];

        $estilos = !empty($validated['estilos']) 
            ? (is_string($validated['estilos']) ? json_decode($validated['estilos'], true) : $validated['estilos'])
            : null;

        $blog = Blog::create([
            'id_usuario' => Auth::id(),
            'titulo'     => $validated['titulo'],
            'subtitulo'  => $validated['subtitulo'] ?? null,
            'artista'    => $validated['artista'] ?? null,
            'venue'      => $validated['venue'] ?? null,
            'portada'    => $portadaPath,
            'contenido'  => $contenido,
            'estilos'    => $estilos,
            'vistas'     => 0,
        ]);

        return redirect()->route('blogs.show', $blog->id_blog);
    }

    /**
     * Formulario de edición (Propietario).
     */
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);

        if (Auth::id() !== $blog->id_usuario) {
            abort(403, 'Acción no autorizada.');
        }

        return view('blogs.edit', compact('blog'));
    }

    /**
     * Actualiza un blog existente.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        if (Auth::id() !== $blog->id_usuario) {
            abort(403, 'Acción no autorizada.');
        }

        $blog->titulo = $request->input('titulo');
        $blog->subtitulo = $request->input('subtitulo');
        $blog->artista = $request->input('artista');
        $blog->venue = $request->input('venue');
        $blog->contenido = $request->input('contenido'); // Carga JSON enviada por Alpine
        $blog->estilos = $request->input('estilos');     // Carga JSON enviada por Alpine

        if ($request->hasFile('portada')) {
            $blog->portada = $request->file('portada')->store('portadas', 'public');
        }

        $blog->save();

        return redirect()->route('blogs.show', $blog->id_blog);
    }

    /**
     * Elimina permanentemente un blog de la base de datos.
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if (\Illuminate\Support\Facades\Auth::id() !== $blog->id_usuario) {
            abort(403, 'Acción no autorizada.');
        }

        if ($blog->portada && \Illuminate\Support\Facades\Storage::disk('public')->exists($blog->portada)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($blog->portada);
        }

        $blog->delete();

        return redirect()->route('dashboard')->with('success', 'Blog eliminado correctamente.');
    }
}