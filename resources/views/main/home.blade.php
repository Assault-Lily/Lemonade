@extends('app.layout', ['pagetype' => 'back-triangle'])

@section('head')
    <style>
        #home-pic-wrap{
            width: 100%;
            background-image: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.6))
            , url("{{ asset('image/home-pic/background.jpg') }}");
            background-size: cover;
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
            {{ config('app.name') }}はアサルトリリィ関連情報を取り扱う非公式のデータベースです。
        </p>
    </main>
@endsection
