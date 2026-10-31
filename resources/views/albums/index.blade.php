@extends('layouts.app')

@section('title', 'Коллекция альбомов')

@section('content')
<section class="container intro-section" aria-labelledby="page-title">
    <p class="intro-eyebrow">Коллекция для долгого прослушивания</p>
    <h1 id="page-title">Альбомы, которые звучат вне времени</h1>
    <p class="intro-copy">От инди-рока до французской электроники. Открой любимый релиз или добавь в коллекцию что-то своё.</p>
    <a class="intro-link" href="#albums">Перейти к коллекции <span aria-hidden="true">↓</span></a>
</section>

<section class="container collection-section" id="albums" aria-labelledby="collection-title">
    <div class="section-heading">
        <div><p class="section-kicker">Альбомов в коллекции: {{ $albums->count() }}</p><h2 id="collection-title">Выбор Vinyl Room</h2></div>
        <a class="btn btn-primary" href="{{ route('albums.create') }}">Добавить альбом</a>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 row-cols-xxl-4 row-cols-xxxl-5 g-4 album-grid">
        @forelse ($albums as $album)
            <div class="col album-col">
                <article class="card album-card">
                    <a class="album-cover" href="{{ route('albums.show', $album) }}" aria-label="Подробнее об альбоме {{ $album->title }}">
                        <img class="card-img-top img-fluid" src="{{ asset('storage/'.$album->cover_path) }}"
                             alt="Обложка альбома {{ $album->title }}" width="900" height="900" loading="lazy">
                        <div class="album-labels"><span class="badge album-genre">{{ $album->genre }}</span><span class="badge album-year">{{ $album->release_date->format('Y') }}</span></div>
                    </a>
                    <div class="card-body d-flex flex-column">
                        <p class="album-edition">{{ $album->edition ?: 'Из коллекции Vinyl Room' }}</p>
                        <h3 class="card-title album-title"><a href="{{ route('albums.show', $album) }}">{{ $album->title }}</a></h3>
                        <p class="card-text album-artist">{{ $album->artist }}</p>
                        @include('albums.partials.tracks', ['tracks' => $album->tracks ?? []])
                        <div class="album-actions mt-auto">
                            <a class="btn btn-outline-primary" href="{{ route('albums.show', $album) }}">Подробнее</a>
                            <a class="btn btn-primary" href="{{ route('albums.edit', $album) }}">Изменить</a>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 mt-3">
                            <a class="album-stream-link mt-0" href="{{ $album->musicUrl() }}" target="_blank" rel="noopener noreferrer">Яндекс Музыка ↗</a>
                            @include('albums.partials.delete')
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12 w-100"><p class="empty-collection">Пока здесь пусто. Добавь первый альбом в свою коллекцию.</p></div>
        @endforelse
    </div>
</section>
@endsection
