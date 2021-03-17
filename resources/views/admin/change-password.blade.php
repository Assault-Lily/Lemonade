@extends('app.layout',[
    'title' => 'パスワード変更', 'pagetype' => 'back-triangle',
    'previous' => route('admin.dashboard')])

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
            <div class="header">パスワード変更</div>
            <div class="body">
                <form action="{{ route('admin.password.change') }}" method="post">
                    @method('patch')
                    @csrf
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <label>
                        <span>現在のパスワード</span>
                        <input id="password" type="password" name="current_password" required
                               placeholder="CurrentPassword" autocomplete="current-password">
                    </label>
                    <label>
                        <span>新しいパスワード</span>
                        <input id="password" type="password" name="new_password" required
                               placeholder="NewPassword" autocomplete="new-password">
                    </label>
                    <label>
                        <span>パスワードの確認</span>
                        <input id="password" type="password" name="new_password_confirmation" required
                               placeholder="NewPassword" autocomplete="new-password">
                    </label>
                    <hr>
                    @if($errors->any() || session('status') || session('warning'))
                        <div style="color: darkred;text-align: center">
                            @forelse ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @empty
                            @endforelse
                            @if (session('warning'))
                                <p>
                                    {{ session('warning') }}
                                </p>
                            @endif
                            @if (session('status'))
                                <p class="alert alert-info">
                                    {{ session('status') }}
                                </p>
                            @endif
                        </div>
                        <hr>
                    @endif
                    <div class="buttons">
                        <a href="{{ route('admin.dashboard') }}" class="button">キャンセル</a>
                        <button type="submit" class="primary">確定</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
