<?php
/**
 * @var $book array
 * @var $lilies array
 */

$ogp['title'] = $book['schema:name'][0];
$ogp['type'] = 'book';
$ogp['description'] = '書籍「'.$book['schema:name'][0].'」の情報です。';
?>

@extends('app.layout', ['title' => '書籍詳細', 'titlebar' => $book['schema:name'][0]])

@section('head')
    <style>
        #book-summary{
            display: flex;
            flex-direction: row-reverse;
        }
        #thumbnail{
            display: block;
            object-fit: scale-down;
            width: 300px;
            flex-shrink: 0;
            margin-left: 20px;
            margin-top: 10px;
            background: rgba(0,0,0,0.1);
            text-align: center;
        }

        .list.four{
            display: block;
            box-sizing: border-box;
        }
        .list.four > .list-item-a{
            width: calc(24% - 6px);
            margin-right: 1%;
        }
        .list.four > .list-item-a:nth-child(4n){
            margin-right: 0;
        }

        @media screen and (max-width: 500px) and (orientation: portrait) {
            #book-summary{
                display: block;
            }
            #thumbnail{
                width: 100%;
                height: 350px;
                margin: 10px 0;
            }
            .list.four > .list-item-a{
                width: calc(100% - 6px);
                margin-right: 0;
            }
            th{
                width: 150px;
            }
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>{{ $book['schema:name'][0] }}</h1>
        <div id="book-summary" class="white-box">
            @if(!empty($book['book.thumbnail'][0]))
                <img src="{{ $book['book.thumbnail'][0] }}" alt="{{ $book['book.thumbnail'][0] }}"
                     id="thumbnail">
            @else
                <div id="thumbnail">
                    <div style="padding-top: 40%">Image Unavailable</div>
                </div>
            @endif
            <div style="width: 100%">
                <table class="table">
                    <tbody>
                    <tr>
                        <th>ジャンル</th><td>{{ $book['lily:genre'][0] }}</td>
                    </tr>
                    <tr>
                        <th>著者</th><td>{{ implode(', ',$book['schema:author'] ?? array()) ?? 'データなし' }}</td>
                    </tr>
                    @if(!empty($book['lily:originalAuthor']))
                        <tr>
                            <th>原作者</th>
                            <td>{{ implode(', ',$book['lily:originalAuthor'] ?? array()) }}</td>
                        </tr>
                    @endif
                    @if(!empty($book['schema:illustrator']))
                        <tr>
                            <th>イラストレーター</th>
                            <td>{{ implode(', ',$book['schema:illustrator'] ?? array()) }}</td>
                        </tr>
                    @endif
                    @if(!empty($book['schema:publisher']))
                        <tr>
                            <th>出版者</th>
                            <td>{{ implode(', ',$book['schema:publisher'] ?? array()) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>発行年月日</th><td>{{ convertDateString($book['schema:datePublished'][0] ?? '')->format('Y年n月j日') ?: 'N/A' }}</td>
                    </tr>
                    @if(!empty($book['schema:numberOfPages'][0]))
                        <tr>
                            <th>ページ数</th><td>{{ $book['schema:numberOfPages'][0] ?? 'N/A' }}</td>
                        </tr>
                    @endif
                    @if(!empty($book['schema:isbn']))
                        <tr>
                            <th>国際標準図書番号</th>
                            <td colspan="3">
                                {{ 'ISBN'.$book['schema:isbn'][0] }}
                                <a href="https://www.hanmoto.com/bd/isbn/{{ str_replace('-','',$book['schema:isbn'][0]) }}"
                                   class="button smaller" target="_blank" title="版元ドットコム">
                                    版元ドットコム
                                </a>
                                <a href="https://www.e-hon.ne.jp/bec/SA/Detail?refISBN={{ str_replace('-','',$book['schema:isbn'][0]) }}"
                                   class="button smaller" target="_blank" title="全国書店ネットワーク e-hon">
                                    全国書店ネットワーク e-hon
                                </a>
                                <a href="https://iss.ndl.go.jp/books?rft.isbn={{ $book['schema:isbn'][0] }}&search_mode=advanced"
                                   class="button smaller" target="_blank" title="国立国会図書館サーチ">
                                    国立国会図書館サーチ
                                </a>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div>
                    <a href="https://x.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-lang="ja" data-show-count="false">Post</a>
                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                </div>
                <div id="abstract">
                    <h3>あらすじ</h3>
                    <p>{!! nl2br(trim($book['schema:abstract'][0] ?? '')) ?: "<span style='color:gray'>あらすじが登録されていません</span>" !!}</p>
                </div>
            </div>
        </div>
        <h2>登場するリリィ</h2>
        <div class="list four">
            @forelse($book['schema:character'] as $lily)
                @continue(empty($lilies[$lily]))
                <a href="{{ route('lily.show',['lily' => str_replace('lilyrdf:','',$lily)]) }}" class="list-item-a">
                    <div class="list-item-data">
                        <div class="title">{{ $lilies[$lily]['schema:name'][0] }}</div>
                        <div>
                            {{ $lilies[$lily]['lily:garden'][0] ?? 'ガーデン情報なし' }}
                            {{ !empty($lilies[$lily]['lily:grade'][0]) ? convertGradeString($lilies[$lily]['lily:grade'][0]) : '' }}
                        </div>
                    </div>
                </a>
            @empty
                <p class="center notice">該当するリリィの情報がありません</p>
            @endforelse
        </div>

        <div class="window-a" id="notice">
            <div class="header">ご注意</div>
            <div class="body">
                <h3>書影・書誌データについて</h3>
                <p>
                    当サイトの書籍の書影は
                    <a href="https://openbd.jp" target="_blank">openBDプロジェクト</a>
                    のデータを利用しています。<br>
                    この書影データはISBNを基に問い合わせを行っており、ISBNがない一部の書籍は書影を利用できません。
                </p>
                <p>
                    書誌データについては LuciaDB のデータをそのまま用いており、
                    openBD及び版元ドットコム等のデータと差異がある場合があります。
                </p>
                <h3>登場するリリィについて</h3>
                <p>
                    登場するリリィのデータは LuciaDB にデータのあるリリィのみ表示しています。
                    データ登録がないなどの理由で全ての登場人物をカバーできていない場合があります。
                </p>
            </div>
        </div>
    </main>
@endsection
