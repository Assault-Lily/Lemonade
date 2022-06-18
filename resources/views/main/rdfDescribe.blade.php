@extends('app.layout', ['title' => 'RDF簡易参照'])

@section('head')
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js" integrity="sha512-hG/Qw6E14LsVUaQRSgw0RrFA1wl5QPG1a4bCOUgwzkGPIVFsOPUPpbr90DFavEEqFMwFXPVI0NS4MzKsInlKxQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/turtle/turtle.min.js" integrity="sha512-wa7FFV53hnpoyBpyed3ASIng6HcJciZ7US1tBL/K3eQzfUOK8ZDu6qJ/wowimjY+BzHTqX+ee3vWMaPI/IXbcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection

@section('main')
    <main>
        <h2>lilyrdf:{{ $resource }}</h2>
        <label>
            <textarea id="turtle" readonly>{{ $turtle ?? '' }}</textarea>
        </label>
        <script>
            const cm = CodeMirror.fromTextArea(document.getElementById('turtle'), {
                lineNumbers: true,
                readOnly: true,
                mode: 'text/turtle'
            });
            cm.setSize('100%','100%');
        </script>
        <p>
            SPARQL endpoint : <a href="{{ config('lemonade.sparqlEndpoint') }}" target="_blank">{{ config('lemonade.sparqlEndpoint') }}</a><br>
            Syntax highlight : <a href="https://codemirror.net/" target="_blank">CodeMirror</a>
        </p>
    </main>
@endsection
