@extends('app.layout',['title' => '画像新規登録'])

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
        <h1>新規登録</h1>
        <div class="white-box" style="text-align: center">
            <form action="{{ route('admin.image.store') }}" method="post">
                @csrf
                <p>
                    追加情報以外はすべて必須の情報です。
                </p>
                <hr>
                <div id="main">
                    <label>
                        対象リソース<br>
                        <input type="text" name="for" required placeholder="Hitotsuyanagi_Riri" value="{{ old('for') }}">
                    </label>
                    <label>
                        画像の種別<br>
                        <input type="text" name="type" required placeholder="icon" value="{{ old('type') }}">
                    </label>
                    <label>
                        作者<br>
                        <input type="text" name="author" required placeholder="K Miyano" value="{{ old('author') }}">
                    </label>
                </div>
                <div id="more">
                    <label>
                        画像URL<br>
                        <input type="text" name="image_url" required placeholder="https://image.of.lily/path/to/image.jpg"
                               value="{{ old('image_url') }}" style="width: 600px">
                    </label><hr>
                    <label>
                        追加情報<br>
                        <textarea style="width: 600px; height: 300px" name="author_info"
                                  placeholder="pixiv,miyacorata&#x0A;twitter,miyacorata&#x0A;(CSV形式)">{{ old('image_url') }}</textarea>
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
