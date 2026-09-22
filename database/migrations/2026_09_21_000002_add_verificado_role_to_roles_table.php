<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['id_rol' => 3],
            [
                'nombre_rol' => 'Verificado',
                'descripcion' => 'Melómano verificado de la plataforma',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function down(): void
    {
        DB::table('roles')->where('id_rol', 3)->delete();
    }
};