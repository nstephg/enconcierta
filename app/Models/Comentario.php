<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentarios';
    protected $primaryKey = 'id_comentario';

    protected $fillable = [
        'id_publicacion',
        'parent_id',
        'id_usuario',
        'contenido',
        'imagenes',
        'gifs',
    ];

    protected $casts = [
        'imagenes' => 'array',
        'gifs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'id_publicacion', 'id_publicacion');
    }

    public function parent()
    {
        return $this->belongsTo(Comentario::class, 'parent_id', 'id_comentario');
    }

    public function respuestas()
    {
        return $this->hasMany(Comentario::class, 'parent_id', 'id_comentario')
            ->with(['user', 'likes', 'respuestas'])
            ->oldest();
    }

    public function likes()
    {
        return $this->hasMany(ComentarioLike::class, 'id_comentario', 'id_comentario');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('id_usuario', $userId)->exists();
    }

    public function getAllDescendants()
    {
        $descendants = collect();
        foreach ($this->respuestas as $reply) {
            $descendants->push($reply);
            $descendants = $descendants->merge($reply->getAllDescendants());
        }
        return $descendants->sortBy('created_at');
    }

    public function getMediaListAttribute(): array
    {
        $items = [];
        $videoExts = ['mp4', 'mov', 'webm', 'ogg', 'avi'];

        if (!empty($this->imagenes) && is_array($this->imagenes)) {
            foreach ($this->imagenes as $img) {
                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                $type = in_array($ext, $videoExts) ? 'video' : 'image';
                $items[] = [
                    'type' => $type,
                    'url' => Str::startsWith($img, 'http') ? $img : asset('storage/' . $img)
                ];
            }
        }
        if (!empty($this->gifs) && is_array($this->gifs)) {
            foreach ($this->gifs as $gif) {
                $items[] = ['type' => 'gif', 'url' => $gif];
            }
        }
        return $items;
    }
}