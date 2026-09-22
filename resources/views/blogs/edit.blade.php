@extends('layouts.app')

@section('title', 'ENCONCIERTA — Editar Blog')

@section('content')
    <!-- Formulario oculto PUT para actualizar datos en MySQL vía Laravel -->
    <form id="realBlogForm" action="{{ route('blogs.update', $blog->id_blog) }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        @method('PUT')
        <input type="text" name="titulo" id="laravel_title">
        <input type="text" name="subtitulo" id="laravel_subtitle">
        <input type="text" name="artista" id="laravel_artist">
        <input type="text" name="venue" id="laravel_venue">
        <input type="hidden" name="contenido" id="laravel_content">
        <input type="hidden" name="estilos" id="laravel_styles">
        <input type="file" name="portada" id="laravel_cover">
    </form>

    <!-- Invocación del Editor con la variable del blog a editar -->
    <x-blog-editor :blog="$blog" />
@endsection