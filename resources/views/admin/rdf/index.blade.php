@extends('app.layout',['title' => 'RDF同期管理'])

@section('main')
    <main>
        <h1>RDF同期管理</h1>
        <div class="white-box" style="text-align: center">
            <p>現在のRDF同期状況です</p>
        </div>

        <h2>RDF同期</h2>
        <div class="white-box">
            <p>RDFファイルの内容を基にトリプルの同期を行います。</p>
            <p>既存のRDFから同期されたトリプルは削除されます。既存でないリリィリソースが検出された場合新規登録を行うか確認します。</p>
            <form action="{{ route('admin.rdf.check') }}" method="post">
                @csrf
                <label>
                    <textarea name="rdf" style="width: 100%; height: 200px; text-align: left; resize: none"
                              required>{{ old('rdf') }}</textarea>
                </label>
                <hr>
                @if($errors->any())
                    <div style="color: darkred;text-align: center">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                    <hr>
                @endif
                <div class="buttons">
                    <input type="submit" value="確認" class="button primary">
                </div>
            </form>
        </div>
    </main>
@endsection
