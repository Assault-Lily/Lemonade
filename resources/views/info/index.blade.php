<?php
use App\Models\Notice;

$ogp['title'] = 'お知らせ一覧';
$ogp['description'] = config('app.name', 'Lemonade').'からのお知らせの一覧です。';
?>

@extends('app.layout', ['title' => 'お知らせ一覧', 'ogp' => $ogp])

@section('main')
    <main>
        <div class="top-options">
            <div>
                <span>更新が新しい順</span>
            </div>
            <div>
                <span class="info">登録数 : {{ count($notices) }}</span>
            </div>
        </div>
        <div>
            @forelse($notices as $notice)
                <?php /** @var $notice Notice */ ?>
                <a href="{{ route('info.show', ['info' => $notice->slug]) }}" class="list-item-b">
                    <div class="title">
                        {{ $notice->title }}
                    </div>
                    <div>
                        {{ $notice->updated_at->format('Y年 n月j日 H:i') }}
                        <div class="tag">{{ $notice->category ?? '未分類' }}</div>
                    </div>
                </a>
            @empty
                <p class="center notice">
                    該当レコードがありません
                </p>
            @endforelse
        </div>
    </main>
@endsection
