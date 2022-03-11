@extends('app.layout',['title' => 'トリプル参照・編集'])

<?php
    /**
     * @var $triple \App\Models\Triple
     */
?>

@section('head')
    <style>
        #triple{
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }
        #triple input{
            display: block;
            width: 350px;
            font-size: large;
        }
    </style>
@endsection

@section('main')
    <main>
        @if(!$triple->trashed())
            <h1>トリプル参照・編集</h1>
            <div class="white-box" style="text-align: center">
                <p>
                    このトリプルは 主語 <span style="font-size: large; font-weight: bold">{{ $triple->lily_slug }}</span> に
                    述語 <span style="font-size: large; font-weight: bold">{{ config('triplePredicate.'.$triple->predicate) }}</span>
                    として紐付けられています。
                </p>
                <p>作成 : {{ $triple->created_at }}<br>更新 : {{ $triple->updated_at }}</p>
                <div class="buttons three">
                    <a href="{{ route('admin.triple.index',['lily_slug' => $triple->lily_slug]) }}" class="button">トリプル絞り込み参照</a>
                    <a href="{{ route('admin.triple.create',['lily_slug' => $triple->lily_slug]) }}" class="button">編集ではなく新規作成する</a>
                    <a href="{{ route('admin.triple.create',['predicate' => $triple->predicate, 'object' => $triple->object]) }}" class="button">このトリプルを別のリリィにコピーする</a>
                </div>
                <hr>
                <form action="{{ route('admin.triple.update',['triple' => $triple->id]) }}" method="post">
                    @csrf
                    @method('patch')
                    <div id="triple">
                        <label>
                            主語(リリィID)
                            <input type="text" name="lily_slug" required value="{{ $triple->lily_slug }}" readonly>
                        </label>
                        <label>
                            述語
                            <input type="text" name="predicate" list="predicates" required
                                   value="{{ $triple->predicate }}" readonly id="predicate">
                            <datalist id="predicates">
                                @foreach($predicates as $key => $predicate)
                                    <option value="{{ $key }}">{{ $predicate }}</option>
                                @endforeach
                            </datalist>
                        </label>
                        <label>
                            目的語(オブジェクト)
                            <input type="text" name="object" required value="{{ old('object', $triple->object) }}">
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
                        データ管理上の観点から、不要になったトリプルは再利用せず、削除してください。<br>
                        (削除したトリプルは、システム内部では論理的に削除されレストアが可能です。)<br>
                        述語の記述子を間違えた場合などを除いて、述語を変更するべきではありません。<br>
                        <a href="javascript:document.getElementById('predicate').removeAttribute('readonly')" target="_self">
                            理解した上で、編集ロックを解除します。
                        </a>
                    </p>
                    <hr>
                    <div class="buttons">
                        <a href="{{ route('admin.triple.index',['lily_slug' => $triple->lily_slug]) }}" class="button">一覧に戻る</a>
                        <a href="{{ route('admin.triple.index') }}" class="button">全一覧に戻る</a>
                        <input type="reset" class="button" value="初期化">
                        <input type="submit" class="button primary" value="更新実行">
                    </div>
                </form>
            </div>
            <h2>トリプルの削除</h2>
            <div class="white-box" style="text-align: center">
                <p>
                    トリプルの情報が無効であるなら、削除することが出来ます。<br>
                    トリプルは論理削除されるため、また必要になったときにはレストアできます。
                </p>
                <hr>
                <form action="{{ route('admin.triple.destroy', ['triple' => $triple->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <div class="buttons">
                        <input type="submit" value="削除実行" class="button" style="color: darkred"
                               onclick="return confirm('削除を実行します。よろしいですか？')">
                    </div>
                </form>
            </div>
        @else
            <h1>トリプルのレストア</h1>
            <div class="white-box" style="text-align: center">
                <p>
                    このトリプルは論理削除されています。<br>
                    復元するとトリプルの再利用ができるようになります。
                </p>
                <hr>
                <p style="font-size: 20px">
                    {{ $triple->lily->name }} ->
                    {{ config('triplePredicate.'.$triple->predicate).'('.$triple->predicate.')' }} ->
                    {{ $triple->object }}
                </p>
                <p>
                <p>作成 : {{ $triple->created_at }}<br>削除 : {{ $triple->deleted_at }}</p>
                <hr>
                <form action="{{ route('admin.triple.update',['triple' => $triple->id]) }}" method="post">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="restore" value="true">
                    <div class="buttons">
                        <input type="submit" value="レストア実行" class="button">
                    </div>
                </form>
            </div>
        @endif
    </main>
@endsection
