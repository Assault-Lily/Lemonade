<?php
    /**
     * @var $slug string
     * @var $triples array
     */

$ts = 'lilyrdf:'.$slug;

$color_rgb = str_replace('#','',$triples[$ts]['lily:color'][0] ?? '');
if(strlen($color_rgb) !== 6) $color_rgba = 'rgba(0,0,0,0.1)';
else {
    $r = hexdec(substr($color_rgb,0,2));
    $g = hexdec(substr($color_rgb,2,2));
    $b = hexdec(substr($color_rgb,4,2));
    $color_rgba = 'rgba('.$r.','.$g.','.$b.',0.6)';
}

$ogp['description'] = "リリィ「{$triples[$ts]['schema:name'][0]}」のプロフィールを閲覧できます。";
if(!empty($triples[$ts]['lily:garden'][0])){
    $ogp['description'] .= $triples[$ts]['lily:garden'][0].
        ($triples[$ts]['lily:gardenDepartment'][0] ?? '');
    if(!empty($triples[$ts]['lily:grade'][0])) $ogp['description'] .= $triples[$ts]['lily:grade'][0].'年生です。';
    else $ogp['description'] .= 'のリリィです。';
}

if(!empty($triples[$ts]['lily:legion'][0]) && !empty($triples[$triples[$ts]['lily:legion'][0]]['schema:name'][0]))
    $ogp['description'] .= "レギオン「{$triples[$triples[$ts]['lily:legion'][0]]['schema:name'][0]}".
        (!empty($triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0]) ?
            '('.$triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0].')' : ''). "」に所属しています。";
?>

