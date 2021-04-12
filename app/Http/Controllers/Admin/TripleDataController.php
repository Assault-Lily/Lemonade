<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Triple;
use Auth;
use Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Log;
use Sarhan\Flatten\Flatten;

class TripleDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if($request->get('trashed')){
            switch ($request->get('trashed')){
                case 'contain':
                    $from = Triple::withTrashed();
                    break;
                case 'only':
                    $from = Triple::onlyTrashed();
                    break;
                default:
                    abort(400, '"trashed"パラメータが正しくありません');
            }
        }else{
            $from = Triple::select();
        }

        if(!empty($request->get('lily_slug'))){
            $lily = $request->get('lily_slug');
            $triples = $from->where('lily_slug','=',$lily)->get();
        }else{
            $lily = null;
            $triples = $from->get();
        }

        return view('admin.triple.index', compact('triples', 'lily'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        if(!empty($request->get('lily_slug'))){
            $args = [
                'lily' => $request->get('lily_slug')
            ];
        }else{
            $args = [
                'predicate' => $request->get('predicate'),
                'object'    => $request->get('object')
            ];
        }

        try {
            $lilies_sparql = sparqlQuery(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?name
WHERE {
  ?subject a lily:Lily;
           schema:name ?name.
  FILTER(lang(?name) = 'ja')
}
SPARQL
            );
            $lilies = array();
            foreach ($lilies_sparql->results->bindings as $record){
                $lilies[str_replace('lilyrdf:','',$record->subject->value)] = $record->name->value;
            }
        } catch (ConnectionException | RequestException $e) {
            $lilies = array();
        }

        $flatten = new Flatten();
        $predicates = $flatten->flattenToArray(config('triplePredicate'));

        return view('admin.triple.create', compact('args', 'predicates', 'lilies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'lily' => ['required', 'exists:lilies,slug'],
            'predicate' => ['required', 'string'],
            'object' => ['required', 'string']
        ]);

        $triple = new Triple();
        $triple->lily_slug = $request->lily;
        $triple->predicate = $request->predicate;
        $triple->object    = $request->object;

        $triple->save();

        // Logging and Notify

        $user = Auth::user();
        $log_text = 'トリプルが追加されました'.PHP_EOL
            .$this->generateLogText($triple).PHP_EOL
            .'登録者：'.$user->name.'('.$user->email.')';
        Http::post(env('DISCORD_URL'), [
            'content' => 'トリプルが追加されました！'.PHP_EOL.'追加実施者は '.$user->name.'('.$user->email.') です',
            'embeds' => [$this->generateDiscordEmbed($triple)]
        ]);
        Log::channel('adminlog')->info($log_text);

        return redirect(route('admin.triple.edit',['triple' => $triple->id]))->with('message', 'トリプルを追加しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function show($id)
    {
        return redirect(route('admin.triple.edit',['triple' => $id]));
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
            $triple = Triple::withTrashed()->findOrFail($id);
        }catch (ModelNotFoundException $e){
            abort(404, '指定されたトリプルが存在しません');
        }

        $flatten = new Flatten();
        $predicates = $flatten->flattenToArray(config('triplePredicate'));

        return view('admin.triple.edit', compact('triple', 'predicates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        if($request->restore){
            try {
                Triple::onlyTrashed()->findOrFail($id)->restore();
            }catch(ModelNotFoundException $e){
                abort(404, '指定されたトリプルはレストアできません');
            }

            // Logging and Notify

            $user = Auth::user();
            $notify = 'トリプルがレストアされました！'.PHP_EOL
                .'トリプルID : '.$id.' ('.route('admin.triple.show',['triple' => $id]).')'.PHP_EOL
                .'更新者 : '.$user->name.'('.$user->email.')';
            Http::post(env('DISCORD_URL'), [
                'content' => $notify
            ]);
            Log::channel('adminlog')->info($notify);

            return redirect(route('admin.triple.edit',['triple' => $id]))->with('message', 'トリプルをレストアしました');
        }

        try {
            $triple = Triple::findOrFail($id);
        }catch(ModelNotFoundException $e){
            abort(404, '指定されたトリプルが存在しません');
        }
        if ($triple->lily_slug != $request->lily_slug){
            abort(400, '紐付けるリリィが異なります');
        }

        $request->validate([
            'predicate' => ['required', 'string'],
            'object' => ['required', 'string']
        ]);

        $triple->predicate = $request->predicate;
        $triple->object    = $request->object;

        $triple->save();

        // Logging and Notify

        $user = Auth::user();
        $log_text = 'トリプルが更新されました'.PHP_EOL
            .$this->generateLogText($triple).PHP_EOL
            .'登録者：'.$user->name.'('.$user->email.')';
        Http::post(env('DISCORD_URL'), [
            'content' => 'トリプルが更新されました！'.PHP_EOL.'更新実施者は '.$user->name.'('.$user->email.') です',
            'embeds' => [$this->generateDiscordEmbed($triple)]
        ]);
        Log::channel('adminlog')->info($log_text);

        return redirect(route('admin.triple.edit',['triple' => $id]))->with('message', 'トリプルを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy($id)
    {
        try {
            Triple::findOrFail($id)->delete();
        }catch (ModelNotFoundException $e){
            abort(400, '指定したトリプルが存在しません');
        }

        // Logging and Notify

        $user = Auth::user();
        $notify = 'トリプルが削除されました...'.PHP_EOL
            .'トリプルID : '.$id.' ('.route('admin.triple.show',['triple' => $id]).')'.PHP_EOL
            .'更新者 : '.$user->name.'('.$user->email.')';
        Http::post(env('DISCORD_URL'), [
            'content' => $notify
        ]);
        Log::channel('adminlog')->info($notify);

        return redirect(route('admin.triple.edit',['triple' => $id]))->with('message', 'トリプルを削除しました');
    }

    protected function generateDiscordEmbed(Triple $triple)
    {
        return [
            'title' => '更新データ詳細',
            'url' => route('admin.triple.show',['triple' => $triple->id]),
            'footer' => [
                'text' => config('app.name').' Ver'.config('lemonade.version')
            ],
            'timestamp' => $triple->updated_at->format('Y-m-d H:i:s'),
            'fields' => [
                [
                    'name' => '主語',
                    'value' => $triple->lily_slug,
                    'inline' => true
                ],
                [
                    'name' => '述語',
                    'value' => config('triplePredicate.'.$triple->predicate).'('.$triple->predicate.')',
                    'inline' => true
                ],
                [
                    'name' => '目的語',
                    'value' => $triple->object
                ]
            ]
        ];
    }

    protected function generateLogText(Triple $triple)
    {
        return '主語　 : '.$triple->lily_slug.' ('.route('admin.triple.show',['triple' => $triple->id]).')'.PHP_EOL
            .'述語　 : '.$triple->predicate.PHP_EOL
            .'目的語 : '.$triple->object.PHP_EOL;
    }
}
