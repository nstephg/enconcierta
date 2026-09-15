<?php

namespace App\Http\Controllers;

use App\Models\Evento;

class HomeController extends Controller
{
    public function __invoke()
    {
        // Trae los eventos ordenados por fecha con el número real de participantes asociados
        $eventos = Evento::withCount('participantes')
            ->orderBy('fecha', 'asc')
            ->get();

        // El primer evento será el destacado y los demás irán al listado lateral
        $eventoDestacado = $eventos->first();
        $eventosSecundarios = $eventos->skip(1);

        return view('welcome', compact('eventoDestacado', 'eventosSecundarios'));
    }
}