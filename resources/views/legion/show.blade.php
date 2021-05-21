<?php
/**
 * @var $legion array
 * @var $legionSlug string
 */
$ls /* LegionSubject */ = 'lilyrdf:'.$legionSlug;

$ogp['title'] = $legion[$ls]['schema:name'][0];
$ogp['type'] = 'legion';
$ogp['description'] = "レギオン「{$legion[$ls]['schema:name'][0]}".
    (!empty($legion[$ls]['schema:alternateName'][0]) ?
        '('.$legion[$ls]['schema:alternateName'][0].')' : '')
    ."」の情報を閲覧できます。";
if(!empty($legion[$ls]['lily:numberOfMembers'][0])) $ogp['description'] .= " {$legion[$ls]['lily:numberOfMembers'][0]}人のリリィが所属しています。";

if(empty($legion[$ls]['lily:disbanded'][0]) || $legion[$ls]['lily:disbanded'][0] === 'true'){
    $additional = null;
}else{
    $additional = ['key' => 'lily:legionJobTitle'];
}

?>

@extends('app.layout', [
    'title' => 'レギオン詳細',
    'titlebar' => $legion[$ls]['schema:name'][0]])

@section('head')
    <link rel="stylesheet" href="{{ asset('css/legion.css') }}">
    <style>
        @media screen and (max-width: 500px) and (orientation: portrait){
            #legion-summary{
                padding-left: 15px;
                flex-wrap: wrap;
            }
            #legion-other-info{
                margin-top: 10px;
            }
        }
    </style>
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
        <div id="legion-other-info">
            @if(!empty($legion[$ls]['rdf:type'][0]) and $legion[$ls]['rdf:type'][0] == 'lily:Taskforce')
                <div class="more-info">臨時・特別部隊</div>
            @endif
            @if(!empty($legion[$ls]['lily:disbanded']) and $legion[$ls]['lily:disbanded'][0] == 'true')
                <div class="more-info">解散済み</div>
            @endif
            @if(!empty($legion[$ls]['lily:numberOfMembers'][0]))
                <div class="more-info">所属数 : {{ $legion[$ls]['lily:numberOfMembers'][0] }}</div>
            @else
                <div class="more-info">所属数 : 不明</div>
            @endif
            <div style="margin-left: 20px">
                <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-lang="ja" data-show-count="false">Tweet</a>
                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
            </div>
        </div>

    </div>
    <main>
        <?php
        $disbanded = !empty($legion[$ls]['lily:disbanded'][0]) && $legion[$ls]['lily:disbanded'][0] === 'true';
        $membersArray = [
            'schema:member' => ($disbanded ? '解散時点での' : '').'所属リリィ',
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
                            /** @var string $member
                             *  @var string $key
                             */
                            if($key !== 'schema:member') $additional = null;
                            ?>
                            @include('app.button_lily',['key' => $member, 'lily' => $legion[$member], 'legion' => $legion[$legion[$member]['lily:legion'][0] ?? '-'] ?? array(), 'additional' => $additional, 'icons' => $icons[$member] ?? array()])
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


