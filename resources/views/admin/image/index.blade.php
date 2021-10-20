@extends('app.layout', ['title' => '画像データ管理', 'previous' => route('admin.dashboard')])

@section('main')
    <main>
        <h1>画像データ一覧</h1>
        <div class="white-box">
            <div class="buttons three">
                <a href="{{ route('admin.image.create') }}" class="button primary">新規登録</a>
                <a href="{{ route('admin.image.index', ['export' => 'json']) }}" class="button">JSONで表示</a>
            </div>
            <hr>
            <div>
                <p class="center">
                    件数 : {{ count($images) }}
                </p>
            </div>
            <hr>
            <table style="min-width: 100%; text-align: center">
                <thead>
                <tr>
                    <th>ID</th><th>対象リソース</th><th>画像種別</th><th>作者</th><th>画像ホスト</th><th>操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($images as $image)
                    <?php
                        /** @var $image \App\Models\Image */
                    ?>
                    <tr>
                        <td>{{ $image->id }}</td>
                        <td>{{ $image->for }}</td>
                        <td>{{ $image->type }}</td>
                        <td>{{ $image->author }}</td>
                        <td>{{ parse_url($image->image_url, PHP_URL_HOST) ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.image.edit', ['image' => $image->id]) }}" class="button smaller">確認・編集</a>
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
