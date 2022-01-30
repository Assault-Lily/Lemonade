<?php
/**
 * @var $series Array
 */
$ogp['title'] = 'アニメシリーズ一覧';
$ogp['description'] = 'アニメシリーズの一覧を表示します。現在'.count($series).'が登録されています。'
?>

@extends('app.layout', ['title' => 'アニメシリーズ一覧', 'ogp' => $ogp])

@section('main')
    <main>
        <div class="top-options">
            <div>リソースキー順</div>
            <div><span class="info">登録数 : {{ count($series) }}</span></div>
        </div>
        <div id="series">
            @forelse($series as $s_key => $s)
                <a class="book list-item-b" href="{{ route('anime.series.show', ['series' => str_replace('lilyrdf:','',$s_key)]) }}">
                    <div class="title">
                        {{ $s['schema:name'][0] }}
                        @if(!empty($s['schema:name@en'][0]))
                            <span style="font-size: small; margin-left: 1em;">- {{ $s['schema:name@en'][0] }} -</span>
                        @endif
                    </div>
                    <div style="margin: .5em 0">
                        @if(!empty($s['lily:originalAuthor']))
                            <span class="tag">原作</span> {{ implode(', ',$s['lily:originalAuthor'] ?? array()) }}
                        @endif
                        @if(!empty($s['lily:scenarioWriter']))
                            <span class="tag">脚本</span> {{ implode(', ',$s['lily:scenarioWriter'] ?? array()) }}
                        @endif
                        @if(!empty($s['lily:chiefEpisodeDirection']))
                            <span class="tag">チーフ演出</span> {{ implode(', ',$s['lily:chiefEpisodeDirection'] ?? array()) }}
                        @endif
                        @if(!empty($s['lily:director']))
                            <br><span class="tag">監督</span> {{ implode(', ',$s['lily:director'] ?? array()) }}
                        @endif
                        @if(!empty($s['lily:animationProduction']))
                            <span class="tag">アニメーション制作</span> {{ implode(', ',$s['lily:animationProduction'] ?? array()) }}
                        @endif
                    </div>
                    <div>
                    <span class="underline" title="初回放送日基準">放送期間 :
                        {{ convertDateString($s['schema:startDate'][0])->isoFormat('YYYY年M月D日') }}
                        {{ !empty($s['schema:endDate']) ? ' - '.convertDateString($s['schema:endDate'][0])->isoFormat('YYYY年M月D日') : '' }}
                    </span>
                    </div>
                </a>
            @empty
            @endforelse
        </div>
        @if(config('app.debug'))@dump($series)@endif
    </main>
@endsection
