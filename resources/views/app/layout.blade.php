<!doctype html>
<html lang="{{ app()->getLocale() }}">
<?php
    $title_long = (!empty($titlebar) ? $titlebar.' - ' : (!empty($title) ? $title.' - ' : '')).config('app.name');
    $desc_default = 'Lemonadeへようこそ。Lemonadeはアサルトリリィ関連情報を取り扱う非公式のファンサイトです。';
    $resource_qs = '?v'.explode(' ', config('lemonade.version'))[0];
?>
<head>
    <meta charset="utf-8">
    <title>{{ $title_long }}</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.5.6/dialog-polyfill.min.css" integrity="sha512-J2+1q+RsZuJXabBfH1q/fgRr6jMy9By5SwVLk7bScEW7NFJkMUXxfeOyyxtDe6fsaJ4jsciexSlGrPYn9YbBIg==" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('css/lemonade.css').$resource_qs }}">
    <link rel="stylesheet" href="{{ asset('css/lemonade-mobile.css').$resource_qs }}" media="screen and (max-width:500px) and (orientation:portrait)">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
    <script src="{{ asset('js/clock.js').$resource_qs }}"></script>
    <script src="{{ asset('js/lemonade.js').$resource_qs }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.5.6/dialog-polyfill.min.js" integrity="sha512-qUIG93zKzcLBVD5RGRbx2PBmbVRu+tJIl+EPLTus0z8I1AMru9sQYdlf6cBacSzYmZVncB9rcc8rYBnazqgrxA==" crossorigin="anonymous"></script>
    <script src="{{ asset('service-worker.js') }}"></script>
    <script>
        if('serviceWorker' in navigator){
            navigator.serviceWorker.register('/service-worker.js').then(function(){
                console.log("[Service Worker] Registered");
            });
        }
    </script>
    <meta name="viewport" content="width=500">
    <meta name="description" content="{{ $ogp['description'] ?? $desc_default }}">
    <meta property="og:title" content="{{ $title_long }}">
    <meta property="og:description" content="{{ $ogp['description'] ?? $desc_default }}">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta name="twitter:card" content="summary_large_image">
    @if(!empty($ogp['image']))
        <meta property="og:image" content="{{ $ogp['image'] }}">
    @elseif(!empty($ogp['title']))
        <meta property="og:image" content="{{ route('ogp',['type' => $ogp['type'] ?? 'other', 'title' => $ogp['title']]) }}">
    @endif

    @yield('head')
</head>
<body class="fade-in" {{ !empty($pagetype) ? 'data-pagetype='.$pagetype : '' }}>
<header class="float-in">
    <div id="title">
        @if(!empty($previous))
            <a href="{{ url($previous['route'] ?? $previous) }}"><i class="{{ $previous['fa'] ?? 'fas fa-chevron-left' }}"></i></a>
        @else
            <a href="javascript:void(0)" title="戻る" id="pageBackButton" target="_self"><i class="fas fa-chevron-left"></i></a>
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
            {{ config('app.name').' '.(App::environment('production') ? ('Ver'.explode(' ',config('lemonade.version',''))[0]) : 'Rev.'.exec('git rev-parse --short HEAD')) }}
            <div style="font-size: 12px">{{ explode(' ',config('lemonade.version',''), 2)[1].(!App::environment('production') ? ' - '.App::environment() : '') }}</div>
        </div>
        <hr>
        @if(Auth::check())
            <a href="{{ route('admin.dashboard') }}" id="user-auth">
                <i class="fas fa-user-check"></i>
                <div style="display: inline-block">
                    Admin : {{ Auth::user()->name ?? 'Authed' }}
                    <div style="font-size: 12px">{{ Auth::user()->email }}</div>
                </div>
            </a>
        @else
            <a href="{{ route('admin.login') }}" id="user-auth">
                <i class="fas fa-user"></i>
                <div style="display: inline-block">
                    Guest
                    <div style="font-size: 12px">{{ 'guest'.substr(md5($_SERVER['REMOTE_ADDR']),0,9) }}</div>
                </div>
            </a>
        @endif
    </div>
    @if(config('app.debug'))
        <div class="indicator">Debug</div>
    @endif
    <hr>
    <nav>
        <a href="{{ url('/') }}"><i class="fas fa-home"></i>Top</a>
        <a href="{{ url('/lily') }}"><i class="fas fa-address-book"></i>Lily</a>
        <a href="{{ url('/legion') }}"><i class="fas fa-users"></i>Legion</a>
        <a href="{{ url('/menu') }}"><i class="fas fa-bars"></i>Menu</a>
    </nav>
</header>

@if(session('headError'))
    <div style="color: darkred; background: #ffe0e0; border-bottom: solid 1px red; padding: 5px; text-align: center; position: relative; z-index: 99">
        {!! nl2br(session('headError')) !!}
    </div>
@endif
@if(session('message'))
    <div class="flash-message">{{ session('message') }}</div>
@endif

@yield('main')

<footer>
    <p>{{ config('app.name') }}<span style="font-size: smaller;margin-left: 1em">{{ 'Ver'.config('lemonade.version') }}</span></p>
    <p style="font-size: smaller; color: dimgray;line-height: 2em">
        Dataset powered by AssaultLily unofficial database
        <a href="{{ config('lemonade.rdf.repository') }}" target="_blank">"assaultlily-rdf"</a><br>
        アイコン等の画像の一部は製作者から提供を受けたものを掲載しています。
        これらの画像の権利の一切は製作者に帰属します。
    </p>
    <div class="buttons" style="font-size: smaller">
        @if(!empty(config('lemonade.statusPageUrl')))
            <a href="{{ config('lemonade.statusPageUrl') }}" class="button smaller" target="_blank">Status</a>
        @endif
        <a href="{{ config('lemonade.repository') }}" class="button smaller" target="_blank">GitHub Repository</a>
        <a href="{{ 'https://twitter.com/'.config('lemonade.developer.twitter') }}" class="button smaller" target="_blank">Twitter {{ '@'.config('lemonade.developer.twitter') }}</a>
    </div>
</footer>

</body>
</html>
