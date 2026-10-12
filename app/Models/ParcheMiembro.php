<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcheMiembro extends Model
{
    protected $table = 'parche_miembros';
    protected $primaryKey = 'id_parche_miembro';

    protected $fillable = [
        'id_parche',
        'id_usuario',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function parche()
    {
        return $this->belongsTo(Parche::class, 'id_parche', 'id_parche');
    }
}