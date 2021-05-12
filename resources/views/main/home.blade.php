<?php
$ogp['title'] = "アサルトリリィ非公式ファンサイト";
?>

@extends('app.layout', ['pagetype' => 'back-triangle', 'previous' => ['route' => url('/'), 'fa' => 'fas fa-home'], 'ogp' => $ogp])

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

        #rdf-updates{
            text-align: center
        }
        #rdf-updates > table{
            width: 100%;
        }

        @media screen and (max-width:500px) and (orientation:portrait){
            #home-pic{
                max-width: 500px;
            }

            .white-box.big-buttons > hr:nth-child(4n){
                display: none;
            }

            #rdf-updates > table{
                width: 1200px;
            }
            #rdf-updates{
                overflow-x: scroll;
            }
            #rdf-updates td{
                word-break: break-all;
            }
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

        #rdf-updates th{
            border-bottom: solid 1px #99A1C3;
            padding-bottom: 3px;
        }
        #rdf-updates td{
            border-bottom: dashed 1px lightgray;
        }
    </style>
@endsection

@section('main')
    <div id="home-pic-wrap">
        <img src="{{ asset('image/home-pic/background.jpg') }}" alt="Background" id="home-pic">
    </div>
    <main>
        @if(count($birthday) !== 0)
            <div class="window-a" style="margin-top: 15px;">
                <div class="header">Happy Birthday!</div>
                <div class="body">
                    <p style="text-align: center">
                        本日
                        <span style="font-size: large">{{ \Carbon\Carbon::now()->format('n月j日') }}</span>
                        がお誕生日のリリィがいます！
                    </p>
                    <div class="list" style="justify-content: space-around">
                        @forelse($birthday as $key => $lily)
                            <?php $legion = !empty($lily['lily:legion'][0]) ? $legions[$lily['lily:legion'][0]] ?? array() : array() ?>
                            @include('app.button_lily',['key' => $key, 'lily' => $lily, 'legion' => $legion, 'icons' => $images[$key] ?? array()])
                        @empty
                            <p style="text-align: center; color: darkred; margin: 3em auto">該当するデータがありません</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
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
            <hr>
            <a href="{{ route('charm.index') }}" >
                <i class="fas fa-pencil-ruler"></i>
                <span>CHARM一覧</span>
                <!-- TODO: https://github.com/miyacorata/Lemonade/issues/63 -->
            </a>
            <hr>
            <a href="{{ route('imedic') }}" >
                <i class="fas fa-spell-check"></i>
                <span>IME辞書生成</span>
            </a>
            <hr>
            <a href="{{ route('book.index') }}" >
                <i class="fas fa-book"></i>
                <span>書籍一覧</span>
            </a>
            <hr>
            <a href="{{ route('play.index') }}" >
                <i class="fas fa-street-view"></i>
                <span>舞台公演一覧</span>
            </a>
        </div>
        <h2>assaultlily-rdf 更新情報</h2>
        <div class="white-box" id="rdf-updates">
            <table>
                <thead>
                <tr>
                    <th>日時</th>
                    <th colspan="2">概要</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($rdf_feed))
                    @foreach($rdf_feed->entry as $entry)
                        <tr>
                            <td>{{ date('Y/m/d H:i', strtotime($entry->updated)) }}</td>
                            <td><a href="{{ $entry->link->attributes()->href }}" target="_blank" class="button smaller"
                                   title="GitHubのコミットログ詳細を開きます">GitHub</a></td>
                            <td style="text-align: left">{{ $entry->title }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="color: darkred">GitHubから情報を取得できませんでした</td>
                    </tr>
                @endif
                </tbody>
            </table>

        </div>
    </main>
@endsection
