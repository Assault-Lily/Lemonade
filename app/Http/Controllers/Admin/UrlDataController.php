<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Url;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UrlDataController extends Controller
{
    private $validationRule = [
        'url' => ['required', 'url'],
        'title' => ['required'],
    ];

    public function index(Request $request)
    {
        $urls = Url::orderBy('updated_at', 'desc')->get();

        return view('admin.url.index', compact('urls'));
    }

    public function create(Request $request)
    {
        $args = [
            'for' => $request->input('for'),
        ];

        return view('admin.url.create', compact('args'));
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRule);

        $url = new Url();
        $url->for         = $request->input('for');
        $url->url         = $request->input('url');
        $url->title       = $request->input('title');
        $url->description = $request->input('description');

        $url->save();

        return redirect(route('admin.url.edit', ['url' => $url->id]))->with('message', '保存しました');
    }

    public function show(Request $request, $id)
    {
        return redirect(route('admin.url.edit', ['url' => $id]));
    }

    public function edit(Request $request, $id)
    {
        try {
            $url = Url::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            abort(404, '該当するデータが存在しません');
        }
        return view('admin.url.edit', compact('url'));
    }

    public function update(Request $request, $id)
    {
        try {
            $url = Url::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            abort(404, '該当するデータが存在しません');
        }

        $request->validate($this->validationRule);

        $url->for         = $request->input('for');
        $url->url         = $request->input('url');
        $url->title       = $request->input('title');
        $url->description = $request->input('description');

        $url->save();

        return redirect(route('admin.url.edit', ['url' => $url->id]))->with('message', '保存しました');
    }

    public function destroy($id)
    {
        try {
            Url::findOrFail($id)->delete();
        } catch (ModelNotFoundException $e) {
            abort(404, '該当するデータが存在しません');
        }

        return redirect(route('admin.url.index'))->with('message', '削除しました');
    }

    public function fetchOgp(Request $request)
    {
        $url = $request->input('url');

        if (!\Str::startsWith($url ,'http')) {
            return response(['error' => 'Required param "URL" not given.'], 400);
        }

        $tags = array();

        // リクエストしDOM変換
        $data = \Http::get($url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($data->body());

        // XPathによりスクレイピング
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//head/meta[@property]') as $element) {
            /** @var $element \DOMElement */
            // OGPを示すmeta要素の場合配列に保存
            if (\Str::startsWith($element->getAttribute('property') ?? '', 'og:')){
                $tags[$element->getAttribute('property')] = $element->getAttribute('content');
            }
        }

        return $tags;
    }
}
