@extends('app.layout', ['title' => 'お知らせ管理'])

@section('main')
    <main>
        <div class="top-options">
            <div>
                <a href="{{ route('admin.notice.create') }}" class="button primary">新規作成</a>
            </div>
            <div>
                <span class="info">登録数 : {{ count($notice) }}</span>
            </div>
        </div>
        @dump($notice)
    </main>
@endsection
