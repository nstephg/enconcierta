<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcheMensaje extends Model
{
    protected $table = 'parche_mensajes';
    protected $primaryKey = 'id_mensaje';

    protected $fillable = [
        'id_parche',
        'id_usuario',
        'mensaje',
        'parent_id',
        'gif_url',
        'imagenes',
    ];

    protected $casts = [
        'imagenes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function parche()
    {
        return $this->belongsTo(Parche::class, 'id_parche', 'id_parche');
    }

    public function parent()
    {
        return $this->belongsTo(ParcheMensaje::class, 'parent_id', 'id_mensaje');
    }
}