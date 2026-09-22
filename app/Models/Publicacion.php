<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Publicacion extends Model
{
    use HasFactory;

    protected $table = 'publicaciones';
    protected $primaryKey = 'id_publicacion';

    protected $fillable = [
        'id_evento',
        'show_nombre',
        'id_usuario',
        'tipo',
        'contenido',
        'imagen',
        'gif_url',
        'imagenes',
        'gifs',
        'ubicacion',
        'encuesta',
    ];

    protected $casts = [
        'encuesta' => 'array',
        'imagenes' => 'array',
        'gifs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'id_evento');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'id_publicacion', 'id_publicacion');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_publicacion', 'id_publicacion');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('id_usuario', $userId)->exists();
    }

    public function getMediaListAttribute(): array
    {
        $items = [];
        $videoExts = ['mp4', 'mov', 'webm', 'ogg', 'avi'];

        $allMedia = $this->imagenes ?? ($this->imagen ? [$this->imagen] : []);

        if (is_array($allMedia)) {
            foreach ($allMedia as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $type = in_array($ext, $videoExts) ? 'video' : 'image';
                $items[] = [
                    'type' => $type,
                    'url' => Str::startsWith($file, 'http') ? $file : asset('storage/' . $file)
                ];
            }
        }

        $allGifs = $this->gifs ?? ($this->gif_url ? [$this->gif_url] : []);
        if (is_array($allGifs)) {
            foreach ($allGifs as $gif) {
                $items[] = ['type' => 'gif', 'url' => $gif];
            }
        }

        return $items;
    }
}