<?php
/**
 * @var $legion array
 * @var $legionSlug string
 */
$ls /* LegionSubject */ = 'lilyrdf:'.$legionSlug;
?>

@extends('app.layout', [
    'title' => 'レギオン詳細',
    'titlebar' => $legion[$ls]['schema:name'][0],
    'previous' => route('legion.index')])

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
                            $memberSlug = str_replace('lilyrdf:','',$member) ?>
                            <a class="list-item-a" href="{{ route('lily.show',['lily' => $memberSlug]) }}" title="{{ $lilies[$memberSlug]->name }}">
                                <div class="list-item-image">
                                    @if(!empty($legion[$member]['lily:legionJobTitle'][0]))
                                        <div class="jobTitle">{{ $legion[$member]['lily:legionJobTitle'][0] }}</div>
                                    @endif
                                    <div style="color: {{ empty($legion[$member]['lily:color'][0]) ? 'transparent' : '#'.$legion[$member]['lily:color'][0] }};
                                        font-weight: bold; text-align: right;font-size: 13px;">{{ $legion[$member]['lily:color'][0] ?? '' }}</div>
                                </div>
                                <div class="list-item-data">
                                    <div class="title-ruby">{!! e($lilies[$memberSlug]->name_y) ?: "<i style=\"color:gray\">読みデータなし</i>" !!}</div>
                                    <div class="title">{{ $lilies[$memberSlug]->name }}</div>
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

        <?php dump($legion); ?>
    </main>
@endsection


