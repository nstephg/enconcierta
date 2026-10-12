<?php

namespace App\Http\Controllers;

use App\Models\Evento;

class HomeController extends Controller
{
    public function __invoke()
    {
        $eventos = Evento::withCount('participaciones')
            ->orderBy('fecha', 'asc')
            ->get();

        $eventoDestacado = $eventos->first();
        $eventosSecundarios = $eventos->skip(1);

        return view('welcome', compact('eventos', 'eventoDestacado', 'eventosSecundarios'));
    }

    public function index()
    {
        return $this->__invoke();
    }
}