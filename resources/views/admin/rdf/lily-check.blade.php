@extends('app.layout',['title' => 'RDF同期管理'])

@section('main')
    <main>
        <h1>RDF同期確認</h1>
        <div class="white-box" style="text-align: center;">
            <p>問い合わせの結果 <span style="font-weight: bold; font-size: large">{{ count($lilies) }}</span> のリリィの情報を取得できました</p>
            <table style="width: 100%">
                <thead>
                <tr>
                    <th>Resource</th><th>Name</th><th>Name EN</th><th>Name Kana</th>
                </tr>
                </thead>
                <tbody>
                @forelse($lilies as $lily)
                    <tr>
                        <td>{{ str_replace(config('lemonade.rdfPrefix.lilyrdf'),'', $lily->lily->value) }}</td>
                        <td>{{ $lily->name->value }}</td>
                        <td>{{ $lily->nameen->value }}</td>
                        <td>{{ $lily->namekana->value }}</td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
            <hr>
            <div class="buttons">
                <a href="" class="button primary">登録実行</a>
            </div>
        </div>
    </main>
@endsection
