<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#17151c">
    <title>@yield('title', 'Коллекция альбомов') — Vinyl Room</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script defer src="{{ mix('js/app.js') }}"></script>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark site-navbar" aria-label="Главная навигация">
        <div class="container">
            <a class="navbar-brand d-inline-flex align-items-center gap-2" href="{{ route('albums.index') }}">
                <span class="brand-mark" aria-hidden="true">V</span><span>Vinyl Room</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation"
                    aria-controls="mainNavigation" aria-expanded="false" aria-label="Открыть меню">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('albums.index') }}">Коллекция</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light rounded-pill nav-cta" href="{{ route('albums.create') }}">Добавить альбом</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main>
    @if (session('success'))
        <div class="container pt-4"><div class="alert alert-success mb-0" role="status">{{ session('success') }}</div></div>
    @endif
    @yield('content')
</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <p>Брик Кира · Лабораторная работа №3</p>
        <p>Vinyl Room · Коллекция музыкальных альбомов</p>
    </div>
</footer>
</body>
</html>
