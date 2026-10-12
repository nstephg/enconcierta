<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parche extends Model
{
    protected $table = 'parches';
    protected $primaryKey = 'id_parche';

    protected $fillable = [
        'id_evento',
        'id_usuario_creador',
        'nombre',
        'tipo_plan',
        'sector',
        'hora_encuentro',
        'cupos',
        'contenido',
        'plan_noche',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'id_evento');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'id_usuario_creador', 'id_usuario');
    }

    public function miembros()
    {
        return $this->hasMany(ParcheMiembro::class, 'id_parche', 'id_parche');
    }

    public function mensajes()
    {
        return $this->hasMany(ParcheMensaje::class, 'id_parche', 'id_parche');
    }
}