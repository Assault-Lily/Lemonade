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
                    このトリプルは <span style="font-size: large; font-weight: bold">{{ $args['lily'] }}</span> に紐付けられます。
                </p>
            @else
                <p>
                    トリプルは指定されたスラッグを基に紐付けられます。<br>
                    先にリリィのデータがassaultlily-rdf上に存在するか、あるいは独自リリィとして存在するか確認してください。</p>
            @endif
            <hr>
            <form action="{{ route('admin.triple.store') }}" method="post">
                @csrf
                <div id="triple">
                    <label>
                        主語(リリィスラッグ)
                        <input type="text" name="lily" list="lilies" required
                               @if(!empty($args['lily']))value="{{ $args['lily'] }}" readonly @else value="{{ old('lily') }}" @endif>
                        <datalist id="lilies">
                            @foreach($lilies as $key => $lily)
                                <option value="{{ $key }}">{{ $lily }}</option>
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
