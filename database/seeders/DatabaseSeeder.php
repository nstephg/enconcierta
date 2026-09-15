<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Evento;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id_rol' => 1, 'nombre_rol' => 'Usuario', 'descripcion' => 'Melómano estándar'],
            ['id_rol' => 2, 'nombre_rol' => 'Administrador', 'descripcion' => 'Administrador del sistema'],
        ]);

        $admin = User::create([
            'nombre' => 'Admin ENCONCIERTA',
            'email' => 'admin@enconcierta.com',
            'password' => Hash::make('password123'),
            'id_rol' => 2,
        ]);

        Evento::create([
            'nombre' => 'Morat World Tour',
            'artista' => 'Morat',
            'fecha' => '2026-09-14 20:00:00',
            'ciudad' => 'Bogotá',
            'lugar' => 'Movistar Arena',
            'imagen_portada' => 'https://images.unsplash.com/photo-1726555474758-37ffb98cadf5?w=800&h=600&fit=crop&auto=format',
            'id_usuario_creador' => $admin->id_usuario,
        ]);
    }
}