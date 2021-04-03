<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lily;
use App\Rules\HexColorCode;
use App\Rules\Hiragana;
use Carbon\Carbon;
use Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Log;

class LilyDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $data = Lily::all();
        return view('admin.lily.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.lily.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug'   => ['required', 'alpha'],
            'name'   => 'required',
            'name_y' => ['required', new Hiragana('・ ')],
            'name_a' => ['required'],
        ]);

        $lily = new Lily;
        $lily->slug = strtolower($request->slug);
        $lily->name = $request->name;
        $lily->name_y = $request->name_y;
        $lily->name_a = $request->name_a;

        $lily->save();

        // Logging and Notify

        $user = \Auth::user();
        $log_str = 'リリィデータが追加されました'.PHP_EOL.$this->generateLogText($lily)
            .'登録者 : '.$user->name.'('.$user->email.')';
        Http::post(env('DISCORD_URL'), [
            'content' => 'リリィデータが追加されました！'.PHP_EOL .'追加実施者は '.$user->name.'('.$user->email.') です',
            'embeds' => [$this->generateDiscordEmbed($lily)]
        ]);
        Log::channel('adminlog')->info($log_str);

        return redirect(route('admin.lily.index'))->with('message', "レコードを追加しました");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $lily = Lily::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, 'レコードが存在しません');
        }


        return view('admin.lily.show', compact('lily'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $lily = Lily::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, 'レコードが存在しません');
        }

        return view('admin.lily.edit', compact('lily'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        try {
            $lily = Lily::findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, 'レコードが存在しません');
        }

        if ($request->id != $id){
            abort(400);
        }

        $request->validate([
            'slug'   => ['required', 'alpha'],
            'name'   => 'required',
            'name_y' => ['required', new Hiragana('・ ')],
            'name_a' => ['required'],
        ]);

        $lily->slug = strtolower($request->slug);
        $lily->name = $request->name;
        $lily->name_y = $request->name_y;
        $lily->name_a = $request->name_a;

        $lily->save();

        // Logging and Notify

        $user = \Auth::user();
        $log_str = 'リリィデータが更新されました'.PHP_EOL.$this->generateLogText($lily)
            .'登録者 : '.$user->name.'('.$user->email.')';
        Http::post(env('DISCORD_URL'), [
            'content' => 'リリィデータが更新されました！'.PHP_EOL .'更新実施者は '.$user->name.'('.$user->email.') です',
            'embeds' => [$this->generateDiscordEmbed($lily)]
        ]);
        Log::channel('adminlog')->info($log_str);

        return redirect(route('admin.lily.index'))->with('message', "レコードを更新しました");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(400);
    }

    protected function generateDiscordEmbed(Lily $lily)
    {
        return [
            'title' => '更新データ詳細',
            'url' => route('admin.lily.show',['lily' => $lily->id]),
            'footer' => [
                'text' => config('app.name').' Ver'.config('lemonade.version')
            ],
            'timestamp' => $lily->updated_at->format(\DateTime::ISO8601),
            'fields' => [
                [
                    'name' => 'リリィ登録名',
                    'value' => $lily->name
                ],
                [
                    'name' => 'なまえ',
                    'value' => $lily->name_y
                ],
                [
                    'name' => 'Name',
                    'value' => $lily->name_a ?? 'N/A'
                ],
                [
                    'name' => 'スラッグ',
                    'value' => $lily->slug,
                    'inline' => true
                ]
            ]
        ];
    }

    protected function generateLogText($lily)
    {
        return '登録名 : '.$lily->name.' ('.route('admin.lily.show',['lily' => $lily->id]).')'.PHP_EOL
            .'なまえ : '.$lily->name_y.', Name : '.$lily->name_a.PHP_EOL;
    }
}
