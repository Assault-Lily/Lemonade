@extends('app.layout', ['title' => '画像データ管理', 'previous' => route('admin.dashboard')])

@section('head')
    <style>
        .sort{
            cursor: pointer;
        }
        .sort::after{
            content: '◆';
            color: lightgray;
            padding-left: 4px;
        }
        .sort.asc::after{
            content: '▲';
            color: black;
        }
        .sort.desc::after{
            content: '▼';
            color: black;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>画像データ一覧</h1>
        <div class="white-box">
            <div class="buttons three">
                <a href="{{ route('admin.image.create') }}" class="button primary">新規登録</a>
                <a href="{{ route('admin.image.createByJson') }}" class="button">JSONから一括登録</a>
                <a href="{{ route('admin.image.index', ['export' => 'json']) }}" class="button">JSONで表示</a>
            </div>
            <hr>
            <div style="display: flex; justify-content: center; gap: 15px; align-content: center;">
                <div>
                    件数 : {{ count($images) }}
                </div>
                <form action="{{ route('admin.image.index') }}" method="get">
                    <label>
                        <input type="search" name="author" value="{{ request()->get('author') }}" placeholder="作者名(部分一致)">
                    </label>
                </form>
            </div>
            <hr>
            <table style="min-width: 100%; text-align: center" id="images">
                <thead>
                <tr>
                    {{--<th class="sort" data-sort="created-at">作成日時</th>--}}
                    <th class="sort" data-sort="updated-at">更新日時</th>
                    <th class="sort" data-sort="for">対象リソース</th>
                    <th class="sort" data-sort="type">画像種別</th>
                    <th class="sort" data-sort="author">作者</th>
                    <th>画像ホスト</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody class="sort-list">
                @forelse($images as $image)
                    <?php
                        /** @var $image \App\Models\Image */
                    ?>
                    <tr>
                        {{--<td class="created-at" data-created="{{ $image->created_at->format('U') }}">{{ $image->created_at->format('Y-m-d - H:i') }}</td>--}}
                        <td class="updated-at" data-updated="{{ $image->updated_at->format('U') }}">{{ $image->updated_at->format('Y-m-d - H:i') }}</td>
                        <td class="for">{{ $image->for }}</td>
                        <td class="type">{{ $image->type }}</td>
                        <td class="author">{{ $image->author }}</td>
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
            <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
            <script>
                let options = {
                    valueNames: [/*{name: 'created-at', attr: 'data-created'},*/ {name: 'updated-at', attr: 'data-updated'}, 'for', 'type', 'author'],
                    listClass: 'sort-list'
                };
                let imageList = new List('images', options);
            </script>
        </div>
    </main>
@endsection
