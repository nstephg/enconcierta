<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Muestra la pantalla del editor de blogs.
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * Guarda un nuevo blog en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'    => 'required|string|max:255',
            'subtitulo' => 'nullable|string|max:500',
            'artista'   => 'nullable|string|max:255',
            'venue'     => 'nullable|string|max:255',
            'contenido' => 'required|string',
            'estilos'   => 'nullable|string',
            'portada'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $portadaPath = null;
        if ($request->hasFile('portada')) {
            $portadaPath = $request->file('portada')->store('blogs/portadas', 'public');
        }

        Blog::create([
            'id_usuario' => Auth::id(),
            'titulo'     => $validated['titulo'],
            'subtitulo'  => $validated['subtitulo'] ?? null,
            'portada'    => $portadaPath,
            'artista'    => $validated['artista'] ?? null,
            'venue'      => $validated['venue'] ?? null,
            'contenido'  => json_decode($validated['contenido'], true),
            'estilos'    => !empty($validated['estilos']) ? json_decode($validated['estilos'], true) : null,
        ]);

        return redirect()->route('profile.show')->with('success', '¡Tu blog ha sido publicado exitosamente!');
    }
}