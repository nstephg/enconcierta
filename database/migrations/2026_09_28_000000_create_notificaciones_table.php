<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('id_notificacion');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_actor')->constrained('users', 'id_usuario')->onDelete('cascade');
            $table->enum('tipo', ['vibro', 'comment', 'reply', 'tag', 'repost', 'follow', 'parche']);
            $table->string('accion');
            $table->string('subject')->nullable();
            $table->foreignId('id_publicacion')->nullable()->constrained('publicaciones', 'id_publicacion')->onDelete('cascade');
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};