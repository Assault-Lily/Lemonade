@extends('app.layout',['title' => '管理コンソール'])

@section('main')
    <main>
        <h2>アカウント操作</h2>
        <div class="buttons three">
            <a href="{{ route('admin.logout') }}" class="button"
               onclick="event.preventDefault();document.getElementById('logout').submit();">ログアウト</a>
            <a href="{{ route('admin.dashboard') }}" class="button">パスワード変更</a>
        </div>
        <form action="{{ route('admin.logout') }}" method="post" style="display: none" id="logout">
            @csrf
        </form>
    </main>
@endsection
