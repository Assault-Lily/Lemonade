<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/lemonade.css') }}">
    <script src="{{ asset('js/clock.js') }}"></script>
</head>
<body>
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
    <nav>
        <a href="{{ url('/') }}">Top</a>
        <a href="{{ url('/') }}">Lily</a>
    </nav>
</header>
</body>
</html>
