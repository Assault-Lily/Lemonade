<?php
/**
 * @var $details array
 * @var $series string
 */
$ts = 'lilyrdf:'.$series;
$fontOverride = $details[$ts]['schema:name@en'][0] === 'Assault Lily BOUQUET'
    ? "style=\"font-family: 'Noto Serif JP', serif; font-weight: 500;\"" : null;
?>

@extends('app.layout', ['title' => 'シリーズ詳細'])

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;500;700&display=swap" rel="stylesheet">
@endsection

@section('main')
    <main>
        <div {!! $fontOverride !!}>
            <h1>{{ $details[$ts]['schema:name'][0] }}</h1>
            @if(!empty($details[$ts]['schema:abstract'][0]))
                <p style="line-height: 1.5em">{!! nl2br($details[$ts]['schema:abstract'][0]) !!}</p>
            @endif
        </div>
        <table class="table">
            <tr><th>原作</th><td>{{ implode(', ', $details[$ts]['lily:originalAuthor'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>キャラクターデザイン</th><td>{{ implode(', ', $details[$ts]['lily:characterDesign'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>サブキャラクターデザイン</th><td>{{ implode(', ', $details[$ts]['lily:subCharacterDesign'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>総作画監督</th><td>{{ implode(', ', $details[$ts]['lily:chiefAnimationSupervisor'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>アクションディレクター</th><td>{{ implode(', ', $details[$ts]['lily:actionDirector'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>副監督</th><td>{{ implode(', ', $details[$ts]['lily:assistantDirector'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>チーフ演出</th><td>{{ implode(', ', $details[$ts]['lily:chiefEpisodeDirection'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>CHARMデザイン</th><td>{{ implode(', ', $details[$ts]['lily:charmDesign'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>HUGEデザイン</th><td>{{ implode(', ', $details[$ts]['lily:hugeDesign'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>美術監督</th><td>{{ implode(', ', $details[$ts]['lily:backgroundArtDirector'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>色彩設計</th><td>{{ implode(', ', $details[$ts]['lily:colorDesign'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>撮影監督</th><td>{{ implode(', ', $details[$ts]['lily:compositingDirector'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>3DCGディレクター</th><td>{{ implode(', ', $details[$ts]['lily:CGDirector'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>編集</th><td>{{ implode(', ', $details[$ts]['lily:editor'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>音響監督</th><td>{{ implode(', ', $details[$ts]['lily:soundDirector'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>音楽</th><td>{{ implode(', ', $details[$ts]['lily:music'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>音楽プロデューサー</th><td>{{ implode(', ', $details[$ts]['lily:musicProducer'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>音楽制作</th><td>{{ implode(', ', $details[$ts]['lily:musicProduction'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>プロデューサー</th><td>{{ implode(', ', $details[$ts]['lily:producer'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>アニメーションプロデューサー</th><td>{{ implode(', ', $details[$ts]['lily:animationProducer'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>監督</th><td>{{ implode(', ', $details[$ts]['lily:director'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>アニメーション制作</th><td>{{ implode(', ', $details[$ts]['lily:animationProduction'] ?? array()) ?: 'N/A' }}</td></tr>
            <tr><th>制作</th><td>{{ implode(', ', $details[$ts]['lily:production'] ?? array()) ?: 'N/A' }}</td></tr>
        </table>

        @if(config('app.debug')) @dump($details) @endif
    </main>
@endsection
