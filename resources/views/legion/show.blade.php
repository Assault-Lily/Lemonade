<?php
/**
 * @var $legion array
 * @var $legionSlug string
 */
$ls /* LegionSubject */ = 'lilyrdf:'.$legionSlug;

$ogp['title'] = $legion[$ls]['schema:name'][0];
$ogp['description'] = "レギオン「{$legion[$ls]['schema:name'][0]}".
    (!empty($legion[$ls]['schema:alternateName'][0]) ?
        '('.$legion[$ls]['schema:alternateName'][0].')' : '')
    ."」の情報を閲覧できます。";
if(!empty($legion[$ls]['lily:numberOfMembers'][0])) $ogp['description'] .= " {$legion[$ls]['lily:numberOfMembers'][0]}人のリリィが所属しています。"
?>

@extends('app.layout', [
    'title' => 'レギオン詳細',
    'titlebar' => $legion[$ls]['schema:name'][0]])

@section('head')
    <link rel="stylesheet" href="{{ asset('css/legion.css') }}">
@endsection

@section('main')
    <div id="legion-summary">
        <div class="legion-grade">{{ $legion[$ls]['lily:legionGrade'][0] ?? '-' }}</div>
        <div id="legion-name">
            <div id="legion-name-en">{{ $legion[$ls]['schema:name@en'][0] ?? 'LEGION NAME' }}</div>
            <div id="legion-name-ja">
                {{ $legion[$ls]['schema:name'][0] }}
                @if(!empty($legion[$ls]['schema:alternateName']))
                    <span>({{ $legion[$ls]['schema:alternateName'][0] }})</span>
                @endif
            </div>
        </div>
        @if(!empty($legion[$ls]['lily:disbanded']) and $legion[$ls]['lily:disbanded'][0] == 'true')
            <div class="more-info">解散済み</div>
        @endif
        @if(!empty($legion[$ls]['lily:numberOfMembers'][0]))
            <div class="more-info">所属数 : {{ $legion[$ls]['lily:numberOfMembers'][0] }}</div>
        @else
            <div class="more-info">所属数 : 不明</div>
        @endif
    </div>
    <main>
        <?php
        $membersArray = [
            'schema:member' => '所属リリィ',
            'lily:submember' => 'サブメンバー',
            'schema:alumni' => '過去所属していたリリィ'
        ]
        ?>
            @foreach($membersArray as $key => $members)
                @if(!empty($legion[$ls][$key]))
                    <h2>{{ $members }}</h2>
                    <div class="list three">
                        @foreach($legion[$ls][$key] as $member)
                            <?php
                            /** @var string $member */
                            $memberSlug = str_replace('lilyrdf:','',$member);
                            ?>
                            <a class="list-item-a" href="{{ route('lily.show',['lily' => $memberSlug]) }}" title="{{ $legion[$member]['schema:name'][0] }}">
                                <div class="list-item-image">
                                    @if(!empty($legion[$member]['lily:legionJobTitle'][0]) and ($legion[$ls]['lily:disbanded'][0] ?? 'false') !== 'true')
                                        <div class="jobTitle">{{ $legion[$member]['lily:legionJobTitle'][0] }}</div>
                                    @endif
                                    <div style="color: {{ empty($legion[$member]['lily:color'][0]) ? 'transparent' : '#'.$legion[$member]['lily:color'][0] }};
                                        font-weight: bold; text-align: right;font-size: 13px;">{{ $legion[$member]['lily:color'][0] ?? '' }}</div>
                                </div>
                                <div class="list-item-data">
                                    <div class="title-ruby">{!! e($legion[$member]['lily:nameKana'][0] ?? '') ?: "<i style=\"color:gray\">読みデータなし</i>" !!}</div>
                                    <div class="title">{{ $legion[$member]['schema:name'][0] }}</div>
                                    <div>
                                        {{ $legion[$member]['lily:garden'][0] ?? 'ガーデン情報なし' }}
                                        {{ !empty($legion[$member]['lily:grade']) ? $legion[$member]['lily:grade'][0].'年' : '' }}
                                    </div>
                                    <div>レアスキル : {!! e($legion[$member]['lily:rareSkill'][0] ?? '') ?: "<span style=\"color:gray\">N/A</span>" !!}</div>
                                </div>
                            </a>
                        @endforeach
                        @if((count($legion[$ls][$key]) % 3) != 0)
                            <div style="width: 32%; margin-left: 6px"></div>
                        @endif
                    </div>
                @endif
            @endforeach

        <?php if (config('app.debug')) dump($legion); ?>

        <div class="window-a" id="notice">
            <div class="header">ご注意</div>
            <div class="body">
                <h3>所属数について</h3>
                <p style="font-weight: bolder">
                    レギオンの所属数はここで表示されているメンバーの総数と必ずしも等しいわけではありません。
                </p>
                <p>
                    メンバー数がわかっているが全員の情報が出ていない(あるいは登録されていない)レギオン、
                    所属しているリリィの一部がわかっているがメンバー数がわかっていないレギオン、
                    またはその両方があるためです。
                </p>
                <p>
                    レギオンの所属人数はデータソースからの<span title="RDFの lily:numberOfMembers 要素">所属数情報</span>に基づいて表示しており、
                    メンバーとサブメンバーの総数をシステム側で計数しているものではありません。
                </p>
            </div>
        </div>
    </main>
@endsection


