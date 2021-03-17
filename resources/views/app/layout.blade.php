<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ (!empty($titlebar) ? $titlebar.' - ' : (!empty($title) ? $title.' - ' : '')).config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/lemonade.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
    <script src="{{ asset('js/clock.js') }}"></script>
    <meta name="viewport" content="width=1340">
    @yield('head')
</head>
<body {{ !empty($pagetype) ? 'data-pagetype='.$pagetype : '' }}>
<header>
    <div id="title">
        @if(!empty($previous))
            <a href="{{ url($previous['route'] ?? $previous) }}"><i class="fas fa-chevron-left"></i></a>
        @else
            <a href="{{ url('/') }}" title="Top"><i class="fas fa-home"></i></a>
        @endif
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
        <hr>
        @if(Auth::check())
            <a href="{{ route('admin.dashboard') }}" id="user-auth">
                <i class="fas fa-user-check"></i>{{ Auth::user()->name ?? 'Authed' }}</a>
        @else
            <a href="{{ route('admin.login') }}" id="user-auth">
                <i class="fas fa-user"></i>Guest</a>
        @endif
    </div>
    @if(config('app.debug'))
        <div class="indicator">Debug Mode</div>
    @endif
    <hr>
    <nav>
        <a href="{{ url('/') }}"><i class="fas fa-home"></i>Top</a>
        <a href="{{ url('/lily') }}"><i class="fas fa-address-book"></i>Lily</a>
    </nav>
</header>

@if(session('message'))
    <div class="flash-message">{{ session('message') }}</div>
@endif

@yield('main')

<footer>
    <p>{{ config('app.name') }}<span style="font-size: smaller;margin-left: 1em">{{ 'Ver'.config('lemonade.version') }}</span></p>
    <p style="font-size: smaller; color: gray;line-height: 2em">
        Dataset powered by AssaultLily unofficial database "assaultlily-rdf"
    </p>
    <div class="buttons" style="font-size: smaller">
        <a href="{{ config('lemonade.repository') }}" class="button smaller" target="_blank">GitHub Repository</a>
        <a href="{{ config('lemonade.mastodon') }}" class="button smaller" target="_blank" rel="me">Mastodon</a>
        <a href="{{ 'https://twitter.com/'.config('lemonade.developer.twitter') }}" class="button smaller" target="_blank">Twitter {{ '@'.config('lemonade.developer.twitter') }}</a>
    </div>
</footer>

</body>
</html>
