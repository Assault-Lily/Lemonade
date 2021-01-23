<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <style>
        body{
            height: 100vh;
            width: 100%;
            margin: 0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Noto Serif', serif;
        }
        #eyecatch{
            width: 1200px;
            height: 500px;
            box-sizing: border-box;
            box-shadow: inset 0 0 5px 5px white;
            padding-left: 750px;
            padding-top: 200px;
            background-color: lightgray;
            background-image: linear-gradient(90deg, transparent, #ff9dae 130px, #ff9dae), url("{{ asset('image/riri.jpg') }}");
            background-size: 500px auto ,900px auto;
            background-repeat: no-repeat;
            background-position: top right, top left;
            overflow: hidden;
            text-align: center;
            color: white;
        }
        #eyecatch h1{
            margin: 0 0 10px;
            font-size: 45px;
            letter-spacing: .1em;
        }
    </style>
</head>
<body>

<div id="eyecatch">
    <h1>{{ config('app.name','Lemonade') }}</h1>
    <div>{{ 'Ver'.config('lemonade.version') }}</div>
</div>

</body>
</html>
