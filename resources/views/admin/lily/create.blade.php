@extends('app.layout',['title' => 'リリィ新規登録'])

@section('head')
    <style>
        #name, #more{
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }
        #more{
            width: 500px;
            margin: 0 auto 10px;
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
        <h1>新規登録</h1>
        <div class="white-box" style="text-align: center">
            <form action="{{ route('admin.lily.store') }}" method="post">
                @csrf
                <p>
                    基本データのみを登録します。詳細な情報は基本データを追加後、トリプルを追加します。
                </p>
                <hr>
                <div id="name">
                    <label>
                        名前<br>
                        <input type="text" name="name" required placeholder="一柳 梨璃" value="{{ old('name') }}">
                    </label>
                    <label>
                        名前の読み (ひらがな or カタカナ)<br>
                        <input type="text" name="name_y" required placeholder="ひとつやなぎ りり" value="{{ old('name_y') }}">
                    </label>
                    <label>
                        アルファベット<br>
                        <input type="text" name="name_a" required placeholder="Hitotsuyanagi Riri" value="{{ old('name_a') }}">
                    </label>
                </div>
                <div id="more">
                    <label>
                        スラッグ<br>
                        <input type="text" name="slug" required placeholder="Hitotsuyanagi_Riri" value="{{ old('slug') }}">
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
                    スラッグはリリィ詳細画面のURIパスとなります。<br>
                    特に事情がない限り、名前のアルファベット表記のうちスペースをアンダーバーに置換した文字列が指定されるべきです。
                </p>
                <p>
                    assaultlily-rdfからの同期時にリリィ基本データは一度TRUNCATEされます。
                    独自に追加したリリィのデータについてはバックアップを取るべきです。
                </p>
                <hr>
                <div class="buttons">
                    <a href="{{ route('admin.lily.index') }}" class="button">一覧に戻る</a>
                    <input type="reset" class="button" value="初期化">
                    <input type="submit" class="button primary" value="登録実行">
                </div>
            </form>

        </div>
    </main>
@endsection
