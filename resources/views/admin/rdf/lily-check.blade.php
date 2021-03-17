@extends('app.layout',['title' => 'RDF同期管理', 'previous' => route('admin.rdf.index')])

@section('head')
    <style>
        .border{
            border: solid 1px gray;
        }
    </style>
@endsection

@section('main')
    <main>
        <h1>RDF同期確認</h1>
        <div class="white-box" style="text-align: center;">
            <p>
                問い合わせの結果 <span style="font-weight: bold; font-size: large">{{ count($lilies) }}</span> のリリィの情報を取得できました<br>
                同期を続行するとテーブルはTRUNCATEされます。
            </p>
            <div class="border" style="height: 500px; overflow-y:scroll;">
                <table style="width: 100%;">
                    <thead>
                    <tr>
                        <th>Resource</th><th>Name</th><th>Name EN</th><th>Exists?</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lilies as $lily)
                        <?php
                        /**
                         * @var $lily
                         */
                        $resource = str_replace(config('lemonade.rdfPrefix.lilyrdf'),'', $lily->lily->value);
                        ?>
                        <tr>
                            <td>{{ $resource }}</td>
                            <td>{{ $lily->name->value }}</td>
                            <td>{{ $lily->nameen->value }}</td>
                            <td>{{ !empty($lilies_exists[$resource]) ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>

            <hr>
            <form method="post" action="{{ route('admin.rdf.lilySync') }}">
                @csrf
                <div class="buttons">
                    <input type="submit" class="button primary" value="同期続行">
                </div>
            </form>
        </div>
    </main>
@endsection
