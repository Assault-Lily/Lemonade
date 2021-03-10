@extends('app.layout',['title' => 'RDF同期管理'])

@section('main')
    <main>
        <h1>RDF同期確認</h1>
        <div class="white-box" style="text-align: center;">
            <p>以下のトリプルが有効であると判定されました。</p>
            <table style="width: 100%">
                <thead>
                <tr>
                    <th>Resource</th><th>Predicate</th><th>Object</th>
                </tr>
                </thead>
                <tbody>
                @forelse($triples as $resource => $triple)
                    @foreach($triple as $key => $value)
                        @foreach($value as $v)
                            <tr>
                                <td>{{ $resource }}</td>
                                <td>{{ config('triplePredicate.'.explode('@',$key)[0], '').' ('.$key.')' }}</td>
                                <td>{{ $v }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @empty
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
@endsection
