<?php
use App\Models\Notice;

/**
 * @var $notice Notice
 */

$ogp['title'] = $notice->title;
$ogp['description'] = config('app.name', 'Lemonade').'からのお知らせです。';
?>

@extends('app.layout', ['title' => 'お知らせ詳細', 'titlebar' => 'お知らせ : '.$notice->title, 'ogp' => $ogp])

@section('main')
    <main>
        <div class="top-options">
            <div>
                <a href="{{ route('info.index') }}" class="button smaller">
                    <i class="fas fa-arrow-left"></i>
                    お知らせ一覧
                </a>
            </div>
            <div>
                <div class="tag">{{ config('noticeCategories.'.$notice->category) ?? '未分類' }}</div>
                <span class="info">登録日時 : {{ $notice->created_at->format('Y年 n月j日 H:i') }}</span>
            </div>
        </div>
        <div class="white-box">
            <h1>{{ $notice->title }}</h1>
            <hr>
            {!! \Illuminate\Mail\Markdown::parse(base64_decode($notice->body)) !!}
            <hr>
            <div style="font-size: small">
                <span>登録 : {{ $notice->created_at->format('Y年 n月j日 H:i') }}</span>
                <span>更新 : {{ $notice->updated_at->format('Y年 n月j日 H:i') }}</span>
            </div>
        </div>

    </main>
@endsection
