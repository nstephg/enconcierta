<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $primaryKey = 'id_evento';

    protected $fillable = [
        'nombre',
        'artista',
        'fecha',
        'ciudad',
        'lugar',
        'imagen_portada',
        'id_usuario_creador',
    ];

    public function participantes()
    {
        return $this->belongsToMany(User::class, 'participaciones', 'id_evento', 'id_usuario');
    }
}