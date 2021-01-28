<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lily;
use App\Models\Triple;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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
        $triple_info = '';
        if(!empty($request->get('lily_id'))){
            try {
                $lily = Lily::findOrFail($request->get('lily_id'));
                $triples = $lily->triples()->with('lily')->get();
            }catch (ModelNotFoundException $e){
                abort(400, '指定されたリリィのレコードが存在しません');
            }
        }else{
            $triples = Triple::with('lily')->get();
            $lily = null;
        }

        //dd($triples->toArray());

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
                $lily = Lily::findOrFail($request->get('lily_id'));
            }catch (ModelNotFoundException $e){
                abort(404, '指定されたリリィのレコードが存在しません');
            }
        }else{
            $lily = null;
        }
        return view('admin.triple.create', compact('lily'));
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

        return redirect(route('admin.triple.index'))->with('message', 'レコードを追加しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
