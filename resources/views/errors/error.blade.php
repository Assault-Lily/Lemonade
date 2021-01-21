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
$message = $exception->getMessage() ?: $messages[$sc] ?? '何かがおかしいようです';
?>

@section('main')
    <div style="margin: 20vh auto 0;width: fit-content">
        <h1 style="font-size: 40px; color: #2d3748">{{ 'Error '.$sc }}</h1>
        <p>{!! nl2br($message) !!}</p>
    </div>
@endsection
