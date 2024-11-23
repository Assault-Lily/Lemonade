@extends('app.layout',['title' => 'Error '.$exception->getStatusCode(), 'pagetype' => 'back-triangle'])
<?php
/**
 * @var $exception \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
 */
$sc = $exception->getStatusCode();
$status = [
    400 => 'Bad Request',
    403 => 'Forbidden',
    404 => 'Not Found',
    500 => 'Internal Server Error',
    503 => 'Service Unavailable',
];
$messages = [
    400 => "要求の形式が正しくありません",
    403 => "このページにアクセスする権限がありません",
    404 => "お探しのページは存在しません\nURLなどをお確かめの上やり直してください",
    500 => "サーバ内部でエラーが発生しました\n管理者までお知らせください",
    503 => "アクセス集中かメンテナンスのためアクセスできません\nしばらく待ってからやり直してください",
];
$message = $exception->getMessage() && $sc !== 500 ? $exception->getMessage() : $messages[$sc] ?? '何かがおかしいようです';
?>

@section('main')
    <div style="margin: 25vh auto;width: fit-content">
        <h1 style="font-size: 40px; color: #2d3748">{{ 'Error '.$sc }}</h1>
        <p>{!! nl2br($message) !!}</p>
        @if((int)($sc / 100) === 5)
            <div class="buttons" style="margin-top: 30px;">
                <a href="javascript:location.reload()" class="button primary">リロード</a>
                <a href="https://x.com/{{ config('lemonade.developer.twitter') }}" class="button" target="_blank">管理者に連絡</a>
            </div>
        @endif
    </div>
@endsection
