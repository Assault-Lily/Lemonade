@extends('app.layout', ['pagetype' => 'back-triangle'])

@section('head')
    <style>
        #home-pic-wrap{
            margin-top: -60px;
            padding-top: 60px;
            width: 100%;
            background-image: linear-gradient(rgba(255,255,255,0.8), rgba(255,255,255,0.5))
            , url("{{ asset('image/home-pic/background.jpg') }}");
            background-size: cover;
            background-position: top center;
        }
        #home-pic{
            display: block;
            max-width: 1500px;
            max-height: 500px;
            width: 100%;
            height: auto;
            margin: 0 auto;
            overflow: hidden;
            opacity: 1;
        }

        .white-box.big-buttons{
            padding: 20px;
        }
        .white-box.big-buttons > a{
            display: inline-block;
            width: 200px;
            color: #4E5B8A;
            text-decoration: none;
            font-weight: 500;
        }
        .white-box.big-buttons > a > i.fas{
            display: block;
            font-size: 4em;
            margin-bottom: 5px;
        }
        .white-box.big-buttons > hr{
            display: inline-block;
            border: none;
            width: 1px;
            height: 70px;
            background: #99A1C3;
        }
    </style>
@endsection

@section('main')
    <div id="home-pic-wrap">
        <img src="{{ asset('image/home-pic/background.jpg') }}" alt="Background" id="home-pic">
    </div>
    <main>
        <h1>Welcome to {{ config('app.name') }}</h1>
        <p>
            {{ config('app.name') }}へようこそ。
            {{ config('app.name') }}はアサルトリリィ関連情報を取り扱う非公式ファンサイトです。
        </p>
        <p>
            {{ config('app.name') }}はデータソースとしてアサルトリリィ非公式データベース
            <a href="https://github.com/fvh-P/assaultlily-rdf" style="font-weight: bold" target="_blank">assaultlily-rdf</a>
            の情報を基にサービスを提供しています。
        </p>
        <h2>Start</h2>
        <div class="white-box big-buttons" style="text-align: center">
            <a href="{{ route('lily.index') }}" >
                <i class="fas fa-address-book"></i>
                <span>リリィ一覧</span>
            </a>
            <hr>
            <a href="{{ route('legion.index') }}" >
                <i class="fas fa-users"></i>
                <span>レギオン一覧</span>
            </a>
        </div>
    </main>
@endsection
