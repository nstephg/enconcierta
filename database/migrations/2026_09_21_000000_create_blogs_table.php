<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id('id_blog');
            $table->unsignedBigInteger('id_usuario');
            $table->string('titulo');
            $table->string('subtitulo')->nullable();
            $table->string('portada')->nullable();
            $table->string('artista')->nullable();
            $table->string('venue')->nullable();
            $table->json('contenido');
            $table->json('estilos')->nullable();
            $table->unsignedInteger('vistas')->default(0);
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};