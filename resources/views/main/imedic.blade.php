<?php
$ogp['title'] = "IME辞書生成";
$ogp['description'] = "アサルトリリィのIME辞書を生成します。Google日本語入力他様々なIMEでご利用いただけます。";
?>

@extends('app.layout', ['title' => 'IME辞書生成', 'previous' => url('/menu'), 'ogp' => $ogp])

@section('main')
    <main>
        <h1>IME辞書ダウンロード & 生成結果</h1>
        <div class="white-box">
            <p class="center">
                この辞書はGoogle日本語入力、及びGoogle日本語入力のフォーマットを受け入れるIME、MacOS日本語入力プログラムで利用可能です。
            </p>
            <p class="center">
                Google日本語入力、
                MicrosoftIMEをご利用の場合はShift-JIS版を、MacOSの日本語入力プログラムをご利用の場合はPropertyList版をご利用ください。<br>
                インポートの方法は各IMEのヘルプをご確認ください。
            </p>
            <div class="buttons three">
                <a href="{{ url()->current().'?format=txt' }}" download="assaultlily-dic.txt"
                   class="button primary" target="_self">ダウンロード</a>
                <a href="{{ url()->current().'?format=txt-sjis' }}" download="assaultlily-dic.txt"
                   class="button" target="_self">ダウンロード (Shift-JIS版)</a>
                {{--<a href="{{ url()->current().'?format=zip' }}" download="assaultlily-dic.zip"
                   class="button" target="_self">ダウンロード (Gboard向けZIP版)</a>--}}
                <a href="{{ url()->current().'?format=plist' }}" download="assaultlily-dic.plist"
                   class="button" target="_self">ダウンロード (PropertyList版)</a>
            </div>
            <hr>
            <p class="center">
                以下は辞書用に生成されたテキストです。<br>
                編集してから利用したい場合、こちらのテキストをご利用いただくこともできます。クリックすると全選択します。
            </p>
            <label>
                <textarea onclick="this.select()" style="width: 100%;height: 500px;text-align: left;">{{ $dicString }}</textarea>
            </label>
        </div>
    </main>
@endsection
