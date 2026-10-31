@extends('layouts.app')

@section('title', $album->title)

@section('content')
<section class="container detail-section">
    <a class="back-link" href="{{ route('albums.index') }}">← К коллекции</a>
    <div class="row g-4 g-lg-5 mt-1 align-items-start">
        <div class="col-md-5">
            <img class="detail-cover img-fluid" src="{{ asset('storage/'.$album->cover_path) }}"
                 alt="Обложка альбома {{ $album->title }}" width="900" height="900">
        </div>
        <div class="col-md-7">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="badge album-genre">{{ $album->genre }}</span>
                <time class="text-secondary" datetime="{{ $album->release_date->format('Y-m-d') }}">{{ $album->release_date->format('d.m.Y') }}</time>
            </div>
            <p class="album-edition">{{ $album->edition }}</p>
            <h1 class="detail-title">{{ $album->title }}</h1>
            <p class="detail-artist">{{ $album->artist }}</p>
            <p class="detail-description">{{ $album->description }}</p>
            @if ($album->sound_notes)
                <h2 class="h6 fw-bold mt-4">О звучании</h2>
                <p class="text-secondary">{{ $album->sound_notes }}</p>
            @endif
            @if (count($album->tracks ?? []))
                <h2 class="h6 fw-bold mt-4">Избранные композиции</h2>
                @include('albums.partials.tracks', ['tracks' => $album->tracks])
            @endif
            <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
                <a class="btn btn-primary" href="{{ route('albums.edit', $album) }}">Изменить альбом</a>
                <a class="btn btn-outline-primary" href="{{ $album->musicUrl() }}" target="_blank" rel="noopener noreferrer">Яндекс Музыка ↗</a>
                @include('albums.partials.delete')
            </div>
        </div>
    </div>
    <nav class="album-pagination" aria-label="Другие альбомы">
        <div>@if ($previous)<a class="btn btn-outline-secondary" data-previous-album href="{{ route('albums.show', $previous) }}">← {{ $previous->title }}</a>@endif</div>
        <div>@if ($next)<a class="btn btn-outline-secondary" data-next-album href="{{ route('albums.show', $next) }}">{{ $next->title }} →</a>@endif</div>
    </nav>
</section>
@endsection
