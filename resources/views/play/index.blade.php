<?php
/**
 * @var $plays array
 */

$ogp['title'] = '舞台公演一覧';
$ogp['description'] = '公演の一覧を表示します。現在'.count($plays).'の演目が登録されています。'
?>
@extends('app.layout', ['title' => '舞台公演一覧'])

@section('head')
    <style>
        .tag{
            position: relative;
            display: inline-block;
            font-size: smaller;
            color: white;
            box-shadow: inset 0 0 3px mediumpurple;
            background: #bca3ef;
            padding: 1px 10px;
            margin: 0 5px;
            border-bottom-right-radius: 5px;
        }
        .tag:before{
            position: absolute;
            top: 0;
            left: 0;
            content: '';
            width: 10px;
            height: 4px;
            background: mediumpurple;
        }
    </style>
@endsection

@section('main')
    <main>
        <div class="top-options">
            <div>
                ならべかえ : 初演日 降順
            </div>
            <div>
                <span class="info">演目登録数 : {{ count($plays) }}</span>
            </div>
        </div>
        @forelse($plays as $playKey => $play)
            <a class="book list-item-b" href="{{ route('play.show', ['play' => str_replace('lilyrdf:','',$playKey)]) }}">
                <div class="title">
                    {{ $play['schema:name'][0] }}
                    @if(!empty($play['lily:genre'][0]))<span class="tag">{{ $play['lily:genre'][0] }}</span>@endif
                </div>
                <div style="margin: .5em 0">
                    @if(!empty($play['lily:originalAuthor']))
                        <span class="tag">原作</span> {{ implode(', ',$play['lily:originalAuthor'] ?? array()) }}
                    @endif
                    @if(!empty($play['lily:scenarioWriter']))
                        <span class="tag">脚本</span> {{ implode(', ',$play['lily:scenarioWriter'] ?? array()) }}
                    @endif
                    @if(!empty($play['lily:episodeDirection']))
                        <span class="tag">演出</span> {{ implode(', ',$play['lily:episodeDirection'] ?? array()) }}
                    @endif
                    @if(!empty($play['lily:planning']))
                        <br><span class="tag">企画</span> {{ implode(', ',$play['lily:planning'] ?? array()) }}
                    @endif
                    @if(!empty($play['lily:organizer']))
                        <br><span class="tag">主催</span> {{ implode(', ',$play['lily:organizer'] ?? array()) }}
                    @endif
                </div>
                <div>
                    <span class="underline">初演 :
                        {{ convertDateString($play['schema:startDate'][0])->format('Y年n月j日') }}</span>
                </div>
            </a>
        @empty
        @endforelse
    </main>
@endsection
