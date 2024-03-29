@extends('app.layout',['title' => 'リリィデータ参照'])

<?php
    /**
     * @var $lily \App\Models\Lily
     * @var $triple \App\Models\Triple
     */
?>

@section('head')
    <style>
        table.data{
            margin: 10px auto;
            max-width: 100%;
            border-spacing: 16px 5px;
        }
        table.data th{
            text-align: right;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>基本データ：{{ $lily->name }}</h1>
        <div class="white-box">
            <table class="data">
                <tr>
                    <th>名前</th>
                    <td style="font-size: 30px">{{ $lily->name }}</td>
                </tr>
                <tr>
                    <th>名前(読み)</th>
                    <td>{{ $lily->name_y }}</td>
                </tr>
                <tr>
                    <th>名前(アルファベット)</th>
                    <td>{{ $lily->name_a }}</td>
                </tr>
                <tr>
                    <th>システム内部ID</th>
                    <td>{{ $lily->id }}</td>
                </tr>
                <tr>
                    <th>スラッグ</th>
                    <td>{{ $lily->slug }}</td>
                </tr>
                <tr>
                    <th>登録日時</th>
                    <td>{{ $lily->created_at }}</td>
                </tr>
                <tr>
                    <th>更新日時</th>
                    <td>{{ $lily->updated_at }}</td>
                </tr>
            </table>
            <hr>
            <div class="buttons three">
                <a href="{{ route('admin.lily.index') }}" class="button">一覧に戻る</a>
                <a href="{{ route('admin.lily.edit',['lily' => $lily->id]) }}" class="button">データ編集</a>
            </div>
        </div>
        <h2>トリプル簡易参照</h2>
        <div class="white-box">
            <table class="data">
                <thead>
                <tr>
                    <th>述語</th>
                    <th style="text-align: left">目的語(オブジェクト)</th>
                </tr>
                </thead>
                <tbody>
                @forelse($lily->triples as $triple)
                    <?php
                    $predicate = config('triplePredicate.'.$triple->predicate)
                    ?>
                    <tr>
                        <th>{{ !empty($predicate) ? $predicate.' ('.$triple->predicate.')' : $triple->predicate }}</th>
                        <td>{{ $triple->object }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="color: darkred; font-style: italic; text-align: center">トリプルが登録されていません</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <hr>
            <div class="buttons three">
                <a href="{{ route('admin.triple.index',['lily_id' => $lily->id]) }}" class="button">トリプル詳細・編集</a>
                <a href="{{ route('admin.triple.create',['lily_id' => $lily->id]) }}" class="button">トリプル新規登録</a>
            </div>
        </div>
    </main>
@endsection
