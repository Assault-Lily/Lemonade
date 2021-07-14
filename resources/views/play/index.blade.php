<?php
/**
 * @var $plays array
 */

$ogp['title'] = '舞台公演一覧';
$ogp['description'] = '公演の一覧を表示します。現在'.count($plays).'の演目が登録されています。'
?>
@extends('app.layout', ['title' => '舞台公演一覧'])

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
