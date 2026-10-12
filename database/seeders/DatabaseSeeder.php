<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('participaciones')->truncate();
        DB::table('eventos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $admin = User::first() ?? User::factory()->create();

        $fep = Evento::create([
            'id_evento' => 1,
            'nombre' => 'Festival Estéreo Picnic',
            'artista' => 'Festival Estéreo Picnic',
            'fecha' => '2027-03-25 14:00:00',
            'ciudad' => 'Bogotá',
            'lugar' => 'Parque Simón Bolívar',
            'imagen_portada' => 'https://los40.com.co/resizer/v2/DG2Y3DE2GZDX5L7LBHLPOHRJXI.png?auth=a2ebd9538dc38966896f6662d417e0033fb0a70919aa38d35b3b4580bd0bc0f0&quality=70&width=1200&height=544&smart=true',
            'id_usuario_creador' => $admin->id_usuario,
        ]);

        $usuariosReales = User::pluck('id_usuario');
        foreach ($usuariosReales as $idUsuario) {
            DB::table('participaciones')->insert([
                'id_usuario' => $idUsuario,
                'id_evento' => $fep->id_evento,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}