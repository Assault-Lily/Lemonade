<?php ?>
@extends('app.layout', ['title' => '書籍一覧'])

@section('head')
    <style>
        .tag{
            position: relative;
            display: inline-block;
            font-size: smaller;
            color: white;
            box-shadow: inset 0 0 3px mediumpurple;
            background: #bca3ef;
            padding: 1px 10px;
            margin: 0 5px;
            border-bottom-right-radius: 5px;
        }
        .tag:before{
            position: absolute;
            top: 0;
            left: 0;
            content: '';
            width: 10px;
            height: 4px;
            background: mediumpurple;
        }
    </style>
@endsection

@section('main')
    <main>
        <div class="top-options">
            <div>
                ならべかえ : デフォルト
            </div>
            <div>
                <span class="info">書籍登録数 : {{ count($books) }}</span>
            </div>
        </div>
        @forelse($books as $bookKey => $book)
            <a class="book list-item-b" href="{{ route('book.show', ['book' => str_replace('lilyrdf:','',$bookKey)]) }}">
                <div class="title">
                    {{ $book['schema:name'][0] }}
                    @if(!empty($book['lily:genre'][0]))<span class="tag">{{ $book['lily:genre'][0] }}</span>@endif
                </div>
                <div style="margin: .5em 0">
                    <span class="tag">著者</span> {{ implode(', ',$book['schema:author'] ?? array()) }}
                    @if(!empty($book['lily:originalAuthor']))
                        <span class="tag">原作</span> {{ implode(', ',$book['lily:originalAuthor'] ?? array()) }}
                    @endif
                    @if(!empty($book['schema:illustrator']))
                        <span class="tag">イラスト</span> {{ implode(', ',$book['schema:illustrator'] ?? array()) }}
                    @endif
                </div>
                <div>
                    <span class="underline">発行日 :
                        {{ convertDateString($book['schema:datePublished'][0])->format('Y年n月j日') }}</span>
                </div>
            </a>
        @empty
        @endforelse
    </main>
@endsection
