@extends('app.layout', ['page-type' => 'back-triangle', 'title' => 'リリィ一覧'])

<?php
    /**
     * @var $lilies array
     * @var $legions array
     * @var $lily array
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
                ならべかえ : 読み順
            </div>
            <div>
                <span class="info">リリィ登録数 : {{ count($lilies) }}</span>
            </div>
        </div>
        <div class="list three">
            @forelse($lilies as $key => $lily)
                <?php $legion = $legions[$lily['lily:legion'][0] ?? ''] ?? array(); ?>
                @include('app.button_lily',['key' => $key, 'lily' => $lily, 'legion' => $legion])
            @empty
                <p style="text-align: center; color: darkred; margin: 3em auto">該当するデータがありません</p>
            @endforelse
            @if((count($lilies) % 3) != 0)
                <div style="width: 32%; margin-left: 6px"></div>
            @endif
        </div>
    </main>
@endsection
