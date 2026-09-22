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
}