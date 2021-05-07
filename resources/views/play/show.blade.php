<?php
/**
 * @var $play array
 * @var $playSlug string
 */

$ps = 'lilyrdf:'.$playSlug;
$ogp['type'] = 'play';
$ogp['title'] = "舞台 ".((mb_strlen($play[$ps]['schema:name'][0]) > 30 and !empty($play[$ps]['schema:alternateName'][0]))
    ? $play[$ps]['schema:alternateName'][0]
    : $play[$ps]['schema:name'][0]);
$ogp['description'] = "舞台 ".$play[$ps]['schema:name'][0]." の情報です。"
?>

@extends('app.layout', ['title' => '舞台公演詳細', 'titlebar' => $play[$ps]['schema:name'][0]])

@section('head')
    <style>
        @media screen and (max-width: 500px) and (orientation: portrait){
            #summary{
                overflow-x: scroll;
            }
            #summary::before{
                content: '横スクロールできます';
                font-size: small;
                color: dimgray;
                font-style: italic;
            }
            #summary > table{
                width: auto;
                white-space: nowrap;
            }
        }

        table#cast-list{
            width: 100%;
        }
        table#cast-list td{
            border: none;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>{{ $play[$ps]['schema:name'][0] }}</h1>
        <div class="white-box">
            @if(!empty($play[$ps]['schema:abstract'][0]))
                <h3>あらすじ</h3>
                <p class="center" style="line-height: 2em">{!! nl2br($play[$ps]['schema:abstract'][0]) ?: "<span style='color:gray;'>N/A</span>" !!}</p>
            @endif
            <h3>公演概要</h3>
            @if(\Carbon\Carbon::now()->lt(convertDateString($play[$ps]['schema:startDate'][0])))
                <p class="center notice">
                    <strong>この公演はまだ初演前です。</strong><br>
                    日程やキャストなどに変更が生じる場合があり、ここに掲載されている情報は現時点においては正確でないかもしれません。<br>
                    最新の情報は必ず公式ウェブサイト等でご確認ください。
                </p>
            @elseif(\Carbon\Carbon::now()->lt(convertDateString($play[$ps]['schema:endDate'][0])))
                <p class="center notice">
                    <strong>この公演はまだすべての日程を終えていません。</strong><br>
                    最新の情報は必ず公式ウェブサイト等でご確認ください。
                </p>
            @endif
            <div id="summary">
                <table class="table">
                    <tr>
                        <th>会場</th>
                        <td>{{ $play[$ps]['schema:location'][0] }}
                            <a href="https://www.google.com/maps/search/{{ $play[$ps]['schema:location'][0] }}" target="_blank" class="button smaller">Google Map</a>
                        </td>
                        <th>略称</th><td>{!! e($play[$ps]['schema:alternateName'][0] ?? '') ?: "<span style='color:gray'>N/A</span>" !!}</td>
                    </tr>
                    <tr>
                        <th>初演</th><td>{{ convertDateString($play[$ps]['schema:startDate'][0])->format('Y年n月j日') }}</td>
                        <th>千穐楽</th><td>{{ convertDateString($play[$ps]['schema:endDate'][0])->format('Y年n月j日') }}</td>
                    </tr>
                    <tr>
                        <th>主催</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:organizer'] ?? array()) }}</td>
                    </tr>
                    <tr>
                        <th>プロデューサー</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:producer'] ?? array()) }}</td>
                    </tr>
                    <tr>
                        <th>脚本</th><td>{{ implode(', ' ,$play[$ps]['lily:scenarioWriter'] ?? array()) }}</td>
                        <th>演出</th><td>{{ implode(', ' ,$play[$ps]['lily:episodeDirection'] ?? array()) }}</td>
                    </tr>
                    <tr>
                        <th>作曲</th><td>{{ implode(', ' ,$play[$ps]['lily:composer'] ?? array()) }}</td>
                        <th>原作</th><td>{{ implode(', ' ,$play[$ps]['lily:originalAuthor'] ?? array()) }}</td>
                    </tr>
                    <tr>
                        <th>企画</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:planning'] ?? array()) }}</td>
                    </tr>
                    <tr>
                        <th>制作</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:production'] ?? array()) }}</td>
                    </tr>
                    @if(!empty($play[$ps]['lily:contributor']))
                        <tr>
                            <th>協力・協賛</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:contributor'] ?? array()) }}</td>
                        </tr>
                    @endif
                    @if(!empty($play[$ps]['lily:cooperation']))
                        <tr>
                            <th>運営協力</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:cooperation'] ?? array()) }}</td>
                        </tr>
                    @endif
                    @if(!empty($play[$ps]['lily:supervisor']))
                        <tr>
                            <th>スーパーバイザー</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:supervisor'] ?? array()) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>キャスト</th>
                        <td rowspan="2">
                            <table id="cast-list">
                                @foreach($play[$ps]['lily:cast'] as $cast)
                                    <tr>
                                        <td>{{ $play[$cast]['schema:name'][0] }}</td>
                                        <td>
                                            @if(!empty($play[$cast]['lily:performAs'][0]))
                                                @if(!empty($play[$play[$cast]['lily:performAs'][0]]) and $play[$play[$cast]['lily:performAs'][0]]['rdf:type'][0] === 'lily:Lily')
                                                    {{ '(' }} <a href="{{ route('lily.show', ['lily' => str_replace('lilyrdf:', '', $play[$cast]['lily:performAs'][0])]) }}">
                                                        {{ $play[$play[$cast]['lily:performAs'][0]]['schema:name'][0] }}</a> 役 )
                                                @elseif(!empty($play[$play[$cast]['lily:performAs'][0]]) and !empty($play[$play[$cast]['lily:performAs'][0]]['schema:name'][0]))
                                                    {{ '( '.$play[$play[$cast]['lily:performAs'][0]]['schema:name'][0] }} 役 )
                                                @else
                                                    {{ '( '.$play[$cast]['lily:performAs'][0] }} 役 )
                                                @endif
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </table>

                        </td>

                        <th>公演日時</th>
                        <td rowspan="2">
                            @foreach($play[$ps]['lily:showTime'] as $showTime)
                                <div>{{ \Carbon\Carbon::make($showTime)->format('Y年n月j日 G:i') }}</div>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="spacer"></td>
                        <td class="spacer"></td>
                    </tr>
                </table>
            </div>

        </div>
        @if(config('app.debug')) @dump($play) @endif
    </main>
@endsection
