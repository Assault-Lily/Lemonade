@extends('app.layout', ['title' => 'お知らせ新規作成'])

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
        <h1>新規作成</h1>
        <div class="white-box" style="text-align: center">
            <p>
                スラッグはUniqueです。本文はHTMLを使用できます。
            </p>
            <p>
                重要度を100にするとトップページに固定されます。ウェルカムメッセージやメンテナンス告知向けです。<br>
                今のところ、1と100以外には意味がないです。
            </p>
            <hr>
            <form action="{{ route('admin.notice.store') }}" method="post">
                @csrf
                <div id="main">
                    <label>
                        スラッグ<br>
                        <input type="text" name="slug" placeholder="TitleSlug" value="{{ old('slug', Str::uuid()) }}">
                    </label>
                    <label>
                        タイトル<br>
                        <input type="text" name="title" required placeholder="タイトル" value="{{ old('title') }}">
                    </label>
                    <label>
                        重要度<br>
                        <input type="number" min="0" max="100" name="importance" required placeholder="1" value="{{ old('importance',1) }}">
                    </label>
                </div>
                <div>
                    <label>
                        本文
                        <textarea name="body" required style="width: 100%; height: 200px; text-align: left">{{ old('body') }}</textarea>
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
                    <button type="reset" class="button">初期化</button>
                    <button type="submit" class="button primary">登録</button>
                </div>
            </form>
        </div>
    </main>
@endsection
