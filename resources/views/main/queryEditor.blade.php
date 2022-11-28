@extends('app.layout', ['title' => 'SPARQLクエリエディター'])

@section('head')
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/show-hint.min.css" integrity="sha512-OmcLQEy8iGiD7PSm85s06dnR7G7C9C0VqahIPAj/KHk5RpOCmnC6R2ob1oK4/uwYhWa9BF1GC6tzxsC8TIx7Jg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/sparql/sparql.min.js" integrity="sha512-VRyu4FjtnsV9Pe7mwhyJlw9Qb/k7KgPNf50ZtTU9dHbfqL5yBc28mn+n/Krf255nX3pdcrmSnW0FrkeSzd6cPA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js" integrity="sha512-I6CdJdruzGtvDyvdO4YsiAq+pkWf2efgd1ZUSK2FnM/u2VuRASPC7GowWQrWyjxCZn6CT89s3ddGI+be0Ak9Fg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/display/placeholder.min.js" integrity="sha512-acBo6sW2h2GZQ9BqU9v5RyYGPUEr1a9jrukJg825Y0ahxAg/7aqTNPtcalloqnf4DfsRVdcdNmcBNWPD8b8W8Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/show-hint.min.js" integrity="sha512-yhmeAerubMLaGAsyS7sE8Oqub6GeTkBDQpkXo2JKHgg7JOCudQvcbDQc5rPxdl7MqcDusTJzSy+ODlyzAwETfQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/anyword-hint.min.js" integrity="sha512-wdYOcbX/zcS4tP3HEDTkdOI5UybyuRxJMQzDQIRcafRLY/oTDWyXO+P8SzuajQipcJXkb2vHcd1QetccSFAaVw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        @font-face {
        font-family: "Noto Sans Mono CJK JP";
        font-weight: 400;
        font-display: swap;
        src: local("NotoSansMonoCJKjp-Regular"),
            local("Noto Sans Mono CJK JP Regular"),
            url("https://cdn.jsdelivr.net/npm/@japanese-monospaced-fonts/noto-sans-mono-cjk-jp@1.0.1/NotoSansMonoCJKJP-Regular.otf") format("opentype");
        }
    </style>
    @endsection

@section('main')
    <main>
        <h2>SPARQLクエリエディター</h2>
        <p style="font-size: smaller">このテキストボックスは編集可能です。Ctrl+Space で簡易な入力補完が効きます。</p>
        <label>
            <textarea id="sparql">{{ $query ?? '' }}</textarea>
        </label>
        <div class="buttons three">
            <div class="button primary" id="execute">クエリ実行</div>
        </div>
        <label>
            <textarea id="result" placeholder="実行結果がここに表示されます。" readonly></textarea>
        </label>
        <script>
            CodeMirror.commands.autocomplete = cm => {
                cm.showHint({hint: CodeMirror.hint.anyword});
            }
            const editor = CodeMirror.fromTextArea(document.getElementById('sparql'), {
                lineNumbers: true,
                readOnly: false,
                mode: 'application/sparql-query',
                extraKeys: {
                    "Ctrl-Space": "autocomplete",
                },
            });
            const resultArea = CodeMirror.fromTextArea(document.getElementById('result'), {
                lineNumbers: true,
                readOnly: true,
                placeholder: "実行結果がここに表示されます...",
                mode: 'application/json',
            });
            const wrapper= resultArea.getWrapperElement();
            wrapper.classList.add("CodeMirror-readonly");

            const button = document.getElementById('execute');
            button.addEventListener('click', (e) => {
                fetch("{{ config('lemonade.sparqlEndpoint') }}", {
                    method: 'POST',
                    body: editor.getValue(),
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/sparql-query; charset=utf-8',
                    },
                })
                .then(async response => {
                    if (!response.ok) {
                        throw new Error(await response.text());
                    }
                    return response.json();
                })
                .then(result => {
                    resultArea.setOption('mode', 'application/json');
                    const doc = resultArea.getDoc();
                    doc.setValue(JSON.stringify(result, null, '\t'));
                })
                .catch(error => {
                    resultArea.setOption('mode', 'text/plain');
                    const doc = resultArea.getDoc();
                    doc.setValue(error.message);
                });
            });

            editor.setSize('100%','100%');
            editor.on("mousedown", function () {
                editor.refresh()
            });
            resultArea.setSize('100%','100%');
            resultArea.on("mousedown", function () {
                resultArea.refresh()
            });
        </script>
        <p>
            SPARQL endpoint : <a href="{{ config('lemonade.sparqlEndpoint') }}" target="_blank">{{ config('lemonade.sparqlEndpoint') }}</a><br>
            Syntax highlight : <a href="https://codemirror.net/" target="_blank">CodeMirror</a>
        </p>
    </main>
@endsection
