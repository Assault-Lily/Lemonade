@extends('app.layout', ['page-type' => 'back-triangle', 'title' => 'リリィ一覧'])

<?php
    /**
     * @var $lilies \Illuminate\Database\Eloquent\Collection
     * @var $lily \App\Models\Lily
     */
?>

@section('head')
    <style>

    </style>
@endsection

@section('main')
    <main>
        <div class="top-options">
            <div>
                ならべかえ
            </div>
            <div>
                <span class="info">リリィ登録数 : {{ $lilies->count() }}</span>
            </div>
        </div>
        <div class="list three">
            @forelse($lilies as $lily)
                @include('app.button_lily',['lily' => $lily, 'triple' => $triples[$lily->id] ?? array()])
            @empty
                <p style="text-align: center; color: darkred; margin: 3em auto">該当するデータがありません</p>
            @endforelse
        </div>
    </main>
@endsection
