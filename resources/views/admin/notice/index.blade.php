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
        <div>
            @forelse($notice as $note)
                <?php /** @var $note \App\Models\Notice */ ?>
                <div class="list-item-b">
                    <div class="title"><a href="{{ route('admin.notice.edit', ['notice' => $note->id]) }}">{{ $note->title }}</a></div>
                    <div style="margin: .5em 0; display: flex; justify-content: space-between">
                        <div>
                            <span class="tag">登録</span>{{ $note->created_at }}
                            <span class="tag">更新</span>{{ $note->updated_at }}
                            <span class="tag">重要度</span>{{ $note->importance }}
                        </div>
                        <div>
                            <a href="{{ route('admin.notice.edit', ['notice' => $note->id]) }}" class="button smaller">編集</a>
                        </div>
                    </div>
                    <p>{{ base64_decode($note->body) }}</p>
                </div>
            @empty
                <p class="center notice" style="margin: 30px 0">
                    お知らせは登録されていません。
                </p>
            @endforelse
        </div>
    </main>
@endsection
