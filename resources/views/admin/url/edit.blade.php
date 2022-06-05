@extends('app.layout', ['title' => '関連URL編集'])

<?php
    /** @var $url App\Models\Url */
?>

@section('main')
    <main>
        <h1>関連URL編集</h1>
        <div class="white-box">
            <p class="center">
                作成 : {{ $url->created_at }}<br>
                更新 : {{ $url->updated_at }}
            </p>
            <hr>
            <form action="{{ route('admin.url.update', ['url' => $url->id]) }}" method="post">
                @csrf @method('patch')
                <table class="table">
                    <tr>
                        <th>対象リソース</th>
                        <td>
                            <label>
                                <input type="text" style="width: 100%; text-align: left;" name="for"
                                       value="{{ old('title', $url->for) }}" required>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td style="display: flex">
                            <div style="width: 90%">
                                <label>
                                    <input type="text" style="width: 100%; text-align: left;" name="url"
                                           value="{{ old('url', $url->url) }}" placeholder="https://" required
                                           pattern="^https?://.+$">
                                </label>
                            </div>
                            <div style="width: 10%">
                                <a href="javascript:void(0)" class="button smaller">OGP自動取得</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>タイトル</th>
                        <td>
                            <label>
                                <input type="text" style="width: 100%; text-align: left;" name="title"
                                       value="{{ old('title', $url->title) }}" required>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>概要</th>
                        <td rowspan="2">
                            <label>
                                <textarea style="width: 100%; min-height: 250px; text-align: left; resize: vertical;"
                                          name="description">{{ old('description', $url->description) }}</textarea>
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
                    <input type="submit" class="button primary" value="更新実行">
                </div>
            </form>
        </div>

        <h2>削除</h2>
        <div class="white-box">
            <p class="center">
                URLが無効になった場合はこれを削除することができます。<br>
                この操作は取り消しできません。
            </p>
            <hr>
            <form action="{{ route('admin.url.destroy', ['url' => $url->id]) }}" method="post">
                @csrf @method('delete')
                <div class="buttons">
                    <input type="submit" class="button" value="削除実行" style="color: darkred">
                </div>
            </form>
        </div>
    </main>
@endsection
