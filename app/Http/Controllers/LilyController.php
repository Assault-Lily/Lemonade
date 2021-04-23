<?php

namespace App\Http\Controllers;

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
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  ?subject a ?type;
           ?predicate ?object.
  FILTER(?type IN(lily:Lily, lily:Legion))
}
SPQRQL
);
        $triples = sparqlToArray($triples_sparql);

        $lilies = array();
        $legions = array();

        // レギオンとリリィの振り分け
        foreach ($triples as $key => $triple){
            if($triple['rdf:type'][0] === 'lily:Lily'){
                $lilies[$key] = $triple;
            }else{
                $legions[$key] = $triple;
            }
        }

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

        // リリィソート用キー配列の作成
        $lily_sortKey = array();
        foreach ($lilies as $lily){
            $lily_sortKey[] = implode(',' ,$lily[$sort] ?? ['-']);
        }
        unset($lily);
        $lily_sortKeyKana = array();
        foreach ($lilies as $lily){
            $lily_sortKeyKana[] = $lily['lily:nameKana'][0] ?? '-';
        }
        // リリィのソート
        array_multisort($lily_sortKey, $order, SORT_STRING,
            $lily_sortKeyKana, $order, SORT_STRING , $lilies);

        $sortKey = substr(request()->get('order', 'asc'), 0, 1).'-'.$sortKey;

        return response()->view('lily.index', compact('lilies', 'legions', 'sortKey', 'filterInfo', 'additional'));
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
    lilyrdf:$slug ?predicate ?object.
    BIND(lilyrdf:$slug AS ?subject)
  }
  UNION
  {
    lilyrdf:$slug ?rp ?ro.
    FILTER(!isLiteral(?ro)).
    ?ro ?predicate ?object.
    BIND(?ro as ?subject)
  }
  UNION
  {
    lilyrdf:$slug lily:charm/lily:resource ?charm.
    ?charm ?cp ?co.
    BIND(?charm as ?subject)
    BIND(?cp as ?predicate)
    BIND(?co as ?object)
  }
  UNION
  {
    lilyrdf:$slug lily:relationship/lily:resource ?rel.
    ?rel ?relp ?relo.
    BIND(?rel as ?subject)
    BIND(?relp as ?predicate)
    BIND(?relo as ?object)
  }
  UNION
  {
    lilyrdf:$slug schema:sibling/lily:resource ?sib.
    ?sib ?sibp ?sibo.
    BIND(?sib as ?subject)
    BIND(?sibp as ?predicate)
    BIND(?sibo as ?object)
  }
}
SPARQL
);
        $triples = sparqlToArray($triples_sparql);

        if(empty($triples)) abort(404, '該当するリリィのデータが存在しません');

        $triples_model = Triple::whereLilySlug($slug)->get();
        foreach ($triples_model as $triple){
            $triples['lilyrdf:'.$slug][$triple->predicate][] = $triple->object;
        }

        return view('lily.show', compact('triples', 'slug'));
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
