<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';
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

    public function participaciones()
    {
        return $this->belongsToMany(User::class, 'participaciones', 'id_evento', 'id_usuario');
    }

    public function parches()
    {
        return $this->hasMany(Parche::class, 'id_evento', 'id_evento');
    }

    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class, 'id_evento', 'id_evento');
    }
}