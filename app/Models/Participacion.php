<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participacion extends Model
{
    use HasFactory;

    protected $table = 'participaciones';
    protected $primaryKey = 'id_participacion';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_evento',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'id_evento');
    }
}