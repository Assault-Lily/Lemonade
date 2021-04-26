@extends('app.layout',['title' => '管理コンソール'])

@section('main')
    <main>
        <h2>サーバ情報</h2>
        <div class="white-box">
            <table style="margin: .4em auto 0;border-spacing: 1em 0">
                <tr>
                    <th>ホスト</th>
                    <td>{{ $_SERVER['HTTP_HOST'] }}</td>
                </tr>
                <tr>
                    <th>アプリ名</th>
                    <td>{{ config('app.name') }}</td>
                </tr>
                <tr>
                    <th>バージョン</th>
                    <td>{{ 'Ver'.config('lemonade.version') }}</td>
                </tr>
                <tr>
                    <th>環境</th>
                    <td>{{ config('app.env') }}</td>
                </tr>
                <tr>
                    <th>マシン情報</th>
                    <td>{{ php_uname() }}</td>
                </tr>
                <tr>
                    <th>PHPバージョン</th>
                    <td>{{ phpversion() }}</td>
                </tr>
                <tr>
                    <th>DBサーバ</th>
                    <td>{{ config('database.connections.'.config('database.default','mysql').'.host') }}</td>
                </tr>
                <tr>
                    <th>SPARQLエンドポイント</th>
                    <td>{{ config('lemonade.sparqlEndpoint') }}</td>
                </tr>
            </table>
        </div>
        <h2>アカウント操作</h2>
        <p style="text-align: center">
            <strong>{{ Auth::user()->name.' ('.Auth::user()->email.')' }}</strong> としてログインしています
        </p>
        <div class="buttons three">
            <a href="{{ route('admin.logout') }}" class="button"
               onclick="event.preventDefault();document.getElementById('logout').submit();">ログアウト</a>
            <a href="{{ route('admin.password.change') }}" class="button">パスワード変更</a>
        </div>
        <form action="{{ route('admin.logout') }}" method="post" style="display: none" id="logout">
            @csrf
        </form>
        <h2>RDF同期管理</h2>
        <div class="buttons three">
            <a href="{{ route('admin.rdf.index') }}" class="button">RDF同期管理</a>
        </div>
        <h2>リリィデータ管理</h2>
        <div class="buttons three">
            <a href="{{ route('admin.lily.index') }}" class="button">データ一覧</a>
            <a href="{{ route('admin.lily.create') }}" class="button">新規登録</a>
{{--            <a href="javascript:void(0)" class="button">データ整合チェック</a>--}}
        </div>
        <h2>独自トリプル管理</h2>
        <div class="buttons three">
            <a href="{{ route('admin.triple.index') }}" class="button">データ一覧</a>
            <a href="{{ route('admin.triple.create') }}" class="button">新規登録</a>
{{--            <a href="javascript:void(0)" class="button">データ整合チェック</a>--}}
        </div>
        <h2>画像データ管理</h2>
        <div class="buttons three">
            <a href="{{ route('admin.image.index') }}" class="button">データ一覧</a>
            <a href="{{ route('admin.image.create') }}" class="button">新規登録</a>
        </div>
    </main>
@endsection
