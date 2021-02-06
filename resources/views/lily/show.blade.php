@extends('app.layout',['title' => 'リリィプロフィール', 'pagetype' => 'back-triangle'])

<?php
    /**
     * @var $lily \App\Models\Lily
     */
?>

@section('head')
    <link rel="stylesheet" href="{{ asset('css/lilyprofile.css') }}">
@endsection

@section('main')
    <main>
        <div id="profile">
            <div class="left">
                <div class="name-plate">
                    <div class="pic"></div>
                    <div class="profile">
                        <div class="name-ruby">{{ $lily->name_y }}</div>
                        <div class="name" style="
                        @if(mb_strlen($lily->name) >= 16) font-size: 24px; @endif">{{ $lily->name }}</div>
                        <div class="summary">
                            <div>誕生日 : {{ $triples['birthday'] ?? 'N/A' }}</div><hr>
                            <div>年齢 : {{ $triples['age'] ?? 'N/A' }}歳</div><hr>
                            <div>血液型 : {{ $triples['bloodType'] ?? 'N/A' }}</div><hr>
                            <div>学年 : {{ !empty($triples['garden.grade']) ? $triples['garden.grade'].'年' : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <table id="profile-table">
                    <tbody>
                    <tr>
                        <th>所属レギオン</th>
                        <td>{{ $triples['legion.name'] ?? 'N/A' }}</td>
                    </tr>
                    @if(!empty($triples['legion.position']))
                        <tr>
                            <th>レギオン役職</th>
                            <td>{{ $triples['legion.position'] }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>好きなもの</th>
                        <td>{{ $triples['favorite'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>苦手なもの</th>
                        <td>{{ $triples['notGood'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>特技・趣味</th>
                        <td rowspan="2" style="height: 5em">{{ $triples['skill_hobby'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="spacer"></td>
                    </tr>
                    <tr>
                        <th>所持レアスキル</th>
                        <td>{{ $triples['rareSkill'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>主な使用CHARM</th>
                        <td>{{ $triples['charm'] ?? 'N/A' }}</td>
                    </tr>
                    @if(!empty($lily->color))
                        <tr>
                            <th>カラーコード</th>
                            <td style="font-weight: bold; color: {{ $lily->color }}">{{ $lily->color }}</td>
                        </tr>
                    @endif
                    @if(!empty($triples['cast']))
                        <tr>
                            <th>キャスト</th>
                            <td>{{ $triples['cast'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($triples['remarks']))
                        <tr>
                            <th>特記事項</th>
                            <td rowspan="2" style="height: 5em">{!! nl2br($triples['remarks']) !!}</td>
                        </tr>
                        <tr>
                            <td class="spacer"></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="right" style="width: 100%;position: relative">
                @if(!empty($triples['KIA']))
                    <div class="KIA">{{ $triples['KIA'] }}<br>戦死・殉職者</div>
                @endif
                <p style="text-align: center; padding-top: 100px; color: gray; font-size: large;">Image Unavailable</p>
            </div>
        </div>
    </main>
@endsection
