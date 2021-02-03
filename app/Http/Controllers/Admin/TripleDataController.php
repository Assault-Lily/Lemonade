<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lily;
use App\Models\Triple;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Sarhan\Flatten\Flatten;

class TripleDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $triples = null;

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

        if(!empty($request->get('lily_id'))){
            try {
                $lily = Lily::findOrFail($request->get('lily_id'));
                $triples = $from->where('lily_id','=',$lily->id)->with('lily')->get();
            }catch (ModelNotFoundException $e){
                abort(400, '指定されたリリィのレコードが存在しません');
            }
        }else{
            $triples = $from->with('lily')->get();
            $lily = null;
        }

        return view('admin.triple.index', compact('triples', 'lily'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!empty($request->get('lily_id'))){
            try {
                $args = [
                    'lily' => Lily::findOrFail($request->get('lily_id'))
                ];
                $lilies = [];
            }catch (ModelNotFoundException $e){
                abort(404, '指定されたリリィのレコードが存在しません');
            }
        }else{
            $args = [
                'predicate' => $request->get('predicate'),
                'object'    => $request->get('object')
            ];
            $lilies = Lily::orderBy('id')->get();
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
            'lily' => ['required', 'exists:lilies,id'],
            'predicate' => ['required', 'string'],
            'object' => ['required', 'string'],
            'spoiler' => ['boolean']
        ]);

        if(Triple::whereLilyId($request->lily)->where('predicate', 'like', $request->predicate)->exists()){
            return back()->withInput()->withErrors('同じ述語を持つトリプルが既に存在します。技術的な問題から、同じ述語を持つトリプルを登録することはできません。');
        }

        $triple = new Triple();
        $triple->lily_id   = $request->lily;
        $triple->predicate = $request->predicate;
        $triple->object    = $request->object;
        $triple->spoiler   = (bool)$request->spoiler ?? false;

        $triple->save();

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
            return redirect(route('admin.triple.edit',['triple' => $id]))->with('message', 'トリプルをレストアしました');
        }

        try {
            $triple = Triple::findOrFail($id);
        }catch(ModelNotFoundException $e){
            abort(404, '指定されたトリプルが存在しません');
        }
        if ($triple->lily_id != $request->lily_id){
            abort(400, '紐付けるリリィが異なります');
        }

        $request->validate([
            'predicate' => ['required', 'string'],
            'object' => ['required', 'string'],
            'spoiler' => ['boolean']
        ]);

        $triple->predicate = $request->predicate;
        $triple->object    = $request->object;
        $triple->spoiler   = (bool)$request->spoiler ?? false;

        $triple->save();

        return redirect(route('admin.triple.index'))->with('message', 'トリプルを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            Triple::findOrFail($id)->delete();
        }catch (ModelNotFoundException $e){
            abort(400, '指定したトリプルが存在しません');
        }

        return redirect(route('admin.triple.edit',['triple' => $id]))->with('message', 'トリプルを削除しました');
    }
}
