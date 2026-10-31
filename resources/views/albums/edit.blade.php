@extends('layouts.app')
@section('title', 'Изменить '.$album->title)
@section('content')
<section class="container form-section">
    <a class="back-link" href="{{ route('albums.show', $album) }}">← К альбому</a>
    <h1 class="form-title">Изменить альбом</h1>
    <p class="text-secondary mb-4">Если не выбрать новую обложку, останется прежняя. Поля со звёздочкой обязательны.</p>
    <form action="{{ route('albums.update', $album) }}" method="POST" enctype="multipart/form-data" class="album-form needs-validation">
        @csrf
        @method('PUT')
        @include('albums.partials.form')
    </form>
</section>
@endsection
