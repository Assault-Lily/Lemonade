@extends('app.layout',['title' => '画像新規登録'])

<?php
/**
 * @var $default array
 */
?>

@section('main')
    <main>
        <h1>新規一括登録</h1>
        <div class="white-box" style="text-align: center">
            <form action="{{ route('admin.image.store') }}" method="post">
                @csrf
                <p>
                    オブジェクトの配列形式のJSONを入力し登録実行をクリックしてください。
                </p>
                <hr>
                <div>
                    <label>
                        JSON<br>
                        <textarea style="width: 100%; height: 300px; text-align: left; resize: vertical;" name="json"
                                  placeholder="pixiv,miyacorata&#x0A;twitter,miyacorata&#x0A;(CSV形式)">{{ old('json') }}</textarea>
                    </label>
                </div>
                @if($errors->any())
                    <hr>
                    <div style="color: darkred">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <hr>
                <p>
                    Lemonadeは画像データそのものは管理しません。<br>
                    その代わり、画像の作者、権利者が持つサーバへの直接リンクを登録することができます。
                </p>
                <hr>
                <div class="buttons">
                    <a href="{{ route('admin.image.index') }}" class="button">一覧に戻る</a>
                    <input type="reset" class="button" value="初期化">
                    <input type="submit" class="button primary" value="登録実行">
                </div>
            </form>

        </div>
    </main>
@endsection
