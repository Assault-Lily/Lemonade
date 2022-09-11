<?php
use App\Models\Notice;

$ogp['title'] = $page_info['type'] ?? 'お知らせ一覧';
$ogp['description'] = config('app.name', 'Lemonade').'からのお知らせの一覧です。';
?>

@extends('app.layout', ['title' => $page_info['type'] ?? 'お知らせ一覧', 'ogp' => $ogp])

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
                        @if(($page_info['type'] ?? null) !== '規約・方針')
                            {{ $notice->updated_at->format('Y年 n月j日 H:i') }}
                        @endif
                        <div class="tag">{{ config('noticeCategories.'.$notice->category) ?? '未分類' }}</div>
                        @if($notice->importance === 100)
                            <div class="tag">重要</div>
                        @endif
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
