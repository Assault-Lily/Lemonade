<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Under Maintenance</title>
    <link rel="stylesheet" href="{{ asset('css/lemonade.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
    <style>
        body{
            margin: 0;
            background-image: linear-gradient(#C5D3DC,#FFFFFF);
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans JP', sans-serif;
            font-weight: 500;
            color: #343434;
            position: relative;
            min-height: 100vh;
        }
        #id{
            position: absolute;
            top: 15px;
            left: 15px;
        }
        #infos-wrap{
            min-height: 100vh;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            justify-content: center;
            padding-bottom: 100px;
        }
        #infos{
            text-align: center;
        }
        #infos > h1{
            font-family: 'Noto Serif JP', serif;
            font-weight: 700;
            font-size: 120px;
            line-height: 125px;
            margin: 0;
            letter-spacing: .05em;
            color: #261616;
        }
        #infos > #subline{
            font-family: 'Noto Serif JP', serif;
            font-size: 25px;
            letter-spacing: .2em;
            margin-bottom: 30px;
            color: #261616;
        }
        .buttons > .button{
            margin: auto 40px;
        }
        footer{
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: absolute;
            width: 100%;
            bottom: 0;
            left: 0;
            padding: 15px;
            box-sizing: border-box;
            border: none;
            margin: 0;
        }
        footer > #links > a{
            display: inline-block;
            margin-right: 10px;
            vertical-align: top;
        }
        footer > #links > a > img{
            height: 50px;
        }
        footer > #links > a > i{
            font-size: 50px;
            color: black;
        }
    </style>
</head>
<body>
<div id="id">{{ $_SERVER['HTTP_HOST'] }}</div>
<div id="infos-wrap">
    <div id="infos">
        <h1>{{ config('app.name', 'Lemonade') }}</h1>
        <div id="subline">AssaultLily FanSite</div>
        <p>
            ただいまメンテナンス中です。メンテナンスの詳細は X で配信しているお知らせをご確認ください。
        </p>
        @if(!empty($exception->getMessage()) or !empty($message))
            <p style="color: #FA0A00">
                {{ $message ?? $exception->getMessage() }}
            </p>
        @endif
        <div class="buttons" style="margin-top: 40px">
            @if(!empty(config('lemonade.statusPageUrl')))
                <a href="{{ config('lemonade.statusPageUrl') }}" class="button primary" target="_blank">Status</a>
            @endif
            <a href="{{ 'https://x.com/'.config('lemonade.developer.twitter') }}" class="button primary" target="_blank">X</a>
            <a href="javascript:location.reload()" class="button">リロード</a>
        </div>
    </div>
</div>

<footer>
    <div id="links">
        <a href="https://github.com/{{ config('lemonade.developer.github') }}" target="_blank">
            <img src="https://github.com/{{ config('lemonade.developer.github') }}.png" alt="github">
        </a>
        <a href="{{ config('lemonade.repository') }}" target="_blank">
            <i class="fab fa-github"></i>
        </a>
    </div>
    <div>
        {{ 'Ver'.config('lemonade.version','').(App::environment('production') ? '' : ' - Rev.'.exec('git rev-parse --short HEAD')) }}
    </div>
</footer>
</body>
</html>
