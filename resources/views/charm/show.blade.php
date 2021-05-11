<?php
/**
 * @var $charm array
 * @var $slug string
 */
$cs = 'lilyrdf:'.$slug;
$resource_qs = '?v'.explode(' ', config('lemonade.version'))[0];
?>

@extends('app.layout', ['title' => 'CHARM詳細', 'titlebar' => ($charm[$cs]['schema:productID'][0] ?? '').' '.$charm[$cs]['schema:name'][0]])

@section('head')
    <link rel="stylesheet" href="{{ asset('css/charm.css').$resource_qs }}">
@endsection

@section('main')
    <main>
        <div id="charm-visual">
            Visual Data Unavailable
        </div>
        <div id="charm-data">
            <div id="summary">
                <div id="name-plate" class="title-underline">
                    @if(!empty($charm[$cs]['schema:productID'][0]))
                        <div id="product-id">{{ $charm[$cs]['schema:productID'][0] }}</div>
                    @endif
                    <div id="charm-name">
                        @if(!empty($charm[$cs]['schema:name@en'][0]))
                            <div id="name-en" class="lang-en">{{ $charm[$cs]['schema:name@en'][0] }}</div>
                        @endif
                        <div id="name-ja">
                            {{ $charm[$cs]['schema:name'][0] }}
                            @if(!empty($charm[$cs]['schema:name@zh'][0]))
                                <span id="name-zh">{{ $charm[$cs]['schema:name@zh'][0] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div id="additionalInfos">
                    @if(!empty($charm[$cs]['lily:seriesName'][0]) and (!empty($charm[$cs]['lily:hasVariant']) or !empty($charm[$cs]['lily:isVariantOf'])))
                        <details>
                            <summary>{{ $charm[$cs]['lily:seriesName'][0] }} シリーズ</summary>
                            @if(!empty($charm[$cs]['lily:hasVariant']))
                                @foreach($charm[$cs]['lily:hasVariant'] as $variant)
                                    <div>
                                        派生機体 : <a href="{{ route('charm.show', ['charm' => str_replace('lilyrdf:','',$variant)]) }}">
                                            {{ $charm[$variant]['schema:name'][0] ?? $variant }}
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                            @if(!empty($charm[$cs]['lily:isVariantOf']))
                                @foreach($charm[$cs]['lily:isVariantOf'] as $variantOf)
                                    <div>
                                        派生元機体 : <a href="{{ route('charm.show', ['charm' => str_replace('lilyrdf:','',$variantOf)]) }}">
                                            {{ $charm[$variantOf]['schema:name'][0] ?? $variantOf }}
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </details>
                    @endif
                    @foreach($charm[$cs]['lily:additionalInformation'] ?? array() as $info)
                        <div>{{ $info }}</div>
                    @endforeach
                </div>
            </div>
            <table class="table">
                <tr>
                    <th>メーカー</th>
                    <td>
                        @if(!empty($charm[$cs]['schema:manufacturer']))
                            @foreach($charm[$cs]['schema:manufacturer'] as $maker)
                                @if(!empty($charm[$maker]['schema:name'][0]))
                                    {{ $charm[$maker]['schema:name'][0] }}
                                @else
                                    {{ $maker }}
                                @endif
                            @endforeach
                        @else
                            <span style="color: gray">N/A</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>CHARM世代</th>
                    <td>{!! !empty($charm[$cs]['lily:generation'][0])
                                ? e('第'.$charm[$cs]['lily:generation'][0].'世代')
                                : "<span style=\"color:gray\">N/A</span>" !!}</td>
                </tr>
                <tr>
                    <th>下限スキラー数値</th>
                    <td>{!! !empty($charm[$cs]['lily:requiredSkillerVal'][0])
                                ? e($charm[$cs]['lily:requiredSkillerVal'][0])
                                : "<span style=\"color:gray\">N/A</span>" !!}</td>
                </tr>
            </table>
        </div>

        <h2>ユーザー</h2>
        <div id="users" class="list four">
            @forelse($charm[$cs]['lily:user'] ?? array() as $user)
                @continue(empty($charm[$user]))
                <a href="{{ route('lily.show',['lily' => removePrefix($user)]) }}" class="list-item-a">
                    <div class="list-item-data">
                        <div class="title">{{ $charm[$user]['schema:name'][0] }}</div>
                        @foreach($charm[$user]['lily:charm'] as $charmUserData)
                            @continue(empty($charm[$charmUserData]['lily:resource'][0]) or $charm[$charmUserData]['lily:resource'][0] !== $cs)
                            @if(!empty($charm[$charmUserData]['lily:usedIn']))
                                <div>登場媒体 : {{ implode(', ', $charm[$charmUserData]['lily:usedIn']) }}</div>
                            @endif
                        @endforeach
                    </div>
                </a>
            @empty
            @endforelse
        </div>


        @if(config('app.debug')) @dump($charm) @endif
    </main>
@endsection
