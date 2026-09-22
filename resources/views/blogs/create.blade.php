@extends('layouts.app')

@section('title', 'ENCONCIERTA — Crear Blog')

@section('content')
    <!-- Formulario oculto POST para persistir datos en MySQL vía Laravel -->
    <form id="realBlogForm" action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="text" name="titulo" id="laravel_title">
        <input type="text" name="subtitulo" id="laravel_subtitle">
        <input type="text" name="artista" id="laravel_artist">
        <input type="text" name="venue" id="laravel_venue">
        <input type="hidden" name="contenido" id="laravel_content">
        <input type="hidden" name="estilos" id="laravel_styles">
        <input type="file" name="portada" id="laravel_cover">
    </form>

    <!-- Invocación del Componente Blade del Editor Reactivo -->
    <x-blog-editor />
@endsection