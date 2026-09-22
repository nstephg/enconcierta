<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';
    protected $primaryKey = 'id_blog';

    protected $fillable = [
        'id_usuario',
        'titulo',
        'subtitulo',
        'portada',
        'artista',
        'venue',
        'contenido',
        'estilos',
        'vistas',
    ];

    protected $casts = [
        'contenido' => 'array',
        'estilos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}