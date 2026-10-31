@extends('layouts.app')
@section('title', 'Добавить альбом')
@section('content')
<section class="container form-section">
    <a class="back-link" href="{{ route('albums.index') }}">← К коллекции</a>
    <h1 class="form-title">Добавить альбом</h1>
    <p class="text-secondary mb-4">Заполни поля и выбери обложку. Поля со звёздочкой обязательны.</p>
    <form action="{{ route('albums.store') }}" method="POST" enctype="multipart/form-data" class="album-form needs-validation">
        @csrf
        @include('albums.partials.form')
    </form>
</section>
@endsection
