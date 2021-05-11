<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Triple;

class LilyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $triples_sparql = sparqlQueryOrDie(<<<SPQRQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>

SELECT ?subject ?predicate ?object
WHERE {
    {
        VALUES ?predicate {
            schema:name lily:nameKana schema:familyNameKana foaf:age
            lily:rareSkill lily:subSkill lily:isBoosted lily:boostedSkill
            lily:garden lily:grade lily:legion lily:position rdf:type
        }
        ?subject a lily:Lily;
                 ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name schema:alternameName rdf:type }
        ?subject a lily:Legion;
                 ?predicate ?object.
    }
}
SPQRQL
);
        $triples = sparqlToArray($triples_sparql);

        $lilies = array();
        $legions = array();

        // フィルタ用データリスト変数初期化
        $datalist = array();

        // レギオンとリリィの振り分け
        foreach ($triples as $key => $triple){
            if($triple['rdf:type'][0] === 'lily:Lily'){
                $lilies[$key] = $triple;

                // スキルデータリスト生成
                if(!empty($triple['lily:rareSkill'][0])){
                    $datalist['rareSkill'][] = $triple['lily:rareSkill'][0];
                }
                if(!empty($triple['lily:subSkill'])){
                    foreach ($triple['lily:subSkill'] as $subSkill)
                        $datalist['subSkill'][] = $subSkill;
                }
                if(!empty($triple['lily:boostedSkill'])){
                    foreach ($triple['lily:boostedSkill'] as $boostedSkill)
                        $datalist['boostedSkill'][] = $boostedSkill;
                }
                if(!empty($triple['lily:garden'][0])){
                    $datalist['garden'][] = $triple['lily:garden'][0];
                }
            }else{
                $legions[$key] = $triple;
            }
        }
        $datalist['rareSkill'] = array_unique($datalist['rareSkill']);
        $datalist['subSkill'] = array_unique($datalist['subSkill']);
        $datalist['boostedSkill'] = array_unique($datalist['boostedSkill']);
        $datalist['garden'] = array_unique($datalist['garden']);

        // 特殊表示変数初期化
        $additional = array();

        // フィルタ判別
        $filterBy = request()->get('filterBy','');
        $filterInfo = array();
        if(!empty($filterBy)){
            switch ($filterBy){
                case 'rareSkill':
                    $filterKey = 'lily:rareSkill';
                    $filterInfo['key'] = 'レアスキル';
                    break;
                case 'subSkill':
                    $filterKey = 'lily:subSkill';
                    $filterInfo['key'] = 'サブスキル';
                    break;
                case 'boostedSkill':
                    $filterKey = 'lily:boostedSkill';
                    $filterInfo['key'] = 'ブーステッドスキル';
                    break;
                case 'age':
                    $filterKey = 'foaf:age';
                    $filterInfo['key'] = '年齢';
                    $additional = [
                        'key' => $filterKey,
                        'suffix' => '歳'
                    ];
                    break;
                case 'position':
                    $filterKey = 'lily:position';
                    $filterInfo['key'] = 'ポジション';
                    $additional = [
                        'key' => $filterKey
                    ];
                    break;
                case 'garden':
                    $filterKey = 'lily:garden';
                    $filterInfo['key'] = 'ガーデン';
                    break;
                default:
                    abort(400, '指定されたキーではフィルタできません');
            }
            $filterValue = (string) request()->get('filterValue','');
            if(empty($filterValue)){
                abort(400, 'フィルタ値が空です');
            }else{
                $filterInfo['value'] = $filterValue;
                foreach ($lilies as $lilyKey => $lily){
                    // フィルタドロップ処理
                    if(empty($lily[$filterKey]) || array_search($filterValue, $lily[$filterKey]) === false){
                        unset($lilies[$lilyKey]);
                    }
                }
                unset($lily);
            }
        }

        // ソート判別
        $sortKey = request()->get('sort', 'name');
        switch ($sortKey){
            case 'name':
                $sort = 'lily:nameKana';
                break;
            case 'givenName':
                $sort = 'lily:givenNameKana';
                break;
            case 'rareSkill':
                $sort = 'lily:rareSkill';
                break;
            case 'age':
                $sort = 'foaf:age';
                $additional = [
                    'key' => $sort,
                    'suffix' => '歳'
                ];
                break;
            case 'position':
                $sort = 'lily:position';
                $additional = [
                    'key' => $sort
                ];
                break;
            case 'legion':
                $sort = 'lily:legion';
                break;
            case 'garden':
                $sort = 'lily:garden';
                break;
            default:
                abort(400, '指定されたキーではソートできません');
        }
        $orderGet = request()->get('order', 'asc');
        switch ($orderGet){
            case 'asc':
                $order = SORT_ASC;
                break;
            case 'desc':
                $order = SORT_DESC;
                break;
            default:
                abort(400, '昇順降順指定に誤りがあります');
        }

        $lily_sortKey = array();
        $lily_sortKeyForKeyUnknown = array();
        $lily_sortKeyKana = array();
        $lily_sortKeyForKanaUnknown = array();
        foreach ($lilies as $lily){
            // ソート用キー配列生成
            $lily_sortKey[] = implode(',' ,$lily[$sort] ?? ['-']);
            $lily_sortKeyForKeyUnknown[] = isset($lily[$sort][0]) ? 0 : 1;
            $lily_sortKeyKana[] = $lily['lily:nameKana'][0] ?? '-';
            $lily_sortKeyForKanaUnknown[] = isset($lily['lily:nameKana'][0]) ? 0 : 1;
        }
        unset($lily);
        // リリィのソート
        array_multisort($lily_sortKeyForKeyUnknown, SORT_ASC, SORT_NUMERIC,
            $lily_sortKey, $order, SORT_STRING,
            $lily_sortKeyForKanaUnknown, SORT_ASC, SORT_NUMERIC,
            $lily_sortKeyKana, $order, SORT_STRING, $lilies);

        $sortKey = substr(request()->get('order', 'asc'), 0, 1).'-'.$sortKey;

        // アイコンデータ取得
        $icons = array();
        foreach (Image::whereType('icon')->get() as $icon){
            $icons['lilyrdf:'.$icon->for][] = $icon;
        }

        return response()->view('lily.index',
            compact('lilies', 'legions', 'sortKey', 'filterInfo', 'additional',
                'datalist', 'icons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //public function create()
    //{
    //    //
    //}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function store(Request $request)
    //{
    //    //
    //}

    /**
     * Display the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $triples_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
    {
        VALUES ?subject { lilyrdf:$slug }
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name lily:resource lily:additionalInformation }
        lilyrdf:$slug ?rp ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:productID schema:name }
        lilyrdf:$slug lily:charm/lily:resource ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name }
        lilyrdf:$slug lily:relationship/lily:resource ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name }
        lilyrdf:$slug schema:sibling/lily:resource ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name }
        lilyrdf:$slug lily:cast/lily:performIn ?subject.
        ?subject ?predicate ?object.
    }
}
SPARQL
);
        $triples = sparqlToArray($triples_sparql);

        if(empty($triples) || empty($triples['lilyrdf:'.$slug])) abort(404, '該当するリリィのデータが存在しません');

        $triples_model = Triple::whereLilySlug($slug)->get();
        foreach ($triples_model as $triple){
            $triples['lilyrdf:'.$slug][$triple->predicate][] = $triple->object;
        }

        $icons = Image::whereFor($slug)->where('type','=','icon')->get();

        return view('lily.show', compact('triples', 'slug', 'icons'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function edit($id)
    //{
    //    //
    //}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function update(Request $request, $id)
    //{
    //    //
    //}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function destroy($id)
    //{
    //    //
    //}
}
