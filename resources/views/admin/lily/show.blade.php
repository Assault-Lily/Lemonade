@extends('app.layout',['title' => 'リリィデータ参照'])

<?php
    /**
     * @var $lily \App\Models\Lily
     */
?>

@section('head')
    <style>
        table#data{
            margin: 10px auto;
            max-width: 100%;
            border-spacing: 16px 5px;
        }
        table#data th{
            text-align: right;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>基本データ：{{ $lily->name }}</h1>
        <div class="white-box">
            <table id="data">
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
                    <th>カラーコード</th>
                    <td style="color: {{ $lily->color }}; font-weight: bold">{{ $lily->color }}</td>
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
    </main>
@endsection
