@extends('app.layout',['title' => '画像情報一括更新', 'previous' => route('admin.image.index')])

<?php
    /**
     * @var $images \App\Models\Image
     */
?>

@section('head')
    <style>
        #main{
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }
        #main input{
            width: 350px;
            font-size: large;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>一括更新</h1>
        <div class="white-box" style="text-align: center">
            <p>
                作者が <strong>{{ $author }}</strong> の画像データを選択しています。<br>
                このページでの変更により {{ $images->count() }}件 のレコードが更新されます。
            </p>
            <p>
                影響するレコード件数が多ければ多いほど処理に時間がかかります。
            </p>
            <hr>
            <form action="{{ route('admin.image.bulkUpdateExec') }}"
                  onsubmit="return confirm('入力されたデータで上書きを実行します。続けますか？')" method="post">
                @csrf
                @method('patch')
                <input type="hidden" name="oldAuthor" value="{{ $author }}">
                <div id="main">
                    <label>
                        作者<br>
                        <input type="text" name="author" required value="{{ old('author', $author) }}">
                    </label>
                </div>
                <div id="more">
                    <label>
                        追加情報<br>
                        <textarea style="width: 600px; height: 300px" name="author_info"
                                  placeholder="pixiv,miyacorata&#x0A;twitter,miyacorata&#x0A;(CSV形式)"></textarea>
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
                <div class="buttons">
                    <a href="{{ route('admin.image.index') }}" class="button">一覧に戻る</a>
                    <input type="reset" class="button" value="初期化">
                    <input type="submit" class="button primary" value="更新実行">
                </div>
            </form>
        </div>
        {{--}}<h2>一括削除</h2> TODO: 生やす
        <div class="white-box" style="text-align: center">
            <p>
                画像レコードを削除する場合はここから削除できます。<br>
                画像に関する記録のみ削除され、画像ファイルそのものが削除されるわけではありません。
            </p>
            <p class="notice">
                この操作は取り消しできません。
            </p>
            <hr>
            <div class="buttons">
                <form action="{{ route('admin.image.bulkUpdate', ['image' => $author]) }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="submit" class="button" value="削除実行" style="color: darkred">
                </form>
            </div>
        </div>{{--}}
    </main>
@endsection
