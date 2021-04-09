@extends('app.layout',['title' => 'RDF同期管理', 'previous' => route('admin.dashboard')])

@section('main')
    <main>
        <h1>RDF同期管理</h1>
        <div style="text-align: center">
            <p>
                SPARQLエンドポイントに問い合わせを行い、基本データについて同期を試みます
            </p>
            <p style="color: darkred">
                Ver5以降はSPARQLからデータをすべて取得可能であるため、基本データの同期は不要です。
            </p>
            <div class="buttons three">
                <a href="{{ route('admin.rdf.lily') }}" class="button">リリィ基本データ同期</a>
            </div>
        </div>

    </main>
@endsection
