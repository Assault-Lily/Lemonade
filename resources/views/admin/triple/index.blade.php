@extends('app.layout',['title' => 'トリプルデータ管理'])

<?php
    /**
     * @var $triple \App\Models\Triple
     * @var $lily \App\Models\Lily
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
        <h1>トリプルデータ参照</h1>
        <div class="white-box">
            @if(!empty($lily))
                <p style="text-align: center; color: darkred;">
                    {{ $lily->name }} (ID: {{ $lily->id }}) のトリプルに絞り込まれています。
                    <a href="{{ url()->current() }}" class="button smaller">全件表示</a>
                </p>
                <div class="buttons three">
                    <a href="{{ route('admin.triple.create',['lily_id' => $lily->id]) }}" class="button">
                        トリプル新規登録 ({{ $lily->name }})
                    </a>
                </div>
            @else
                <div class="buttons three">
                    <a href="{{ route('admin.triple.create') }}" class="button">トリプル新規登録</a>
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
                    <tr>
                        <td>{{ $triple->id }}</td>
                        <td>{{ $triple->lily->name }}</td>
                        <td>{{ !empty($predicate) ? $predicate.' ('.$triple->predicate.')' : $triple->predicate }}</td>
                        <td>{{ $triple->object }}</td>
                        <td>{{ $triple->updated_at }}</td>
                        <td></td>
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
