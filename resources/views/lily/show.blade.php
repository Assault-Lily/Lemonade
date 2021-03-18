@extends('app.layout',[
    'title' => 'リリィプロフィール', 'titlebar' => $lily->name,
    'pagetype' => 'back-triangle', 'previous' => route('lily.index')])

<?php
    /**
     * @var $lily Lily
     */

use App\Models\Lily;

$ts = 'lilyrdf:'.$lily->slug;

$color_rgb = str_replace('#','',$triples[$ts]['lily:color'][0] ?? '');
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
        @if(!empty($rdf_error))
            <div class="window-a" style="margin-top: 15px">
                <div class="header">RDF連携エラー</div>
                <div class="body">
                    <p>
                        SPARQL問い合わせが正常に完了しませんでした。管理者までご連絡ください。
                        現在表示されている情報は大部分が欠落しています。
                    </p>
                    <p style="color: darkred">
                        @if($rdf_error instanceof \Illuminate\Http\Client\RequestException)
                            {!! nl2br(strip_tags($rdf_error->getMessage())) !!}
                        @endif
                        @if($rdf_error instanceof \Illuminate\Http\Client\ConnectionException)
                            {!! nl2br(strip_tags($rdf_error->getPrevious()->getHandlerContext()['error'] ?? '')) !!}
                        @endif
                    </p>
                </div>
            </div>
        @endif
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
                            <div>誕生日 : {{ !empty($triples[$ts]['schema:birthDate'][0]) ?
                                                convertDateString($triples[$ts]['schema:birthDate'][0])->format('n月j日') : 'N/A' }}</div><hr>
                            <div>年齢 : {{ $triples[$ts]['foaf:age'][0] ?? 'N/A' }}歳</div><hr>
                            <div>血液型 : {{ $triples[$ts]['lily:bloodType'][0] ?? 'N/A' }}</div><hr>
                            <div>学年 : {{ !empty($triples[$ts]['lily:grade'][0]) ? $triples[$ts]['lily:grade'][0].'年' : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <table id="profile-table">
                    <tbody>
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:garden'] ?? null, 'th' => '所属ガーデン'])
                    @if(!empty($triples[$ts]['lily:gardenDepartment']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:gardenDepartment'], 'th' => '学科'])
                    @endif
                    @if(!empty($triples[$ts]['lily:gardenPosition']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:gardenPosition'], 'th' => 'ガーデン役職'])
                    @endif
                    <?php
                        $legion_name = $triples[$triples[$ts]['lily:legion'][0] ?? 0]['schema:name'][0] ?? null;
                        if(!empty($legion_name) and !empty($triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0])){
                            $legion_name .= ' ('.$triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0].')';
                        }
                    ?>
                    @include('app.lilyprofiletable.record',['object' => $legion_name ?? null, 'th' => '所属レギオン'])
                    @if(!empty($triples[$ts]['lily:pastLegion']))
                        <?php
                        $past_legion_name = $triples[$triples[$ts]['lily:pastLegion'][0] ?? 0]['schema:name'][0] ?? null;
                        if(!empty($past_legion_name) and !empty($triples[$triples[$ts]['lily:pastLegion'][0]]['schema:alternateName'][0])){
                            $past_legion_name .= ' ('.$triples[$triples[$ts]['lily:pastLegion'][0]]['schema:alternateName'][0].')';
                        }
                        ?>
                        @include('app.lilyprofiletable.record',['object' => $past_legion_name ?? null, 'th' => '過去の所属レギオン'])
                    @endif
                    @if(!empty($triples[$ts]['lily:legionJobTitle'][0]))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:legionJobTitle'][0], 'th' => 'レギオン役職'])
                    @endif
                    @if(!empty($triples[$ts]['lily:position']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:position'], 'th' => 'ポジション'])
                    @endif
                    @if(!empty($triples[$ts]['lily:schutzengel'][0]))
                        @include('app.lilyprofiletable.partner', ['partner' => $triples[$ts]['lily:schutzengel'][0], 'partner_data' => $triples[$triples[$ts]['lily:schutzengel'][0]], 'th' => 'シュッツエンゲル'])
                    @endif
                    @if(!empty($triples[$ts]['lily:pastSchutzengel'][0]))
                        @include('app.lilyprofiletable.partner', ['partner' => $triples[$ts]['lily:pastSchutzengel'][0], 'partner_data' => $triples[$triples[$ts]['lily:pastSchutzengel'][0]], 'th' => '過去のシュッツエンゲル'])
                    @endif
                    @if(!empty($triples[$ts]['lily:schild'][0]))
                        @include('app.lilyprofiletable.partner', ['partner' => $triples[$ts]['lily:schild'][0], 'partner_data' => $triples[$triples[$ts]['lily:schild'][0]], 'th' => 'シルト'])
                    @endif
                    @if(!empty($triples[$ts]['lily:pastSchild'][0]))
                        @include('app.lilyprofiletable.partner', ['partner' => $triples[$ts]['lily:pastSchild'][0], 'partner_data' => $triples[$triples[$ts]['lily:pastSchild'][0]], 'th' => '過去のシルト'])
                    @endif
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:favorite'] ?? null, 'th' => '好きなもの'])
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:notGood'] ?? null, 'th' => '苦手なもの'])
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:hobby_talent'] ?? null, 'th' => '特技・趣味', 'multiline' => true])
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:rareSkill'] ?? null, 'th' => '所持レアスキル'])
                    @if(!empty($triples[$ts]['lily:subSkill']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:subSkill'] ?? null, 'th' => '所持サブスキル'])
                    @endif
                    @if(!empty($triples[$ts]['lily:boostedSkill']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:boostedSkill'] ?? null, 'th' => 'ブーステッドスキル'])
                    @endif
                    <tr>
                        <th>主な使用CHARM</th>
                        <td {{ count($triples[$ts]['lily:charm'] ?? array()) >= 2 ? 'rowspan="2" style="height: 4em;"' : ''}}>
                            <?php
                            foreach ($triples[$ts]['lily:charm'] ?? array() as $charm){
                                $charm_name = ($triples[$charm]['schema:productID'][0] ?? '').' '.$triples[$charm]['schema:name'][0];
                                if(!empty($triples[$ts]['lily:charmAdditionalInformation'])){
                                    $charm_name .= ' ( ';
                                    foreach ($triples[$ts]['lily:charmAdditionalInformation'] as $info){
                                        $charm_name .= $info.' ';
                                    }
                                    $charm_name .= ')';
                                }
                                ?><span>{{ $charm_name }}</span><?php
                            }
                            ?>
                        </td>
                    </tr>
                    @if(count($triples[$ts]['lily:charm'] ?? array()) >= 2)
                        <tr>
                            <td class="spacer"></td>
                        </tr>
                    @endif
                    @if(!empty($triples[$ts]['schema:height'][0]))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['schema:height'][0] ?? null, 'th' => '身長', 'suffix' => 'cm'])
                    @endif
                    @if(!empty($triples[$ts]['schema:weight'][0]))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['schema:weight'][0] ?? null, 'th' => '体重', 'suffix' => 'kg'])
                    @endif
                    @if(!empty($triples[$ts]['lily:color'][0]))
                        <tr>
                            <th>カラーコード</th>
                            <td style="font-weight: bold; color: {{ '#'.$triples[$ts]['lily:color'][0] }}">{{ '#'.$triples[$ts]['lily:color'][0] }}</td>
                        </tr>
                    @endif
                    @if(!empty($triples[$ts]['lily:castName']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:castName'] ?? null, 'th' => 'キャスト'])
                    @endif
                    @if(!empty($triples['remarks']))
                        @include('app.lilyprofiletable.record',['object' => $triples['remarks'] ?? null, 'th' => '特記事項'])
                    @endif
                    </tbody>
                </table>
                <div style="font-size: smaller">
                    <a href="{{ route('admin.lily.show',['lily' => $lily->id]) }}" class="button smaller">管理</a>
                    基本データ更新 : {{ $lily->updated_at->format('Y-m/d H:i:s') }},
                    トリプル数 : {{ count($triples[$ts] ?? array(), 1) - count($triples[$ts] ?? array()) }}
                </div>
            </div>
            <div class="right" style="width: 100%;position: relative">
                @if(!empty($triples[$ts]['lily:killedIn'][0]))
                    <div class="KIA">{{ $triples[$ts]['lily:killedIn'][0] }}<br>戦死・殉職者</div>
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
                            $tweet_search = 'https://twitter.com/search?q=from%3A'.config('lemonade.fumi.twitter').'%20';
                            $tweet_search .= $triples[$ts]['schema:givenName'][0] ?? $lily->name;
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
        <?php if (config('app.debug')) dump($triples) ?>
    </main>
@endsection
