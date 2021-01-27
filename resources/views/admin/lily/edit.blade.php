@extends('app.layout',['title' => 'リリィデータ編集'])

<?php
    /**
     * @var $lily \App\Models\Lily
     */
?>

@section('head')
    <style>
        #name, #more{
            display: flex;
            justify-content: space-around;
            margin: 10px 0;
        }
        #more{
            width: 500px;
            margin: 10px auto;
            justify-content: space-around;
        }
        #name input[name^="name"]{
            width: 350px;
            font-size: large;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>データ編集</h1>
        <div class="white-box" style="text-align: center">
            <form action="{{ route('admin.lily.update',['lily' => $lily->id]) }}" method="post">
                @csrf
                @method('patch')
                <input type="hidden" name="id" value="{{ $lily->id }}">
                <div id="name">
                    <label>
                        名前<br>
                        <input type="text" name="name" required value="{{ old('name', $lily->name) }}">
                    </label>
                    <label>
                        名前の読み (ひらがな or カタカナ)<br>
                        <input type="text" name="name_y" required value="{{ old('name_y', $lily->name_y) }}">
                    </label>
                    <label>
                        アルファベット<br>
                        <input type="text" name="name_a" required value="{{ old('name_a', $lily->name_a) }}">
                    </label>
                </div>
                <div id="more">
                    <label>
                        スラッグ<br>
                        <input type="text" name="slug" required value="{{ old('slug', $lily->slug) }}" readonly id="slug">
                    </label>
                    <label>
                        カラーコード<br>
                        <input type="text" name="color" maxlength="7" value="{{ old('color', $lily->color) }}">
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
                    スラッグはリリィ詳細画面のURIパスとして既に機能しています。
                    既存ページにアクセスできなくなるため、よほどのことがない限り変更されるべきではありません。<br>
                    <a href="javascript:document.getElementById('slug').removeAttribute('readonly')">
                        理解した上で、編集ロックを解除します。
                    </a>
                </p>
                <p>カラーコードは必須入力ではありません。</p>
                <hr>
                <div class="buttons">
                    <a href="{{ route('admin.lily.show',['lily' => $lily->id]) }}" class="button">キャンセル</a>
                    <input type="reset" class="button" value="初期化">
                    <input type="submit" class="button primary" value="登録実行">
                </div>
            </form>

        </div>
    </main>
@endsection
