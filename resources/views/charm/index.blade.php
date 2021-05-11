@extends('app.layout', ['title' => 'CHARM一覧'])

@section('head')
    <style>
        .list-item-b{
            width: calc(50% - 5px);
        }
    </style>
@endsection

@section('main')
    <main>
        <div class="top-options">
            <div>ならべかえ : リソースキー順</div>
            <div><span class="info">登録機種数 : {{ count($charms) }}機種</span></div>
        </div>
        <div class="list">
            @forelse($charms as $charmKey => $charm)
                <a href="{{ route('charm.show', ['charm' => removePrefix($charmKey)]) }}" class="list-item-b">
                    <div class="title">
                        <div style="font-size: small" class="lang-en">{{ ($charm['schema:productID'][0] ?? '').' '.$charm['schema:name@en'][0] }}</div>
                        {{ $charm['schema:name'][0] }}
                    </div>
                    <div>{!! e($corporation[$charm['schema:manufacturer'][0] ?? '-']['schema:name'][0] ?? '') ?: '<span style="color:gray">メーカー情報なし</span>' !!}</div>
                </a>
            @empty
            @endforelse
        </div>
        @if(config('app.debug')) @dump($charms, $corporation) @endif
    </main>
@endsection
