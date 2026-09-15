<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id('id_evento');
            $table->string('nombre');
            $table->string('artista');
            $table->dateTime('fecha');
            $table->string('ciudad');
            $table->string('lugar');
            $table->string('imagen_portada')->nullable();
            $table->foreignId('id_usuario_creador')->constrained('users', 'id_usuario');
            $table->timestamps();
        });

        Schema::create('participaciones', function (Blueprint $table) {
            $table->id('id_participacion');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_evento')->constrained('eventos', 'id_evento')->onDelete('cascade');
            $table->timestamp('fecha_union')->useCurrent();
            $table->timestamps();
        });

        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id('id_publicacion');
            $table->foreignId('id_evento')->constrained('eventos', 'id_evento')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade');
            $table->text('contenido');
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });

        Schema::create('galeria_fotos', function (Blueprint $table) {
            $table->id('id_foto');
            $table->foreignId('id_evento')->constrained('eventos', 'id_evento')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade');
            $table->string('url_imagen');
            $table->timestamp('fecha_subida')->useCurrent();
            $table->timestamps();
        });

        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->foreignId('id_publicacion')->constrained('publicaciones', 'id_publicacion')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade');
            $table->text('contenido');
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
        Schema::dropIfExists('galeria_fotos');
        Schema::dropIfExists('publicaciones');
        Schema::dropIfExists('participaciones');
        Schema::dropIfExists('eventos');
    }
};