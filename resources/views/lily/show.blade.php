<?php
    /**
     * @var $slug string
     * @var $triples array
     * @var $icons \Illuminate\Database\Eloquent\Collection
     * @var $icon \App\Models\Image | null
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

$ogp['title'] = $triples[$ts]['schema:name'][0];
$ogp['type'] = 'lily';
$ogp['description'] = "リリィ「{$triples[$ts]['schema:name'][0]}」のプロフィールを閲覧できます。";
if(!empty($triples[$ts]['lily:garden'][0])){
    $ogp['description'] .= $triples[$ts]['lily:garden'][0].
        ($triples[$ts]['lily:gardenDepartment'][0] ?? '');
    if(!empty($triples[$ts]['lily:grade'][0])) $ogp['description'] .= convertGradeString($triples[$ts]['lily:grade'][0]).'です。';
    else $ogp['description'] .= 'のリリィです。';
}

if(!empty($triples[$ts]['lily:legion'][0]) && !empty($triples[$triples[$ts]['lily:legion'][0]]['schema:name'][0]))
    $ogp['description'] .= "レギオン「{$triples[$triples[$ts]['lily:legion'][0]]['schema:name'][0]}".
        (!empty($triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0]) ?
            '('.$triples[$triples[$ts]['lily:legion'][0]]['schema:alternateName'][0].')' : ''). "」に所属しています。";

$icon = !$icons->isEmpty() ? $icons->random() : null;

$resource_qs = '?v'.explode(' ', config('lemonade.version'))[0];
?>

@extends('app.layout',[
    'title' => 'リリィプロフィール', 'titlebar' => $triples[$ts]['schema:name'][0] ,
    'pagetype' => 'back-triangle'])

@section('head')
    <link rel="stylesheet" href="{{ asset('css/lilyprofile.css').$resource_qs }}">
    <link rel="stylesheet" href="{{ asset('css/lilyprofile-mobile.css').$resource_qs }}" media="screen and (max-width:500px) and (orientation:portrait)">
    <style>
        #profile{
            background-image: radial-gradient(circle farthest-corner at 90% 100%, {{ $color_rgba }}, transparent 50%, transparent);
        }
    </style>
@endsection

@section('main')
    <main>
        <div id="profile">
            <div class="left">
                <div class="name-plate">
                    @if(!empty($icon))
                        <img src="{{ $icon->image_url }}" class="pic" alt="icon"
                             title="アイコン作者 : {{ $icon->author }} さん">
                    @else
                        <div class="pic">NoImage</div>
                    @endif
                    <div class="profile">
                        @if(empty($triples[$ts]['lily:nameKana'][0]))
                            <div class="name-ruby" style="color: gray">読みデータなし</div>
                            <div class="name">{{ $triples[$ts]['schema:name'][0] }}</div>
                        @elseif(mb_strlen($triples[$ts]['lily:nameKana'][0]) < 16 || empty($triples[$ts]['schema:name@en'][0]))
                            <div class="name-ruby">{{ $triples[$ts]['lily:nameKana'][0] }}
                                {{(!empty($triples[$ts]['schema:name@en'][0]) ? ' - '.$triples[$ts]['schema:name@en'][0] : '') }}</div>
                            <div class="name">{{ $triples[$ts]['schema:name'][0] }}</div>
                        @else
                            <div class="name-ruby flip">
                                <span class="name-y">{{ $triples[$ts]['lily:nameKana'][0] }}</span>
                                <span class="name-a">{{ $triples[$ts]['schema:name@en'][0] }}</span>
                            </div>
                            <div class="name name-long">{{  $triples[$ts]['schema:name'][0]  }}</div>
                        @endif
                        <div class="summary">
                            <div>誕生日 : {{ !empty($triples[$ts]['schema:birthDate'][0]) ?
                                                convertDateString($triples[$ts]['schema:birthDate'][0])->format('n月j日') : 'N/A' }}</div><hr>
                            <div>年齢 : {{ $triples[$ts]['foaf:age'][0] ?? 'N/A' }}歳</div><hr>
                            <div>血液型 : {{ $triples[$ts]['lily:bloodType'][0] ?? 'N/A' }}</div><hr>
                            <div>学年 : {{ !empty($triples[$ts]['lily:grade'][0]) ? convertGradeString($triples[$ts]['lily:grade'][0]) : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <table id="profile-table" class="table">
                    <tbody>
                    @if(!empty($triples[$ts]['lily:dharmaName']))
                        <?php
                        $dharma_name = $triples[$ts]['lily:dharmaName'][0];
                        if(!empty($triples[$ts]['lily:dharmaNameKana'][0])){
                            $dharma_name .= ' ('.$triples[$ts]['lily:dharmaNameKana'][0].')';
                        }
                        ?>
                        @include('app.lilyprofiletable.record',['object' => $dharma_name, 'th' => '僧名'])
                    @endif
                    @if(!empty($triples[$ts]['lily:anotherName']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:anotherName'], 'th' => '異名・二つ名', 'prefix' => '「', 'suffix' => '」'])
                    @endif
                    @include('app.lilyprofiletable.filterRecord',['object' => $triples[$ts]['lily:garden'] ?? null, 'th' => '所属ガーデン', 'key' => 'garden'])
                    @if(!empty($triples[$ts]['lily:gardenJobTitle']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:gardenJobTitle'], 'th' => 'ガーデン役職'])
                    @endif
                    @if(!empty($triples[$ts]['lily:rank']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:rank'][0], 'th' => '序列', 'suffix' => '位'])
                    @endif
                    @if(!empty($triples[$ts]['lily:gardenDepartment']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:gardenDepartment'], 'th' => '学科'])
                    @endif
                    @if(!empty($triples[$ts]['lily:class'][0]))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:class'][0], 'th' => 'クラス'])
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
                    @if(!empty($triples[$ts]['lily:legionJobTitle']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:legionJobTitle'], 'th' => 'レギオン役職'])
                    @endif
                    @if(!empty($triples[$ts]['lily:pastLegion']))
                        <tr>
                            <th>過去の所属レギオン</th>
                            <td {!! (count($triples[$ts]['lily:pastLegion']) >= 2) ? 'rowspan="2" style="height: 4em;"' : '' !!}>
                                @foreach($triples[$ts]['lily:pastLegion'] as $past_legion)
                                    <?php
                                    $past_legion_name = $triples[$past_legion ?? 0]['schema:name'][0] ?? null;
                                    if(!empty($past_legion_name) and !empty($triples[$past_legion]['schema:alternateName'][0])){
                                        $past_legion_name .= ' ('.$triples[$past_legion]['schema:alternateName'][0].')';
                                    }
                                    ?>
                                    <a href="{{ route('legion.show',['legion' => str_replace('lilyrdf:','',$past_legion)]) }}"
                                       style="display: block">
                                        {{ $past_legion_name }}
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        {!! (count($triples[$ts]['lily:pastLegion']) >= 2) ? '<tr><td class="spacer"></td></tr>' : '' !!}
                    @endif
                    @if(!empty($triples[$ts]['lily:taskforce']))
                        <tr>
                            <th>所属<!--している-->部隊</th><!-- TODO: 対応完了次第コメント解除 -->
                            <td {!! (count($triples[$ts]['lily:taskforce']) >= 2) ? 'rowspan="2" style="height: 4em;"' : '' !!}>
                                @foreach($triples[$ts]['lily:taskforce'] as $taskforce)
                                    <?php
                                    $taskforce_name = $triples[$taskforce ?? 0]['schema:name'][0] ?? null;
                                    if(!empty($taskforce_name) and !empty($triples[$taskforce]['schema:alternateName'][0])){
                                        $taskforce_name .= ' ('.$triples[$taskforce]['schema:alternateName'][0].')';
                                    }
                                    ?>
                                    <a href="{{ route('legion.show',['legion' => str_replace('lilyrdf:','',$taskforce)]) }}"
                                       style="display: block">
                                        {{ $taskforce_name }}
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        {!! (count($triples[$ts]['lily:taskforce']) >= 2) ? '<tr><td class="spacer"></td></tr>' : '' !!}
                    @endif
                    @if(!empty($triples[$ts]['lily:pastTaskforce']))
                        <tr>
                            <th>過去所属した部隊</th>
                            <td {!! (count($triples[$ts]['lily:pastTaskforce']) >= 2) ? 'rowspan="2" style="height: 4em;"' : '' !!}>
                                @foreach($triples[$ts]['lily:pastTaskforce'] as $past_taskforce)
                                    <?php
                                    $past_taskforce_name = $triples[$past_taskforce ?? 0]['schema:name'][0] ?? null;
                                    if(!empty($past_taskforce_name) and !empty($triples[$past_taskforce]['schema:alternateName'][0])){
                                        $past_taskforce_name .= ' ('.$triples[$past_taskforce]['schema:alternateName'][0].')';
                                    }
                                    ?>
                                    <a href="{{ route('legion.show',['legion' => str_replace('lilyrdf:','',$past_taskforce)]) }}"
                                       style="display: block">
                                        {{ $past_taskforce_name }}
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        {!! (count($triples[$ts]['lily:pastTaskforce']) >= 2) ? '<tr><td class="spacer"></td></tr>' : '' !!}
                    @endif
                    @if(!empty($triples[$ts]['lily:position']))
                        @include('app.lilyprofiletable.filterRecord',['object' => $triples[$ts]['lily:position'], 'th' => 'ポジション', 'key' => 'position'])
                    @endif
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:favorite'] ?? null, 'th' => '好きなもの'])
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:notGood'] ?? null, 'th' => '苦手なもの'])
                    @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:hobby_talent'] ?? null, 'th' => '特技・趣味', 'multiline' => true])
                    @include('app.lilyprofiletable.filterRecord',['object' => $triples[$ts]['lily:rareSkill'] ?? null, 'th' => '所持レアスキル', 'key' => 'rareSkill'])
                    @if(!empty($triples[$ts]['lily:subSkill']))
                        @include('app.lilyprofiletable.filterRecord',['object' => $triples[$ts]['lily:subSkill'] ?? null, 'th' => '所持サブスキル', 'key' => 'subSkill'])
                    @endif
                    @if(!empty($triples[$ts]['lily:boostedSkill']))
                        @include('app.lilyprofiletable.filterRecord',['object' => $triples[$ts]['lily:boostedSkill'] ?? null, 'th' => 'ブーステッドスキル', 'key' => 'boostedSkill'])
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
                                ?><a href="{{ route('charm.show', ['charm' => removePrefix($triples[$charm]['lily:resource'][0])]) }}" style="display: block"
                                    {!! !empty($charm_used_in) ? 'title="'.e($charm_used_in).'"' : '' !!}>{{ $charm_name }}</a><?php
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
                            <td>
                                <span onclick="copyString('{{ '#'.$triples[$ts]['lily:color'][0] }}')"
                                      style="font-weight: bold; color: {{ '#'.$triples[$ts]['lily:color'][0] }}; cursor: pointer;"
                                      title="クリックしてカラーコードをコピー">
                                    {{ '#'.$triples[$ts]['lily:color'][0] }}
                                </span>
                            </td>
                        </tr>
                    @endif
                    @if(!empty($triples[$ts]['lily:cast']))
                        <tr>
                            <th>キャスト</th>
                            <td rowspan="2" style="height: 4em;">
                                <?php
                                foreach ($triples[$ts]['lily:cast'] ?? array() as $cast){
                                // キャスト名がない場合のスキップ
                                if(empty($triples[$cast]['schema:name'][0])) continue;
                                ?>
                                    <details>
                                        <summary>{{ $triples[$cast]['schema:name'][0] }}</summary>
                                <?php
                                foreach ($triples[$cast]['lily:performIn'] ?? array() as $play){
                                    ?><div style="font-size: smaller; padding-left: .5em">
                                            <span class="indicator">{{ $triples[$play]['lily:genre'][0] ?? '不明' }}</span>
                                            @if($triples[$play]['rdf:type'][0] === 'lily:Play')
                                                <a href="{{ route('play.show', ['play' => str_replace('lilyrdf:', '', $play)]) }}">
                                                    {{ (mb_strlen($triples[$play]['schema:name'][0]) > 30 and !empty($triples[$play]['schema:alternateName'][0]))
                                                        ? $triples[$play]['schema:alternateName'][0]
                                                        : $triples[$play]['schema:name'][0] }}
                                                </a>
                                            @else
                                                {{ $triples[$play]['schema:name'][0] }}
                                            @endif

                                        </div><?php
                                }
                                ?></details><?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="spacer"></td>
                        </tr>
                    @endif
                    @if(!empty($triples[$ts]['lily:additionalInformation']))
                        @include('app.lilyprofiletable.record',['object' => $triples[$ts]['lily:additionalInformation'] ?? null, 'th' => '特記事項'])
                    @endif
                    </tbody>
                </table>
                <div style="display: flex; justify-content: space-between">
                    <div>
                        <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-lang="ja" data-show-count="false">Tweet</a>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    </div>
                    <div style="font-size: smaller;">
                        <a href="{{ route('rdfDescribe', ['resource' => removePrefix($ts)]) }}" class="button smaller">RDF生データ参照</a>
                    </div>
                </div>
                @if(!empty($icon))
                    <div style="font-size: smaller;">
                        アイコン作者 : {{ $icon->author }} さん
                    </div>
                @endif
                @auth
                    <div>
                        <h3>管理用</h3>
                        <div class="buttons two">
                            <a href="{{ route('admin.image.create', ['for' => removePrefix($ts)]) }}" class="button">画像を追加</a>
                            <a href="{{ route('admin.triple.create', ['lily_slug' => removePrefix($ts)]) }}" class="button" target="_blank">独自トリプルを追加</a>
                            <a href="javascript:copyString('{{ removePrefix($ts) }}')" class="button" target="_self">リソース名をコピー</a>
                            <a href="javascript:window.open('{{ route('qr',['data' => removePrefix($ts), 'label' => removePrefix($ts)]) }}', '_blank', 'width=300,height=300')" class="button" target="_self">QRコード</a>
                        </div>
                    </div>
                @endauth
            </div>
            <div class="right">
                <div id="right-tag">
                    @if(!empty($triples[$ts]['lily:lifeStatus']) and $triples[$ts]['lily:lifeStatus'][0] === 'dead')
                        <div class="KIA">故人</div>
                    @endif
                    @if(!empty($triples[$ts]['lily:lifeStatus']) and $triples[$ts]['lily:lifeStatus'][0] === 'unknown')
                        <div class="KIA">生死不明</div>
                    @endif
                    @if(!empty($triples[$ts]['lily:killedIn'][0]))
                        <div class="KIA">{{ $triples[$ts]['lily:killedIn'][0] }}<br>戦死・殉職者</div>
                    @endif
                    @if(!empty($triples[$ts]['lily:isBoosted']) and $triples[$ts]['lily:isBoosted'][0] === 'true')
                        <div class="boosted">強化リリィ</div>
                    @endif
                    @if(!empty($triples[$ts]['rdf:type']) and $triples[$ts]['rdf:type'][0] === 'lily:Teacher')
                        <div class="teacher">教導官</div>
                    @endif
                    @if(!empty($triples[$ts]['rdf:type']) and $triples[$ts]['rdf:type'][0] === 'lily:Madec')
                        <div class="madec">マディック</div>
                    @endif
                </div>

                <?php
                /** @var \Illuminate\Support\Collection $memorias */
                ?>
                @if(!$memorias->isEmpty())
                    <?php $memoria = $memorias->random(); ?>
                    <div id="pics">
                        <img src="{{ $memoria->image_url }}" alt="{{ $memoria->author }}">
                        <div id="memoria-info">メモリア寄稿 : {{ $memoria->author }}</div>
                    </div>
                @else
                    <div id="pics">
                        <div style="text-align: center; padding-top: 130px; color: gray;">
                            <div style="margin-bottom: .4em">Image Unavailable</div>
                            <p style="font-size: smaller">
                                メモリア(写真やイラストなど)の投稿がランダムで表示されます
                            </p>
                        </div>
                    </div>
                @endif
                <div>
                    <h3>イラスト・画像を探す</h3>
                    <div class="buttons two">
                        <a href="https://twitter.com/search?q={{ urlencode('#アサルトリリィ_FA '.($triples[$ts]['schema:givenName'][0] ?? $triples[$ts]['schema:name'][0])) }}&f=live"
                           target="_blank" class="button">
                            <i class="fab fa-twitter"></i>
                            #アサルトリリィ_FA
                        </a>
                        <a href="https://www.pixiv.net/tags/{{ urlencode($triples[$ts]['schema:name'][0]) }}/illustrations"
                           target="_blank" class="button">
                            pixiv イラストタグ検索
                        </a>
                        <a href="https://www.google.com/search?q={{ urlencode($triples[$ts]['schema:name'][0]) }}&tbm=isch"
                           target="_blank" class="button">
                            <i class="fab fa-google"></i>
                            Google画像検索
                        </a>
                    </div>
                </div>

                <div id="links">
                    <h3>公式リンク</h3>
                    <?php
                    $slug = strtolower($triples[$ts]['schema:givenName@en'][0] ?? '');

                    $tweet_search = 'https://twitter.com/search?q=from%3A'.config('lemonade.fumi.twitter').'%20';
                    $tweet_search .= urlencode($triples[$ts]['schema:givenName'][0] ?? '').'&f=live';

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
                    // 中間ノードのない関係
                    foreach ($directRel as $key => $psr){
                        foreach ($triples[$ts][$key] ?? array() as $psrLily){
                            $relationship[$psrLily][] = $psr;
                        }
                    }
                    // 中間ノードのある関係
                    foreach ($IntermediateRel as $predicate){
                        foreach ($triples[$ts][$predicate] ?? array() as $imRel){
                            if (empty($relationship[$triples[$imRel]['lily:resource'][0]]))
                                $relationship[$triples[$imRel]['lily:resource'][0]] = array();
                            $relationship[$triples[$imRel]['lily:resource'][0]] =
                                array_merge($relationship[$triples[$imRel]['lily:resource'][0]], $triples[$imRel]['lily:additionalInformation'] ?? array());
                        }
                    }
                    ?>
                @if(!empty($relationship))
                    <div>
                        <h3>関連する人物</h3>
                        <div class="list" id="relationship">
                            @foreach($relationship as $lilyrdf => $relation)
                                <a href="{{ route('lily.show', ['lily' => removePrefix($lilyrdf)]) }}"
                                   class="list-item-a">
                                    <div class="list-item-data">
                                        <div class="title" style="font-size: 17px">{{ $triples[$lilyrdf]['schema:name'][0] ?? '' }}</div>
                                        @foreach($relation ?? array() as $info)
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

        @if($icons->isNotEmpty())
            <h2>アイコン一覧</h2>
            <div class="white-box">
                <p class="center">以下の方々からアイコン画像をご提供いただいています。深く御礼申し上げます。</p>
                <hr>
                <div class="list two" id="icon-list">
                    @foreach($icons as $icon)
                        <?php
                        $infos = array();
                        foreach (explode("\n", str_replace(array("\r\n", "\r", "\n"), "\n", $icon->author_info)) as $line){
                            $title = e(explode(',', $line)[0]);
                            $value = e(explode(',', $line)[1]);
                            switch ($title){
                                case 'twitter':
                                    $infos[] = "Twitter : <a href='https://twitter.com/$value' target='_blank'>@$value</a>";
                                    break;
                                case 'pixiv':
                                    $infos[] = "pixiv : <a href='https://pixiv.net/$value' target='_blank'>$value</a>";
                                    break;
                                default:
                                    $infos[] = "$title : <a href='$value' target='_blank'>$value</a>";
                            }
                        }
                        ?>
                        <div class="list-item-b">
                            <div class="list-item-image">
                                <img class="pic" src="{{ $icon->image_url }}" alt="icon">
                            </div>
                            <div class="list-item-data">
                                <div class="title">{{ $icon->author }}<span style="font-size: small">さん</span></div>
                                @foreach($infos as $info)<div style="margin: .5em 0">{!! $info !!}</div>@endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
@endsection
