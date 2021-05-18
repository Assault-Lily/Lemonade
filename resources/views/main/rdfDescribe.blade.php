@extends('app.layout', ['title' => 'RDF簡易参照'])

@section('head')
    <meta name="robots" content="noindex">
    <link rel=stylesheet href="//codemirror.net/lib/codemirror.css">
    <script src="//codemirror.net/lib/codemirror.js"></script>
    <script src="//codemirror.net/mode/turtle/turtle.js"></script>
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
