<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $upcomingEvents = Evento::orderBy('fecha', 'asc')->take(3)->get();

        $posts = [
            [
                'id' => 1,
                'type' => 'review',
                'user' => [
                    'name' => 'Sebastián Mora',
                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&auto=format',
                    'verified' => true
                ],
                'show' => [
                    'artist' => 'Feid',
                    'venue' => 'La Macarena · Medellín',
                    'date' => 'Dom 22 Sep',
                    'genre' => 'Urbano'
                ],
                'content' => 'Tres años buscando parche para Feid y ENCONCIERTA lo hizo en 20 minutos. Terminamos en primera fila, con gente de Bogotá y Cali.',
                'img' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=700&h=400&fit=crop&auto=format',
                'likes' => 284,
                'comments' => 31,
                'time' => 'hace 2h'
            ]
        ];

        $suggestedUsers = [
            ['name' => 'Juan Pulido', 'genre' => 'Electrónica', 'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&h=80&fit=crop&auto=format'],
            ['name' => 'Sara Ríos', 'genre' => 'Jazz / Soul', 'avatar' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=80&h=80&fit=crop&auto=format']
        ];

        return view('dashboard.feed', compact('posts', 'upcomingEvents', 'suggestedUsers'));
    }
}