<?php
/**
 * @var $details array
 * @var $series string
 * @var $episode string
 */
$ts = 'lilyrdf:'.$episode;
$isBouquet = str_contains($details[$ts]['schema:name@en'][0], 'Assault Lily BOUQUET');
$fontOverride = $isBouquet ? "font-family: 'Noto Serif JP', serif;" : null;

if(!empty($details[$ts]['lily:subtitle'][0])){
    $ogp['title'] = '第'.$details[$ts]['schema:episodeNumber'][0].'話 '.$details[$ts]['lily:subtitle'][0];
}else{
    $ogp['title'] = $details[$ts]['schema:name'][0];
}
$ogp['type'] = 'anime-episode';
$ogp['description'] = "アニメ ".$details[$ts]['schema:name'][0].' '.($details[$ts]['lily:subtitle'][0] ?? '')." のデータです。";
?>

@extends('app.layout', ['title' => '各話詳細', 'titlebar' => $details[$ts]['schema:name'][0]])

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
        #character_list{
            flex-shrink: 1;
            width: 350px;
            margin-left: 15px;
            background: rgba(100,100,100,0.1);
        }
        #character_list > h2{
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
        #character_list > h2::before{
            position: static;
        }

        #episode_board{
            text-align: center;
            font-family: 'Noto Serif JP', serif;
            font-weight: 500;
            margin-top: -60px;
            padding: 75px 0 60px;
            background: linear-gradient(
            {{ '#'.($details[$ts]['lily:subtitleColor'][0] ?? 'ffffff') }},
            {{ '#'.($details[$ts]['lily:subtitleColor'][0] ?? 'ffffff') }} 85%,
            transparent
            );
            @if(($details[$ts]['lily:subtitleColor'][0] ?? '') === '000000')
            color: white;
            @endif
        }
        #episode_board > #episode_no{
            font-size: 20px;
        }
        #episode_board > #episode_no > span{
            font-size: 30px;
            vertical-align: center;
            padding: 0 20px;
        }
        #episode_board > #subtitle{
            font-size: 60px;
            margin: 15px 0;
        }
        #episode_board > #subtitle_en{
            font-size: 20px;
            font-weight: normal;
            margin: 15px 0;
        }
        #episode_board > #parentheses{
            font-weight: normal;
            margin: 25px 0 15px;
        }

        @media screen and (max-width:500px) and (orientation:portrait){
            #container{
                display: block;
            }
            #character_list{
                width: 100%;
                margin: 0;
            }
        }
    </style>
@endsection

@section('main')
    @if($isBouquet)
        <div id="episode_board">
            <div id="episode_no">
                第<span>{{ $details[$ts]['schema:episodeNumber'][0] }}</span>話
            </div>
            <div id="subtitle">{{ $details[$ts]['lily:subtitle'][0] }}</div>
            <div id="subtitle_en">{{ $details[$ts]['lily:subtitle@en'][0] }}</div>
            <div id="parentheses">
                {{ $details[$ts]['lily:parentheses@en'][0]}} - {{ $details[$ts]['lily:parentheses'][0] }}
            </div>
        </div>
    @endif
    <main>
        @if(!$isBouquet) <h1 style="{!! $fontOverride !!}">{{ $details[$ts]['schema:name'][0] }}</h1> @endif
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
                    'lily:storyboard' => '絵コンテ',
                    'lily:episodeDirection' => '演出',
                    'lily:chiefAnimationSupervisor' => '総作画監督',
                    'lily:CASAssistance' => '総作画監督協力',
                    'lily:animationSupervisor' => '作画監督',
                    'lily:ASAssistance' => '作画監督協力',
                    'lily:actionDirector' => 'アクションディレクター',
                    'lily:assistantDirector' => '副監督',
                    'lily:chiefEpisodeDirection' => 'チーフ演出',
                    'lily:charmDesign' => 'CHARMデザイン',
                    'lily:hugeDesign' => 'HUGELYデザイン',
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
                <div>
                    <a href="{{ route('anime.series.show', ['series' => $series]) }}" class="button smaller">
                        <i class="fas fa-arrow-left"></i>
                        シリーズ詳細・各話リストに戻る
                    </a>
                </div>
            </div>
            @if(!empty($details[$ts]['schema:character']))
                <div id="character_list">
                    <h2>登場したリリィ・人物</h2>
                    <div style="padding: 10px; max-height: 500px; overflow-y: scroll;">
                        @foreach($details[$ts]['schema:character'] as $character)
                            @if(empty($details[$character])) @continue @endif
                            <a href="{{ route('lily.show', ['lily' => removePrefix($character)]) }}" class="list-item-a" style="display: block; width: calc(100% - 6px); margin-bottom: 10px">
                                <div class="list-item-data">
                                    <div class="title" style="font-size: 16px">
                                        @if(!empty($details[$character]['lily:color'][0]))
                                            <span style="color: {{ '#'.$details[$character]['lily:color'][0] }}">■</span>
                                        @endif
                                        {{ $details[$character]['schema:name'][0] ?? '' }}
                                    </div>
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
