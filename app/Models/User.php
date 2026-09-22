<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'email',
        'handle',
        'ciudad',
        'avatar',
        'portada',
        'bio',
        'instagram',
        'spotify',
        'spotify_name',
        'generos',
        'artistas',
        'perfil_completado',
        'password',
        'id_rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'generos' => 'array',
        'artistas' => 'array',
        'perfil_completado' => 'boolean',
    ];

    public function getAvatarUrlAttribute()
    {
        if (!empty($this->avatar)) {
            return str_starts_with($this->avatar, 'http') 
                ? $this->avatar 
                : asset('storage/' . $this->avatar);
        }
        return asset('images/default-avatar.svg');
    }

    public function getCoverUrlAttribute()
    {
        if (!empty($this->portada)) {
            return str_starts_with($this->portada, 'http') 
                ? $this->portada 
                : asset('storage/' . $this->portada);
        }
        return 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=1000&h=350&fit=crop&auto=format';
    }

    public function getEsVerificadoAttribute(): bool
    {
        // Rol 2: Administrador | Rol 3: Verificado
        return in_array($this->id_rol, [2, 3]);
    }

    /**
     * Usuarios que siguen a este usuario (Seguidores).
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'id_seguido', 'id_seguidor');
    }

    /**
     * Usuarios a los que este usuario sigue (Siguiendo).
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'id_seguidor', 'id_seguido');
    }

    /**
     * Relación con los blogs creados por el usuario.
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'id_usuario', 'id_usuario');
    }
}