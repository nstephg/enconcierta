<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';

    protected $fillable = [
        'id_usuario',
        'id_actor',
        'tipo',
        'accion',
        'subject',
        'id_publicacion',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'id_actor', 'id_usuario');
    }

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'id_publicacion', 'id_publicacion');
    }

    /**
     * Helper para registrar notificaciones previniendo la auto-notificación
     */
    public static function crear(int $idUsuario, int $idActor, string $tipo, string $accion, ?string $subject = null, ?int $idPublicacion = null)
    {
        if ($idUsuario === $idActor) {
            return null;
        }

        return self::create([
            'id_usuario' => $idUsuario,
            'id_actor' => $idActor,
            'tipo' => $tipo,
            'accion' => $accion,
            'subject' => $subject,
            'id_publicacion' => $idPublicacion,
            'leido' => false,
        ]);
    }
}