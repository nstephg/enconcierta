<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';
    protected $primaryKey = 'id_like';

    protected $fillable = [
        'id_usuario',
        'id_publicacion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'id_publicacion', 'id_publicacion');
    }

    public function toggleLike($idPublicacion)
    {
        $publicacion = Publicacion::findOrFail($idPublicacion);
        $user = auth()->user();

        $like = $publicacion->likes()->where('id_usuario', $user->id_usuario)->first();

        if ($like) {
            $like->delete();
        } else {
            $publicacion->likes()->create(['id_usuario' => $user->id_usuario]);

            // Disparar Notificación
            Notificacion::crear(
                idUsuario: $publicacion->id_usuario,
                idActor: $user->id_usuario,
                tipo: 'vibro',
                accion: 'vibró con tu momento de',
                subject: $publicacion->show_nombre ?? 'tu publicación',
                idPublicacion: $publicacion->id_publicacion
            );
        }
    }
}