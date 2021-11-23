@extends('app.layout',['title' => '画像データ参照・編集', 'previous' => route('admin.image.index')])

<?php
    /**
     * @var $image \App\Models\Image
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
        <h1>画像情報</h1>
        <div class="white-box" style="text-align: center">
            <p>
                この画像の内部IDは <strong>{{ $image->id }}</strong> です。以下に画像のプレビューを表示しています。
            </p>
            <img src="{{ $image->image_url }}" alt="image" style="width: auto; height: 100px; border: solid 1px gray">
            <p>
                登録日時 : {{ $image->created_at->format('Y-m-d H:i:s') }}<br>
                更新日時 : {{ $image->updated_at->format('Y-m-d H:i:s') }}
            </p>
            <hr>
            <form action="{{ route('admin.image.update', ['image' => $image->id]) }}"
                  onsubmit="return confirm('画像データの上書きをしようとしています。続けますか？')" method="post">
                @csrf
                @method('patch')
                <input type="hidden" name="id" value="{{ $image->id }}">
                <div id="main">
                    <label>
                        対象リソース<br>
                        <input type="text" name="for" required value="{{ old('for', $image->for) }}">
                    </label>
                    <label>
                        画像の種別<br>
                        <input type="text" name="type" required value="{{ old('type', $image->type) }}">
                    </label>
                    <label>
                        作者<br>
                        <input type="text" name="author" required value="{{ old('author', $image->author) }}">
                    </label>
                </div>
                <div id="more">
                    <label>
                        画像URL<br>
                        <input type="text" name="image_url" required placeholder="https://image.of.lily/path/to/image.jpg"
                               value="{{ old('image_url', $image->image_url) }}" style="width: 600px">
                    </label><hr>
                    <label>
                        追加情報<br>
                        <textarea style="width: 600px; height: 300px" name="author_info"
                                  placeholder="pixiv,miyacorata&#x0A;twitter,miyacorata&#x0A;(CSV形式)">{{ old('image_url', $image->author_info) }}</textarea>
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
                    <?php
                    $parameter = [
                        'for' => $image->for,
                        'type' => $image->type,
                        'author' => $image->author,
                        'image_url' => $image->image_url,
                        'author_info' => $image->author_info
                    ]
                    ?>
                    <a href="{{ route('admin.image.create', $parameter) }}" class="button">複製</a>
                    <a href="{{ route('admin.image.bulkUpdate', ['author' => $image->author]) }}" class="button">一括書換</a>
                    <input type="reset" class="button" value="初期化">
                    <input type="submit" class="button primary" value="更新実行">
                </div>
            </form>
        </div>
        <h2>画像レコード削除</h2>
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
                <form action="{{ route('admin.image.destroy', ['image' => $image->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="submit" class="button" value="削除実行" style="color: darkred">
                </form>
            </div>
        </div>
    </main>
@endsection
