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
                <label>
                    ならべかえ :
                    <select>
                        <option>フルネーム 昇順</option>
                        <option>フルネーム 降順</option>
                        <option>名前 昇順</option>
                        <option>名前 降順</option>
                        <option>レアスキル 昇順</option>
                        <option>レアスキル 降順</option>
                        <option>年齢 昇順</option>
                        <option>年齢 降順</option>
                        <option>ポジション 昇順</option>
                        <option>ポジション 降順</option>
                        <option>レギオン 昇順</option>
                        <option>レギオン 降順</option>
                    </select>
                </label>
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
