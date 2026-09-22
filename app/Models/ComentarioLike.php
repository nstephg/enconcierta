<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioLike extends Model
{
    use HasFactory;

    protected $table = 'comentario_likes';
    protected $primaryKey = 'id_like';

    protected $fillable = [
        'id_usuario',
        'id_comentario',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function comentario()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario', 'id_comentario');
    }
}