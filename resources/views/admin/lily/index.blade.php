@extends('app.layout',['title' => 'リリィデータ管理'])

@section('main')
    <main>
        <h1>データ一覧</h1>
        <div class="white-box">
            <p style="color: darkred;text-align: center">
                現状すべてのリリィのデータはassaultlily-rdfより取得されており、リリィデータをこちらで管理する必要はありません。
            </p>
            <div class="buttons three">
                <a href="{{ route('admin.lily.create') }}" class="button">新規登録</a>
            </div>
            <hr>
            <table style="width: 100%; text-align: center;">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>スラッグ</th>
                    <th>名前</th>
                    <th>名前(読み)</th>
{{--                    <th>名前(アルファベット)</th>--}}
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $row)
                    <?php
                    /**
                     * @var $row \App\Models\Lily
                     */
                    ?>
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->slug }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->name_y }}</td>
{{--                        <td>{{ $row->name_a }}</td>--}}
                        <td>
                            <a href="{{ route('admin.lily.show',['lily' => $row->id]) }}" class="button smaller">詳細</a>
                            <a href="{{ route('admin.lily.edit',['lily' => $row->id]) }}" class="button smaller">編集</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="color: darkred; font-style: italic; text-align: center">レコードがありません</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </main>
@endsection
