@extends('app.layout',['title' => 'トリプル新規登録'])

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
        <h1>トリプル新規登録</h1>
        <div class="white-box" style="text-align: center">
            @if(!empty($args['lily']))
                <p>
                    リリィIDが既に指定されています。
                    このトリプルは <span style="font-size: large; font-weight: bold">{{ $args['lily']->name }}</span> に紐付けられます。
                </p>
            @else
                <p>トリプルはリリィ基本データに紐付けられます。先にリリィ基本データがデータベースに存在する必要があります。</p>
            @endif
            <div class="buttons three">
                <a href="{{ route('admin.lily.index') }}" class="button">リリィ一覧</a>
            </div>
            <hr>
            <form action="{{ route('admin.triple.store') }}" method="post">
                @csrf
                <div id="triple">
                    <label>
                        主語(リリィスラッグ)
                        <input type="text" name="lily" list="lilies" required
                               @if(!empty($args['lily']))value="{{ $args['lily']->id }}" readonly @else value="{{ old('lily') }}" @endif>
                        <datalist id="lilies">
                            <?php
                            /**
                             * @var $lily \App\Models\Lily
                             */
                            ?>
                            @foreach($lilies as $lily)
                                <option value="{{ $lily->slug }}">{{ $lily->name }}</option>
                            @endforeach
                        </datalist>
                    </label>
                    <label>
                        述語
                        <input type="text" name="predicate" list="predicates" required value="{{ old('predicate', $args['predicate'] ?? '') }}">
                        <datalist id="predicates">
                            @foreach($predicates as $key => $predicate)
                                <option value="{{ $key }}">{{ $predicate }}</option>
                            @endforeach
                        </datalist>
                    </label>
                    <label>
                        目的語(オブジェクト)
                        <input type="text" name="object" required value="{{ old('object', $args['object'] ?? '') }}">
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
                    <a href="{{ url()->previous() }}" class="button">キャンセル</a>
                    <input type="reset" class="button" value="初期化">
                    <input type="submit" class="button primary" value="登録実行">
                </div>
            </form>
        </div>
    </main>
@endsection
