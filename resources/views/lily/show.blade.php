@extends('app.layout',['title' => 'リリィプロフィール', 'titlebar' => $lily->name, 'pagetype' => 'back-triangle'])

<?php
    /**
     * @var $lily Lily
     */

use App\Models\Lily;

    $color_rgb = str_replace('#','',$lily->color ?? '');
    if(strlen($color_rgb) !== 6) $color_rgba = 'rgba(0,0,0,0.1)';
    else {
        $r = hexdec(substr($color_rgb,0,2));
        $g = hexdec(substr($color_rgb,2,2));
        $b = hexdec(substr($color_rgb,4,2));
        $color_rgba = 'rgba('.$r.','.$g.','.$b.',0.6)';
    }
?>

@section('head')
    <link rel="stylesheet" href="{{ asset('css/lilyprofile.css') }}">
    <style>
        #profile{
            background-image: radial-gradient(circle farthest-corner at 90% 100%, {{ $color_rgba }}, transparent 50%, transparent);
        }
        .buttons.two > .button{
            margin: 5px 10px;
            width: 45%;
        }
    </style>
@endsection

@section('main')
    <main>
        <div id="profile">
            <div class="left">
                <div class="name-plate">
                    <div class="pic"></div>
                    <div class="profile">
                        @if(mb_strlen($lily->name) < 16)
                            <div class="name-ruby">{{ $lily->name_y }} - {{ $lily->name_a }}</div>
                            <div class="name">{{ $lily->name }}</div>
                        @else
                            <div class="name-ruby flip">
                                <span class="name-y">{{ $lily->name_y }}</span>
                                <span class="name-a">{{ $lily->name_a }}</span>
                            </div>
                            <div class="name" style="font-size: 24px">{{ $lily->name }}</div>
                        @endif
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
                    @include('app.lilyprofiletable.record',['object' => $triples['garden.name'] ?? null, 'th' => '所属ガーデン'])
                    @if(!empty($triples['garden.course']))
                        @include('app.lilyprofiletable.record',['object' => $triples['garden.course'], 'th' => '学科'])
                    @endif
                    @if(!empty($triples['garden.position']))
                        @include('app.lilyprofiletable.record',['object' => $triples['garden.position'], 'th' => 'ガーデン役職'])
                    @endif
                    @include('app.lilyprofiletable.record',['object' => $triples['legion.name'] ?? null, 'th' => '所属レギオン'])
                    @if(!empty($triples['legion.position']))
                        @include('app.lilyprofiletable.record',['object' => $triples['legion.position'], 'th' => 'レギオン役職'])
                    @endif
                    @if(!empty($triples['tacticalPosition']))
                        @include('app.lilyprofiletable.record',['object' => $triples['tacticalPosition'], 'th' => 'ポジション'])
                    @endif
                    @if(!empty($triples['partner.schutzengel']))
                        @include('app.lilyprofiletable.partner',['partner' => $triples['partner.schutzengel'], 'th' => 'シュッツエンゲル'])
                    @endif
                    @if(!empty($triples['partner.schild']))
                        @include('app.lilyprofiletable.partner',['partner' => $triples['partner.schild'], 'th' => 'シルト'])
                    @endif
                    @include('app.lilyprofiletable.record',['object' => $triples['favorite'] ?? null, 'th' => '好きなもの'])
                    @include('app.lilyprofiletable.record',['object' => $triples['notGood'] ?? null, 'th' => '苦手なもの'])
                    @include('app.lilyprofiletable.record',['object' => $triples['skill_hobby'] ?? null, 'th' => '特技・趣味', 'multiline' => true])
                    @include('app.lilyprofiletable.record',['object' => $triples['rareSkill'] ?? null, 'th' => '所持レアスキル'])
                    @if(!empty($triples['subSkill']))
                        @include('app.lilyprofiletable.record',['object' => $triples['subSkill'] ?? null, 'th' => '所持サブスキル'])
                    @endif
                    @if(!empty($triples['boostedSkill']))
                        @include('app.lilyprofiletable.record',['object' => $triples['boostedSkill'] ?? null, 'th' => 'ブーステッドスキル'])
                    @endif
                    @include('app.lilyprofiletable.record',['object' => $triples['charm'] ?? null, 'th' => '主な使用CHARM'])
                    @if(!empty($triples['height']))
                        @include('app.lilyprofiletable.record',['object' => $triples['height'] ?? null, 'th' => '身長', 'suffix' => 'cm'])
                    @endif
                    @if(!empty($triples['weight']))
                        @include('app.lilyprofiletable.record',['object' => $triples['weight'] ?? null, 'th' => '身長', 'suffix' => 'kg'])
                    @endif
                    @if(!empty($lily->color))
                        <tr>
                            <th>カラーコード</th>
                            <td style="font-weight: bold; color: {{ $lily->color }}">{{ $lily->color }}</td>
                        </tr>
                    @endif
                    @if(!empty($triples['cast']))
                        @include('app.lilyprofiletable.record',['object' => $triples['cast'] ?? null, 'th' => 'キャスト'])
                    @endif
                    @if(!empty($triples['remarks']))
                        @include('app.lilyprofiletable.record',['object' => $triples['remarks'] ?? null, 'th' => '特記事項'])
                    @endif
                    </tbody>
                </table>
                <div style="font-size: smaller">
                    <a href="{{ route('admin.lily.show',['lily' => $lily->id]) }}" class="button smaller">管理</a>
                    基本データ更新 : {{ $lily->updated_at->format('Y-m/d H:i:s') }},
                    トリプル更新 : {{ $triples['_last_update']->format('Y-m/d H:i:s') }},
                    トリプル数 : {{ count($triples) }}
                </div>
            </div>
            <div class="right" style="width: 100%;position: relative">
                @if(!empty($triples['KIA']))
                    <div class="KIA">{{ $triples['KIA'] }}<br>戦死・殉職者</div>
                @endif
                <div id="pics">
                    <div style="text-align: center; padding-top: 130px; color: gray; font-size: large;">Image Unavailable</div>
                </div>
                <div id="links">
                    <h3>公式リンク</h3>
                    <?php
                    if (!empty($triples['officialUrls.acus']) && !str_starts_with($triples['officialUrls.acus'],'http')){
                        $triples['officialUrls.acus'] = str_replace('{no}', $triples['officialUrls.acus'], config('lemonade.officialUrls.acus'));
                    }
                    if (!empty($triples['officialUrls.anime']) && !str_starts_with($triples['officialUrls.anime'],'http')){
                        $triples['officialUrls.anime'] = $triples['officialUrls.anime'] === '=' ? $lily->slug.'/' : $triples['officialUrls.anime'];
                        $triples['officialUrls.anime'] = $triples['officialUrls.anime'] === '/' ? '' : $triples['officialUrls.anime'];
                        $triples['officialUrls.anime'] = str_replace('{slug}', $triples['officialUrls.anime'], config('lemonade.officialUrls.anime'));
                    }
                    if (!empty($triples['officialUrls.lb']) && !str_starts_with($triples['officialUrls.lb'],'http')){
                        $triples['officialUrls.lb'] = $triples['officialUrls.lb'] === '=' ? $lily->slug : $triples['officialUrls.anime'];
                        $triples['officialUrls.lb'] = str_replace('{slug}', $triples['officialUrls.lb'], config('lemonade.officialUrls.lb'));
                    }
                    ?>
                    <div class="buttons two">
                        @if(!empty($triples['officialUrls.acus']))
                            <a class="button" href="{{ $triples['officialUrls.acus'] }}" target="_blank"
                               title="原作公式サイトのキャラクターページを開きます">AssaultLily.com (原作公式)</a>
                        @endif
                            <?php
                            $tweet_search = 'https://twitter.com/search?q=from%3A'.config('lemonade.fumi.twitter').'%20'.$lily->getFirstName();
                            ?>
                            <a class="button" href="{{ $tweet_search }}" target="_blank"
                               title="二水ちゃんのツイートを検索します">{{ '@'.config('lemonade.fumi.twitter') }} ツイート検索</a>
                        @if(!empty($triples['officialUrls.anime']))
                            <a class="button" href="{{ $triples['officialUrls.anime'] }}" target="_blank"
                               title="アニメ「アサルトリリィ BOUQUET」のキャラクターページを開きます">BOUQUET (アニメ版)</a>
                        @endif
                        @if(!empty($triples['officialUrls.lb']))
                            <a class="button" href="{{ $triples['officialUrls.lb'] }}" target="_blank"
                               title="ゲーム「アサルトリリィ Last Bullet」のキャラクターページを開きます">Last Bullet (ラスバレ)</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
