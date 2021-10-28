@extends('app.layout',['title' => '画像一括登録'])

<?php
/**
 * @var $default array
 */
?>

@section('main')
    <main>
        <h1>新規一括登録</h1>
        <div class="white-box" style="text-align: center">
            <form action="{{ route('admin.image.storeJson') }}" method="post">
                @csrf
                <p>
                    オブジェクトの配列形式のJSONを入力し登録実行をクリックしてください。
                </p>
                <hr>
                <div>
                    <label>
                        JSON<br>
                        <textarea style="width: 100%; height: 300px; text-align: left; resize: vertical;" name="json" placeholder="[{JSON: 'value'}]">{{ old('json') }}</textarea>
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
                    このフォームは整合性チェックを行わず、このJSONのまま挿入されます。<br>
                    基本的には既存環境からのインポート以外に使用するべきではありません。
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
