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
            <a href="https://github.com/fvh-P/assaultlily-rdf" style="font-weight: bold">assaultlily-rdf</a>
            の情報を基にサービスを提供しています。
        </p>
    </main>
@endsection
