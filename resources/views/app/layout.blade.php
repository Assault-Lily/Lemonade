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
    @yield('head')
</head>
<body {{ !empty($pagetype) ? 'data-pagetype='.$pagetype : '' }}>
<header>
    <div id="title">
        <a href="{{ url('/') }}"><i class="fas fa-home"></i>{{--<i class="fas fa-chevron-left">--}}</i></a>
        <h1>
            {{ $title ?? 'Lemonade' }}
        </h1>
    </div>
    <div id="info">
        <div id="date">
            <div id="date-month">---</div>
            <div id="date-date">--</div>
        </div>
        <div id="clock-wrap">
            <div id="clock">--:--</div>
            <div id="clock-progress"><div id="clock-value"></div></div>
        </div>

        <hr>
        <div id="system-info">
            {{ config('app.name').' Ver'.config('lemonade.version','') }}
        </div>
        @if(!App::environment('production'))
            <div class="indicator">ENV : {{ App::environment() }}</div>
        @endif
        @if(config('app.debug'))
            <div class="indicator">Debug Mode</div>
        @endif
    </div>
    <hr>
    <nav>
        <a href="{{ url('/') }}"><i class="fas fa-home"></i>Top</a>
        <a href="{{ url('/lily') }}"><i class="fas fa-address-book"></i>Lily</a>
    </nav>
</header>

@yield('main')

</body>
</html>
