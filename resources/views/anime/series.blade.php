<?php
/**
 * @var $details array
 * @var $series string
 */
$ts = 'lilyrdf:'.$series;
$fontOverride = $details[$ts]['schema:name@en'][0] === 'Assault Lily BOUQUET'
    ? "font-family: 'Noto Serif JP', serif;" : null;
if (!empty($details[$ts]['schema:episode']) && is_array($details[$ts]['schema:episode'])){
    $episodes = array();
    $episode_no = array();
    foreach ($details[$ts]['schema:episode'] as $ep){
        $episodes[$ep] = $details[$ep];
        $episode_no[$ep] = $details[$ep]['schema:episodeNumber'][0] ?? null;
    }
    array_multisort($episode_no, SORT_ASC, $episodes);
}

$ogp['title'] = $details[$ts]['schema:name'][0];
$ogp['type'] = 'anime-series';
$ogp['description'] = "アニメシリーズ「".$details[$ts]['schema:name'][0]."」のデータです。";
?>

@extends('app.layout', ['title' => 'シリーズ詳細', 'titlebar' => $details[$ts]['schema:name'][0]])

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        #container{
            display: flex;
        }
        #details{
            flex-shrink: 1;
            width: 100%;
            overflow: hidden;
        }
        #episode_list{
            flex-shrink: 0;
            width: 350px;
            margin-left: 15px;
            background: rgba(100,100,100,0.1);
        }
        #episode_list > h2{
            margin: 0;
            font-size: 18px;
            padding: 5px 12px;
            background-color: #3d4352;
            background-position: bottom right;
            background-image:
                linear-gradient(-25deg,
                transparent, transparent 30px,
                rgba(255,255,255,0.1) 31px, rgba(255,255,255,0.1) 80px,
                transparent 81px, transparent),

                linear-gradient(215deg,
                transparent, transparent 40px,
                rgba(150,150,150,0.1) 41px, rgba(150,150,150,0.1) 90px,
                transparent 91px, transparent),

                linear-gradient(260deg,
                rgba(200,200,200,0.1), rgba(200,200,200,0.1) 50px,
                transparent 51px, transparent);

            border-left: solid 4px #36d0ca;
            color: white;
            font-weight: normal;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        #episode_list > h2::before{
            position: static;
        }

        @media screen and (max-width:500px) and (orientation:portrait){
            #container{
                display: block;
            }
            #episode_list{
                width: 100%;
                margin: 0;
            }
        }
    </style>
@endsection

@section('main')
    <main>
        <h1 style="{!! $fontOverride !!}">{{ $details[$ts]['schema:name'][0] }}</h1>
        <div id="container">
            <div id="details">
                @if(!empty($details[$ts]['schema:abstract'][0]))
                    <p style="line-height: 1.5em; {!! $fontOverride !!}">
                        {!! nl2br(strip_tags($details[$ts]['schema:abstract'][0])) !!}
                    </p>
                @endif
                <?php
                $staff_list = [
                    'lily:originalAuthor' => '原作',
                    'lily:characterDesign' => 'キャラクターデザイン',
                    'lily:subCharacterDesign' => 'サブキャラクターデザイン',
                    'lily:scenarioWriter' => 'ストーリー原案',
                    'lily:chiefAnimationSupervisor' => '総作画監督',
                    'lily:actionDirector' => 'アクションディレクター',
                    'lily:assistantDirector' => '副監督',
                    'lily:chiefEpisodeDirection' => 'チーフ演出',
                    'lily:charmDesign' => 'CHARMデザイン',
                    'lily:hugeDesign' => 'HUGEデザイン',
                    'lily:backgroundArtDirector' => '美術監督',
                    'lily:colorDesign' => '色彩設計',
                    'lily:compositingDirector' => '撮影監督',
                    'lily:CGDirector' => '3DCGディレクター',
                    'lily:editor' => '編集',
                    'lily:soundDirector' => '音響監督',
                    'lily:music' => '音楽',
                    'lily:musicProducer' => '音楽プロデューサー',
                    'lily:musicProduction' => '音楽制作',
                    'lily:producer' => 'プロデューサー',
                    'lily:animationProducer' => 'アニメーションプロデューサー',
                    'lily:director' => '監督',
                    'lily:animationProduction' => 'アニメーション制作',
                    'lily:production' => '制作',
                ]
                ?>
                <table class="table">
                    @foreach($staff_list as $predicate => $position)
                        @empty($details[$ts][$predicate]) @continue @endempty
                        <tr>
                            <th>{{ $position }}</th>
                            <td>{{ implode(', ', $details[$ts][$predicate]) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            @if(!empty($details[$ts]['schema:episode']))
                <div id="episode_list">
                    <h2>各話リスト</h2>
                    <div style="padding: 10px">
                        @foreach($episodes as $epKey => $ep)
                            <a href="{{ route('anime.episode.show', ['series' => $series, 'episode' => removePrefix($epKey)]) }}" class="list-item-a" style="display: block; width: calc(100% - 6px); margin-bottom: 10px">
                                <div class="list-item-data">
                                    <div class="title">
                                        @if(!empty($ep['schema:episodeNumber'][0]))
                                            <span style="font-size: smaller">第{{ $ep['schema:episodeNumber'][0] }}話</span>
                                        @endif
                                        @if(!empty($ep['lily:subtitleColor'][0]))
                                            <span style="color: {{ '#'.$ep['lily:subtitleColor'][0] }}"
                                                  title="{{ '#'.$ep['lily:subtitleColor'][0] }}">■</span>
                                        @endif
                                        {{ $ep['lily:subtitle'][0] }}
                                    </div>
                                    <div>{{ $ep['lily:parentheses'][0] ?? '' }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>


        @if(config('app.debug')) @dump($details) @endif
    </main>
@endsection