@extends('app.layout',[
    'title' => 'リリィプロフィール', 'titlebar' => $triples[$ts]['schema:name'][0] ,
    'pagetype' => 'back-triangle', 'previous' => route('lily.index')])

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
                        @if(empty($triples[$ts]['lily:nameKana'][0]))
                            <div class="name-ruby" style="color: gray">読みデータなし</div>
                            <div class="name">{{ $triples[$ts]['schema:name'][0] }}</div>
                        @elseif(mb_strlen($triples[$ts]['lily:nameKana'][0]) < 16)
                            <div class="name-ruby">{{ $triples[$ts]['lily:nameKana'][0] }} - {{ $triples[$ts]['schema:name@en'][0] }}</div>
                            <div class="name">{{ $triples[$ts]['schema:name'][0] }}</div>
                        @else
                            <div class="name-ruby flip">
                                <span class="name-y">{{ $triples[$ts]['lily:nameKana'][0] }}</span>
                                <span class="name-a">{{ $triples[$ts]['schema:name@en'][0] }}</span>
                            </div>
                            <div class="name" style="font-size: 24px">{{  $triples[$ts]['schema:name'][0]  }}</div>
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
                    @if(!empty($triples[$ts]['lily:anotherName']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:anotherName'], 'th' => '異名・二つ名', 'prefix' => '「', 'suffix' => '」'])
                    @endif
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:garden'] ?? null, 'th' => '所属ガーデン'])
                    @if(!empty($triples[$ts]['lily:gardenJobTitle']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:gardenJobTitle'], 'th' => 'ガーデン役職'])
                    @endif
                    @if(!empty($triples[$ts]['lily:rank']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:rank'][0], 'th' => '序列', 'suffix' => '位'])
                    @endif
                    @if(!empty($triples[$ts]['lily:gardenDepartment']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:gardenDepartment'], 'th' => '学科'])
                    @endif
                    <?php
                        $legion_name = $triples[$triples[$ts]['lily:legion'][0] ?? 0]['schema:name'][0] ?? null;
                        if(!empty($legion_name) and !empty($triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0])){
                            $legion_name .= ' ('.$triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0].')';
                        }
                    ?>
                    <tr>
                        <th>所属レギオン</th>
                        <td>
                            @if(!empty($legion_name))
                                <a href="{{ route('legion.show',['legion' => str_replace('lilyrdf:','',$triples[$ts]['lily:legion'][0])]) }}">
                                    {{ $legion_name }}
                                </a>
                            @else
                                <span style="color:gray;">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @if(!empty($triples[$ts]['lily:legionJobTitle'][0]))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:legionJobTitle'][0], 'th' => 'レギオン役職'])
                    @endif
                    @if(!empty($triples[$ts]['lily:pastLegion']))
                        <?php
                        $past_legion_name = $triples[$triples[$ts]['lily:pastLegion'][0] ?? 0]['schema:name'][0] ?? null;
                        if(!empty($past_legion_name) and !empty($triples[$triples[$ts]['lily:pastLegion'][0]]['schema:alternateName'][0])){
                            $past_legion_name .= ' ('.$triples[$triples[$ts]['lily:pastLegion'][0]]['schema:alternateName'][0].')';
                        }
                        ?>
                        <tr>
                            <th>過去の所属レギオン</th>
                            <td>
                                <a href="{{ route('legion.show',['legion' => str_replace('lilyrdf:','',$triples[$ts]['lily:pastLegion'][0])]) }}">
                                    {{ $past_legion_name }}
                                </a>
                            </td>
                        </tr>
                    @endif
                    @if(!empty($triples[$ts]['lily:position']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:position'], 'th' => 'ポジション'])
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
                        <td {!! count($triples[$ts]['lily:charm'] ?? array()) >= 2 ? 'rowspan="2" style="height: 4em;"' : '' !!}>
                            <?php
                            foreach ($triples[$ts]['lily:charm'] ?? array() as $charm){
                                // CHARM情報が取得できない場合のスキップ
                                if(empty($triples[$charm]['lily:resource'][0]) or empty($triples[$triples[$charm]['lily:resource'][0]]))continue;
                                $charm_resource = $triples[$triples[$charm]['lily:resource'][0]];

                                // CHARM名の取得と追加情報の付加
                                $charm_name = ($charm_resource['schema:productID'][0] ?? '').' '.$charm_resource['schema:name'][0];
                                if(!empty($triples[$charm]['lily:additionalInformation'])){
                                    $charm_name .= ' (';
                                    $additional_info = '';
                                    foreach ($triples[$charm]['lily:additionalInformation'] as $info){
                                        $additional_info .= $info.' ';
                                    }
                                    $charm_name .= trim($additional_info).')';
                                }

                                // 使用場面情報の付加
                                if(!empty($triples[$charm]['lily:usedIn'])){
                                    $charm_used_in = '登場媒体：';
                                    foreach ($triples[$charm]['lily:usedIn'] as $used_in){
                                        $charm_used_in .= $used_in.', ';
                                    }
                                    $charm_used_in = mb_substr($charm_used_in, 0, mb_strlen($charm_used_in) - 2);
                                }else{
                                    $charm_used_in = '';
                                }
                                ?><div {!! !empty($charm_used_in) ? 'title="'.$charm_used_in.'"' : '' !!}>{{ $charm_name }}</div><?php
                            }
                            ?>
                            @if(count($triples[$ts]['lily:charm'] ?? array()) < 1)
                                    <span style="color:gray;">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @if(count($triples[$ts]['lily:charm'] ?? array()) >= 2)
                        <tr>
                            <td class="spacer"></td>
                        </tr>
                    @endif
                    @if(!empty($triples[$ts]['schema:height'][0]))
                        <?php
                        $height = $triples[$ts]['schema:height'][0];
                        if(is_numeric($height) === (float)0){
                            $height_suffix = '';
                        }else{
                            $height = floatval($height);
                            $height_suffix = 'cm';
                        }
                        ?>
                        @include('app.lilyprofiletable.record',['object' => $height, 'th' => '身長', 'suffix' => $height_suffix])
                    @endif
                    @if(!empty($triples[$ts]['schema:weight'][0]))
                        <?php
                        $weight = $triples[$ts]['schema:weight'][0];
                        if(is_numeric($weight) === (float)0){
                            $weight_suffix = '';
                        }else{
                            $weight = floatval($weight);
                            $weight_suffix = 'kg';
                        }
                        ?>
                        @include('app.lilyprofiletable.record',['object' => $weight, 'th' => '体重', 'suffix' => $weight_suffix])
                    @endif
                    @if(!empty($triples[$ts]['schema:birthPlace'][0]))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['schema:birthPlace'][0] ?? null, 'th' => '出身地'])
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
                    トリプル数 : {{ count($triples[$ts] ?? array(), 1) - count($triples[$ts] ?? array()) }} ,
                    <?php $triple_count = 0; foreach ($triples as $sub_triples)
                        $triple_count += (count($sub_triples, 1) - count($sub_triples)); ?>
                    総参照トリプル数 : {{ $triple_count }}
                </div>
            </div>
            <div class="right" style="width: 100%;position: relative">
                @if(!empty($triples[$ts]['lily:killedIn'][0]))
                    <div class="KIA">{{ $triples[$ts]['lily:killedIn'][0] }}<br>戦死・殉職者</div>
                @endif
                <div id="pics">
                    <div style="text-align: center; padding-top: 130px; color: gray;">
                        <div style="margin-bottom: .4em">Image Unavailable</div>
                        <div class="buttons" style="font-size: smaller">
                            <a href="https://www.google.com/search?q={{ urlencode($triples[$ts]['schema:name'][0]) }}&tbm=isch"
                               target="_blank" class="button smaller">
                                Google 画像検索
                            </a>
                            <a href="https://www.pixiv.net/tags/{{ urlencode($triples[$ts]['schema:name'][0]) }}/illustrations"
                               target="_blank" class="button smaller">
                                pixiv イラストタグ検索
                            </a>
                        </div>
                    </div>
                </div>
                <div id="links">
                    <h3>公式リンク</h3>
                    <?php
                    $slug = strtolower($triples[$ts]['schema:givenName@en'][0] ?? '');

                    $tweet_search = 'https://twitter.com/search?q=from%3A'.config('lemonade.fumi.twitter').'%20';
                    $tweet_search .= urlencode($triples[$ts]['schema:givenName'][0] ?? '');

                    if (!empty($triples[$ts]['officialUrls.acus'][0]) && !str_starts_with($triples[$ts]['officialUrls.acus'][0],'http')){
                        $official_urls['acus'] = str_replace('{no}', $triples[$ts]['officialUrls.acus'][0], config('lemonade.officialUrls.acus'));
                    }
                    // リリィが特定のレギオンに所属する場合、公式サイトへのリンクを生成
                    if (!empty($triples[$ts]['lily:legion'][0]) && in_array($triples[$ts]['lily:legion'][0], config('lemonade.specialLegion.anime')) ||
                        !empty($triples[$ts]['lily:pastLegion'][0]) && in_array($triples[$ts]['lily:pastLegion'][0], config('lemonade.specialLegion.anime'))){
                        $official_urls['anime'] = str_replace('{slug}', $slug, config('lemonade.officialUrls.anime'));
                    }
                    if (!empty($triples[$ts]['lily:legion'][0]) && in_array($triples[$ts]['lily:legion'][0], config('lemonade.specialLegion.lb'))){
                        $official_urls['lb'] = str_replace('{slug}', $slug, config('lemonade.officialUrls.lb'));
                    }
                    ?>
                    <div class="buttons two">
                        @if(!empty($official_urls['acus']))
                            <a class="button" href="{{ $official_urls['acus'] }}" target="_blank"
                               title="原作公式サイトのキャラクターページを開きます">AssaultLily.com (原作公式)</a>
                        @endif
                            <a class="button" href="{{ $tweet_search }}" target="_blank"
                               title="二水ちゃんのツイートを検索します">{{ '@'.config('lemonade.fumi.twitter') }} ツイート検索</a>
                        @if(!empty($official_urls['anime']))
                            <a class="button" href="{{ $official_urls['anime'] }}" target="_blank"
                               title="アニメ「アサルトリリィ BOUQUET」のキャラクターページを開きます">BOUQUET (アニメ版)</a>
                        @endif
                        @if(!empty($official_urls['lb']))
                            <a class="button" href="{{ $official_urls['lb'] }}" target="_blank"
                               title="ゲーム「アサルトリリィ Last Bullet」のキャラクターページを開きます">Last Bullet (ラスバレ)</a>
                        @endif
                    </div>
                </div>
                    <?php
                    $relationship = array();
                    $directRel = [ // 中間ノードのない関係
                        'lily:schutzengel' => 'シュッツエンゲル',
                        'lily:pastSchutzengel' => '過去のシュッツエンゲル',
                        'lily:olderSchwester' => 'シュベスター(姉)',
                        'lily:youngerSchwester' => 'シュベスター(妹)',
                        'lily:schild' => 'シルト',
                        'lily:pastSchild' => '過去のシルト',
                        'lily:roomMate' => 'ルームメイト',
                    ];
                    $IntermediateRel = [ // 中間ノードのある関係
                        'schema:sibling',
                        'lily:relationship',
                    ];
                    // 疑似中間ノードの生成とキー配列への追加
                    foreach ($directRel as $key => $psr){
                        $count = 0;
                        foreach ($triples[$ts][$key] ?? array() as $psrLily){
                            $triples[$key.'-'.$count] = [
                                'lily:resource' => [ $psrLily ],
                                'lily:additionalInformation' => [ $psr ],
                            ];
                            $relationship[] = $key.'-'.$count;
                            $count++;
                        }
                    }
                    // キー配列のマージ
                    foreach ($IntermediateRel as $predicate){
                        if(!empty($triples[$ts][$predicate]))
                            $relationship = array_merge_recursive($relationship, $triples[$ts][$predicate]);
                    }
                    ?>
                @if(!empty($relationship))
                    <div>
                        <h3>関連する人物</h3>
                        <div class="list" >
                            @foreach($relationship as $rel)
                                <?php /** @var $rel string */ $key = $triples[$rel]['lily:resource'][0]; ?>
                                <a href="{{ route('lily.show', ['lily' => str_replace('lilyrdf:','',$triples[$rel]['lily:resource'][0])]) }}"
                                   class="list-item-a" style="width: 48%">
                                    <div class="list-item-data">
                                        <div class="title" style="font-size: 17px">{{ $triples[$key]['schema:name'][0] ?? '' }}</div>
                                        @foreach($triples[$rel]['lily:additionalInformation'] ?? array() as $info)
                                        <div>{{ $info ?? '' }}</div>
                                        @endforeach
                                    </div>
                                </a>
                            @endforeach
                        </div>

                    </div>
                @endif

            </div>
        </div>
        <?php if (config('app.debug')) dump($triples) ?>
    </main>
@endsection
