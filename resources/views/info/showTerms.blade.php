<?php
use App\Models\Notice;

/**
 * @var $notice Notice
 */

$ogp['title'] = $notice->title;
$ogp['description'] = config('app.name', 'Lemonade').'の「'.$notice->title.'」のページです。';
?>

@extends('app.layout', ['title' => '規約・方針', 'titlebar' => $notice->title, 'ogp' => $ogp])

@section('main')
    <main>
        <div class="top-options">
            <div>
                <a href="{{ route('info.index', ['list' => 'terms']) }}" class="button smaller">
                    <i class="fas fa-arrow-left"></i>
                    規約・方針の一覧
                </a>
            </div>
            <div>
                <div class="tag">{{ config('noticeCategories.'.$notice->category) ?? '未分類' }}</div>
            </div>
        </div>
        <div class="white-box">
            <h1>{{ $notice->title }}</h1>
            <hr>
            {!! \Illuminate\Mail\Markdown::parse(base64_decode($notice->body)) !!}
        </div>

    </main>
@endsection
