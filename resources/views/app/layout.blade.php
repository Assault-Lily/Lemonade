<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ (!empty($title) ? $title.' - ' : '').config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/lemonade.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
    <script src="{{ asset('js/clock.js') }}"></script>
</head>
<body {{ !empty($pagetype) ? 'data-pagetype='.$pagetype : '' }}>
<header>
    <h1>{{ $title ?? 'Lemonade' }}</h1>
    <div id="info">
        <div id="date">
            <div id="date-month">---</div>
            <div id="date-date">--</div>
        </div>
        <div id="clock">--- --:--</div>
        <hr>
        <div id="system-info">
            {{ config('app.name').' '.config('lemonade.version','') }}
        </div>
    </div>
    <hr>
    <nav>
        <a href="{{ url('/') }}"><i class="fas fa-home"></i>Top</a>
        <a href="{{ url('/') }}"><i class="fas fa-users"></i>Lily</a>
    </nav>
</header>

@yield('main')

</body>
</html>
