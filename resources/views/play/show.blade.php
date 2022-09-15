<?php
/**
 * @var $play array
 * @var $playSlug string
 * @var $urls \Illuminate\Database\Eloquent\Collection
 * @var $url \App\Models\Url
 */

$ps = 'lilyrdf:'.$playSlug;
$ogp['type'] = 'play';
$ogp_genre = !empty($play[$ps]['lily:genre'][0]) ? $play[$ps]['lily:genre'][0].' ' : '';
$ogp['title'] = $ogp_genre.((mb_strlen($play[$ps]['schema:name'][0]) > 30 and !empty($play[$ps]['schema:alternateName'][0]))
    ? $play[$ps]['schema:alternateName'][0]
    : $play[$ps]['schema:name'][0]);
$ogp['description'] = $ogp_genre.$play[$ps]['schema:name'][0]." の情報です。"
?>

@extends('app.layout', ['title' => '公演詳細', 'titlebar' => $play[$ps]['schema:name'][0]])

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
            <h3 style="display: flex; justify-content: flex-start; align-items: flex-end">
                <span style="margin-right: 10px">公演概要</span>
                <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-lang="ja" data-show-count="false">Tweet</a>
                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
            </h3>
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
            @if(!empty($urls->toArray()))
                <details class="white-box">
                    <summary style="margin: .2em 0 0">この公演に関連する外部リンク</summary>
                    @foreach($urls as $url)
                        <a href="{{ $url->url }}" target="_blank" class="list-item-b" style="padding: 10px 20px;">
                            <div class="title">{{ $url->title }} <i class="fas fa-external-link-alt" style="font-size: smaller"></i></div>
                            <div style="font-size: small; color: dimgray">{{ $url->url }}</div>
                            <div style="font-size: smaller; margin-top: 3px;">{{ $url->description }}</div>
                        </a>
                    @endforeach
                    <hr>
                    <p class="center" style="font-size: smaller">
                        これらのリンク先はLemonadeが管理しているウェブサイトではありません。<br>
                        また、ここに掲載しているリンクは管理者が手作業で登録しているものです。<br>
                        これらのリンク先は今後移転・削除される、もしくは既にされている可能性があります。
                    </p>
                </details>
            @endif
            @if(Auth::user())
                <div class="buttons" style="text-align: right">
                    <a href="{{ route('admin.url.create', ['for' => $playSlug]) }}" class="button smaller" target="_blank">
                        関連URLの追加
                    </a>
                </div>
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
                        <th>初演</th><td>{{ convertDateString($play[$ps]['schema:startDate'][0])->isoFormat('YYYY年M月D日 (ddd)') }}</td>
                        <th>千穐楽</th><td>{{ convertDateString($play[$ps]['schema:endDate'][0])->isoFormat('YYYY年M月D日 (ddd)') }}</td>
                    </tr>
                    <tr>
                        <th>主催</th><td colspan="3">{{ implode(', ' ,$play[$ps]['lily:organizer'] ?? array()) }}</td>
                    </tr>
                    <tr>
                        <th>プロデューサー</th><td colspan="3">{!! e(implode(', ' ,$play[$ps]['lily:producer'] ?? array())) ?: '<span style="color:gray">N/A</span>' !!}</td>
                    </tr>
                    <tr>
                        <th>脚本</th><td>{{ implode(', ' ,$play[$ps]['lily:scenarioWriter'] ?? array()) }}</td>
                        <th>演出</th><td>{{ implode(', ' ,$play[$ps]['lily:episodeDirection'] ?? array()) }}</td>
                    </tr>
                    <tr>
                        <th>作曲</th><td>{!! e(implode(', ' ,$play[$ps]['lily:composer'] ?? array())) ?: '<span style="color:gray">N/A</span>' !!}</td>
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
                    @if(!empty($play[$ps]['lily:additionalInformation']))
                        <tr>
                            <th>特記事項</th><td colspan="3" rowspan="2">
                                @foreach($play[$ps]['lily:additionalInformation'] as $playInfo)
                                    <div style="margin-bottom: .5em">{{ $playInfo }}</div>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td class="spacer"></td>
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
                                            @if(count($play[$cast]['lily:performAs']) > 0)
                                                (
                                                @foreach($play[$cast]['lily:performAs'] as $performAs)
                                                    @if(!empty($play[$performAs]))
                                                        <a href="{{ route('lily.show', ['lily' => str_replace('lilyrdf:', '', $performAs)]) }}">
                                                            {{ $play[$performAs]['schema:name'][0] }}</a> 役
                                                    @else
                                                        {{ $performAs }} 役
                                                    @endif
                                                    @if(!$loop->last)
                                                        ・
                                                    @endif
                                                @endforeach
                                                )
                                            @endif
                                        </td>
                                    </tr>
                                    @if(!empty($play[$cast]['lily:additionalInformation']))
                                        <tr>
                                            <td colspan="2" style="font-size: small; padding-left: 2em">
                                                @foreach($play[$cast]['lily:additionalInformation'] as $castInfo)
                                                    <div>{{ $castInfo }}</div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>

                        </td>

                        <th>公演日時</th>
                        <td rowspan="2">
                            @foreach($play[$ps]['lily:showTime'] as $showTime)
                                @if(in_array($showTime, $play[$ps]['lily:cancelledShowTime'] ?? [], true))
                                    <div style="min-width: 220px;">
                                        <span style="text-decoration: line-through; color: gray">
                                            {{ \Carbon\Carbon::make($showTime)->isoFormat('YYYY年M月D日 (ddd) HH:mm') }}</span>
                                        <span class="indicator">中止</span>
                                    </div>
                                @else
                                    <div style="min-width: 220px;">{{ \Carbon\Carbon::make($showTime)->isoFormat('YYYY年M月D日 (ddd) HH:mm') }}</div>
                                @endif
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
