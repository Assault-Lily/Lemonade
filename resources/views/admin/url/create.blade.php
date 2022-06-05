@extends('app.layout', ['title' => '関連URL新規登録'])

@section('head')
    @include('admin.url.ogpfetch')
@endsection

@section('main')
    <main>
        <h1>関連URL新規登録</h1>
        <div class="white-box">
            <form action="{{ route('admin.url.store') }}" method="post">
                @csrf
                <table class="table">
                    <tr>
                        <th>対象リソース</th>
                        <td>
                            <label>
                                <input type="text" style="width: 100%; text-align: left;" name="for"
                                       value="{{ old('title', $args['for'] ?? '') }}" required>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td style="display: flex">
                            <div style="width: 90%">
                                <label>
                                    <input type="text" style="width: 100%; text-align: left;" name="url" id="url"
                                           value="{{ old('url') }}" placeholder="https://" required
                                           pattern="^https?://.+$">
                                </label>
                            </div>
                            <div style="width: 10%">
                                <a href="javascript:ogpFetch(document.getElementById('url').value)"
                                   class="button smaller" target="_self">OGP自動取得</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>タイトル</th>
                        <td>
                            <label>
                                <input type="text" style="width: 100%; text-align: left;" name="title"
                                       value="{{ old('title') }}" required>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>概要</th>
                        <td rowspan="2">
                            <label>
                                <textarea style="width: 100%; min-height: 250px; text-align: left; resize: vertical;"
                                          name="description">{{ old('description') }}</textarea>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="spacer"></td>
                    </tr>
                </table>
                <hr>
                <div class="buttons">
                    <input type="reset" class="button">
                    <input type="submit" class="button primary" value="登録実行">
                </div>
            </form>
        </div>
    </main>
@endsection
