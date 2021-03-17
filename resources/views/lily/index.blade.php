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
        @if(!empty($rdf_error))
            <div class="window-a" style="margin-top: 15px">
                <div class="header">RDF連携エラー</div>
                <div class="body">
                    <p>
                        SPARQL問い合わせが正常に完了しませんでした。管理者までご連絡ください。
                        現在表示されている情報は大部分が欠落しています。
                    </p>
                    <p style="color: darkred">
                        @if($rdf_error instanceof \Illuminate\Http\Client\RequestException)
                            {!! nl2br(strip_tags($rdf_error->getMessage())) !!}
                        @endif
                        @if($rdf_error instanceof \Illuminate\Http\Client\ConnectionException)
                            {!! nl2br(strip_tags($rdf_error->getPrevious()->getHandlerContext()['error'] ?? '')) !!}
                        @endif
                    </p>
                </div>
            </div>
        @endif
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
                @include('app.button_lily',['lily' => $lily, 'triple' => $triples[$lily->slug] ?? array()])
            @empty
                <p style="text-align: center; color: darkred; margin: 3em auto">該当するデータがありません</p>
            @endforelse
        </div>
    </main>
@endsection
