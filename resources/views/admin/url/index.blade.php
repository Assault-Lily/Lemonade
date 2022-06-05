@extends('app.layout', ['title' => '関連URL管理'])

@section('main')
    <main>
        <h1>関連URL一覧</h1>
        <div class="white-box">
            <div class="buttons three">
                <a class="button primary" href="{{ route('admin.url.create') }}">URL新規登録</a>
            </div>
            <hr>
            <table style="min-width: 100%; text-align: center" id="urls">
                <thead>
                <tr>
                    <th class="sort" data-sort="updated-at" style="width: 150px">更新日時</th>
                    <th class="sort" data-sort="for">対象リソース</th>
                    <th class="sort" data-sort="title">タイトル</th>
                    <th class="sort" data-sort="url">URL</th>
                    <th style="width: 100px">操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($urls as $url)
                    <?php
                    /** @var $url \App\Models\Url */
                    ?>
                    <tr>
                        <td class="updated-at" data-updated="{{ $url->updated_at->format('U') }}">{{ $url->updated_at->format('Y-m-d - H:i') }}</td>
                        <td class="for">{{ $url->for }}</td>
                        <td class="type">{{ $url->title }}</td>
                        <td class="author">{{ $url->url }}</td>
                        <td>
                            <a href="{{ route('admin.url.edit', ['url' => $url->id]) }}" class="button smaller">確認・編集</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 20px; color: darkred;">
                            レコードが存在しません
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
@endsection
