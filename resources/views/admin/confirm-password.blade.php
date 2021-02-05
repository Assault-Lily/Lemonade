@extends('app.layout',['title' => '追加認証', 'pagetype' => 'back-triangle'])

@section('head')
    <style>
        #auth{
            width: 520px;
            margin: 100px auto;
        }
        label{
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        input[type="email"], input[type="password"]{
            width: 300px;
        }
    </style>
@endsection

@section('main')
    <main>
        <div class="window-a" id="auth">
            <div class="header">認証</div>
            <div class="body">
                <p style="text-align: center">操作を続行するためにパスワードの確認が必要です</p>
                <form action="{{ route('admin.login') }}" method="post">
                    @csrf
                    <label>
                        <span>パスワード</span>
                        <input id="password" type="password" name="password" required
                               placeholder="Password" autocomplete="current-password">
                    </label>
                    <hr>
                    @if($errors->any())
                        <div style="color: darkred;text-align: center">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                        <hr>
                    @endif
                    <div class="buttons">
                        <a href="javascript:history.back()" class="button">キャンセル</a>
                        <button type="submit" class="primary">確認</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
