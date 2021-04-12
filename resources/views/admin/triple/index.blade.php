@extends('app.layout',['title' => 'トリプルデータ管理'])

<?php
    /**
     * @var $triple \App\Models\Triple
     */
?>

@section('head')
    <style>
        table#triples{
            width: 100%;
            text-align: center;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>独自トリプル参照</h1>
        <div class="white-box">
            @if(!empty($lily))
                <p style="text-align: center; color: darkred;">
                    主語が {{ $lily }} のトリプルに絞り込まれています。
                    <a href="{{ url()->current() }}" class="button smaller">全件表示</a>
                </p>
                <div class="buttons three">
                    <a href="{{ route('admin.triple.create',['lily_slug' => $lily]) }}" class="button">
                        トリプル新規登録 ({{ $lily }})
                    </a>
                    <a href="{{ route('admin.triple.index',['lily_slug' => $lily, 'trashed' => 'contain']) }}" class="button">削除済みトリプルも表示</a>
                    <a href="{{ route('admin.triple.index',['lily_slug' => $lily, 'trashed' => 'only']) }}" class="button">削除済みトリプルのみ表示</a>
                </div>
            @else
                <div class="buttons three">
                    <a href="{{ route('admin.triple.create') }}" class="button primary">トリプル新規登録</a>
                    <a href="{{ route('admin.triple.index',['trashed' => 'contain']) }}" class="button">削除済みトリプルも表示</a>
                    <a href="{{ route('admin.triple.index',['trashed' => 'only']) }}" class="button">削除済みトリプルのみ表示</a>
                </div>
            @endif
            <hr>
            <table id="triples">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>主語(リリィ)</th>
                    <th>述語</th>
                    <th>目的語(オブジェクト)</th>
                    <th>更新日時</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($triples as $triple)
                    <?php
                        $predicate = config('triplePredicate.'.$triple->predicate)
                    ?>
                    <tr @if($triple->trashed()) style="font-style: italic;" @endif>
                        <td>{{ $triple->id }}</td>
                        <td>{{ $triple->lily_slug }}</td>
                        <td>{{ !empty($predicate) ? $predicate.' ('.$triple->predicate.')' : $triple->predicate }}</td>
                        <td>{{ $triple->object }}</td>
                        <td>{{ $triple->updated_at }}@if($triple->trashed()) (削除済み)@endif</td>
                        <td>
                            <a href="{{ route('admin.triple.edit',['triple' => $triple->id]) }}" class="button smaller">編集</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="color: darkred; font-style: italic; text-align: center">トリプルが登録されていません</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
@endsection
