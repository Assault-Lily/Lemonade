<?php
$ogp['title'] = "サイトメニュー";
?>

@extends('app.layout', ['title' => 'メニュー'])

@section('head')
    <style>
        #top-link{
            display: none;
        }
        @media screen and (max-width: 500px) and (orientation: portrait){
            #top-link{
                display: block;
            }
        }
    </style>
@endsection

@section('main')
    <main>
        <p class="center" style="margin: 30px 0;font-size: large">
            どんな情報をお探しですか？
            <a href="{{ config('lemonade.rdf.repository') }}" target="_blank">assaultlily-rdf</a> と
            {{ config('app.name') }} がお手伝いします。
        </p>
        <div class="buttons three" id="top-link">
            <a href="{{ url('/') }}" class="button">トップページ</a>
        </div>
        <h2>リリィ・レギオンを探す</h2>
        <div class="buttons three">
            <a href="{{ route('lily.index') }}" class="button">リリィ一覧</a>
            <a href="{{ route('legion.index') }}" class="button">レギオン一覧</a>
        </div>

        <h2>メディアを探す</h2>
        <div class="buttons three">
            <a href="{{ route('book.index') }}" class="button">書籍一覧</a>
            <a href="{{ route('play.index') }}" class="button">舞台一覧</a>
        </div>

        <h2>プチ機能</h2>
        <div class="buttons three">
            <a href="{{ '/imedic' }}" class="button">アサルトリリィIME辞書生成</a>
        </div>

        <h2>このウェブサイトについて</h2>
        <div class="buttons three">
            <a href="{{ config('lemonade.mastodon.server').'/'.config('lemonade.mastodon.account') }}" class="button" target="_blank">
                Mastodon {{ config('lemonade.mastodon.account') }}
            </a>
            <a href="{{ 'https://twitter.com/'.config('lemonade.developer.twitter') }}" class="button" target="_blank">開発者のTwitter</a>
            <br>
            <a href="{{ config('lemonade.repository') }}" class="button" target="_blank">Lemonade - GitHubリポジトリ</a>
            <a href="{{ config('lemonade.rdf.repository') }}" class="button" target="_blank">assaultlily-rdf - GitHubリポジトリ</a>
        </div>
    </main>
@endsection
