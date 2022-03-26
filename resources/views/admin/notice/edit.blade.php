@extends('app.layout', ['title' => 'お知らせ参照・編集', 'previous' => ['route' => route('admin.notice.index')]])

<?php
    /** @var $notice \App\Models\Notice */
?>

@section('head')
    <style>
        #main{
            display: flex;
            justify-content: space-around;
            margin: 10px 0;
        }
        #main input{
            width: 350px;
            font-size: large;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>お知らせ詳細・編集</h1>
        <div class="white-box" style="text-align: center">
            <p>作成 : {{ $notice->created_at }}<br>更新 : {{ $notice->updated_at }}</p>
            <hr>
            <form action="{{ route('admin.notice.update', ['notice' => $notice->id]) }}" method="post">
                @csrf
                @method('patch')
                <input type="hidden" name="id" value="{{ $notice->id }}">
                <div>
                    <label>
                        タイトル<br>
                        <input type="text" name="title" required placeholder="タイトル" value="{{ old('title', $notice->title) }}" style="font-size: large; width: 500px">
                    </label>
                </div>
                <div id="main">
                    <label>
                        スラッグ<br>
                        <input type="text" name="slug" placeholder="TitleSlug" value="{{ old('slug', $notice->slug) }}">
                    </label>
                    <label>
                        カテゴリ<br>
                        <input type="text" name="category" placeholder="Category" list="categories" value="{{ old('category', $notice->category) }}">
                        <datalist id="categories">
                            @foreach(config('noticeCategories') as $key => $val)
                                <option value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </datalist>
                    </label>
                    <label>
                        重要度<br>
                        <input type="number" min="0" max="100" name="importance" required placeholder="1" value="{{ old('importance', $notice->importance) }}">
                    </label>
                </div>
                <div>
                    <label>
                        本文
                        <textarea name="body" required style="width: 100%; height: 200px; text-align: left">{{ old('body', base64_decode($notice->body)) }}</textarea>
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
        <h2>お知らせの削除</h2>
        <div class="white-box" style="text-align: center">
            <p>
                お知らせが不要である場合、削除することが出来ます。<br>
                レストアはそのうち実装します。
            </p>
            <hr>
            <form action="{{ route('admin.notice.destroy', ['notice' => $notice->id]) }}" method="post">
                @csrf
                @method('delete')
                <div class="buttons">
                    <input type="submit" value="削除実行" class="button" style="color: darkred"
                           onclick="return confirm('削除を実行します。よろしいですか？')">
                </div>
            </form>
        </div>
    </main>
@endsection
