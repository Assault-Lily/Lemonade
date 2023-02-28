<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Triple;
use Aws\DynamoDb\Exception\DynamoDbException;
use Carbon\Carbon;

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
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>

SELECT ?subject ?predicate ?object
WHERE {
    {
        VALUES ?predicate {
            schema:name lily:nameKana lily:givenNameKana foaf:age schema:birthDate
            lily:rareSkill lily:subSkill lily:isBoosted lily:boostedSkill
            lily:garden lily:grade lily:legion lily:legionJobTitle lily:position rdf:type lily:color
            schema:height schema:weight lily:bloodType lily:isBoosted lily:lifeStatus
        }
        ?subject a lily:Lily;
                 ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate {
            schema:name lily:nameKana lily:givenNameKana foaf:age schema:birthDate
            lily:rareSkill lily:subSkill lily:isBoosted lily:boostedSkill
            lily:garden lily:grade lily:legion lily:position rdf:type lily:color
            schema:height schema:weight lily:bloodType lily:isBoosted lily:lifeStatus
        }
        ?subject a lily:Teacher;
                 ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate {
            schema:name lily:nameKana lily:givenNameKana foaf:age schema:birthDate
            lily:rareSkill lily:subSkill lily:isBoosted lily:boostedSkill
            lily:garden lily:grade lily:legion lily:position rdf:type lily:color
            schema:height schema:weight lily:bloodType lily:isBoosted lily:lifeStatus
        }
        ?subject a lily:Character;
                 ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate {
            schema:name lily:nameKana lily:givenNameKana foaf:age schema:birthDate
            lily:rareSkill lily:subSkill lily:isBoosted lily:boostedSkill
            lily:garden lily:grade lily:legion lily:position rdf:type lily:color
            schema:height schema:weight lily:bloodType lily:isBoosted lily:lifeStatus
        }
        ?subject a lily:Madec;
                 ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name schema:alternateName rdf:type }
        ?subject a lily:Legion;
                 ?predicate ?object.
    }
}
SPQRQL
);
        $triples = sparqlToArray($triples_sparql);

        $lilies = array();
        $legions = array();

        // 表示設定
        $approvalType = ['character', 'lily', 'teacher', 'madec'];
        $approveType = explode(',',request()->get('type', 'lily'));
        if(!empty(array_diff($approveType, $approvalType))) // 許容されないタイプの検出
            abort(400 ,'表示設定の値に誤りがあります。');
        $approveType = array_map(function ($value){
            return 'lily:'.ucfirst($value);
        }, $approveType);

        // フィルタ用データリスト変数初期化
        $datalist = array();

        foreach ($triples as $key => $triple){
            if(in_array($triple['rdf:type'][0], $approveType)){
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
        $datalist['rareSkill'] = array_unique($datalist['rareSkill'] ?? array());
        $datalist['subSkill'] = array_unique($datalist['subSkill'] ?? array());
        $datalist['boostedSkill'] = array_unique($datalist['boostedSkill'] ?? array());
        $datalist['skill'] = array_merge(
            $datalist['rareSkill'] ?? array(),
            $datalist['subSkill'] ?? array(),
            $datalist['boostedSkill'] ?? array());
        $datalist['garden'] = array_unique($datalist['garden'] ?? array());

        // 特殊表示変数初期化
        $additional = array();

        // フィルタ判別
        $filterBy = request()->get('filterBy','');
        $filterInfo = array();
        if(!empty($filterBy)){
            switch ($filterBy){
                case 'skill':
                    $filterKey = 'lemonade:skill';
                    $filterInfo['key'] = 'スキル';
                    $lilies = array_map(function ($lily){ // レア・サブ・ブーステッドのマージ
                        return $lily + array('lemonade:skill' => array_merge(
                            $lily['lily:rareSkill'] ?? array(),
                            $lily['lily:subSkill'] ?? array(),
                            $lily['lily:boostedSkill'] ?? array()));
                    }, $lilies);
                    break;
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
                    $filterInfo['suffix'] = '歳';
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
                case 'bloodType':
                    $filterKey = 'lily:bloodType';
                    $filterInfo['key'] = '血液型';
                    $filterInfo['suffix'] = '型';
                    $additional = [
                        'key' => $filterKey,
                        'suffix' => '型'
                    ];
                    break;
                default:
                    abort(400, '指定されたキーではフィルタできません');
                    exit();
            }
            $filterValue = (string) request()->get('filterValue','');
            if(empty($filterValue)) abort(400, 'フィルタ値が空です');
            $filterInfo['value'] = $filterValue;
        }
        // 特殊フィルタ初期化
        $filterSP['lifeStatus'] = request()->get('lifeStatus', null);
        if(!is_null($filterSP['lifeStatus'])){
            switch ($filterSP['lifeStatus']){
                case 'alive':
                    $filterInfo['SP'][] = '生存している'; break;
                case 'dead':
                    $filterInfo['SP'][] = '殉職者・故人である'; break;
                case 'unknown':
                    $filterInfo['SP'][] = '生死不明である'; break;
                default:
                    abort(400, 'lifeStatusフィルタの値が不正です'); break;
            }
        }
        $filterSP['isBoosted']  = request()->get('isBoosted', null);
        if(!is_null($filterSP['isBoosted'])){
            switch ($filterSP['isBoosted']){
                case 'true':
                    $filterInfo['SP'][] = '強化されている'; break;
                case 'false':
                    $filterInfo['SP'][] = '強化されていない(と思われる)'; break;
                default:
                    abort(400, 'isBoostedフィルタの値が不正です'); break;
            }
        }

        // フィルタドロップ処理
        foreach ($lilies as $lilyKey => $lily){
            // キーフィルタ
            if(!empty($filterKey) && !empty($filterValue)
                && (empty($lily[$filterKey]) || array_search($filterValue, $lily[$filterKey]) === false)){
                unset($lilies[$lilyKey]);
            }
            // 特殊フィルタ
            if(!is_null($filterSP['lifeStatus']) && (empty($lily['lily:lifeStatus']) || $lily['lily:lifeStatus'][0] !== $filterSP['lifeStatus'])){
                unset($lilies[$lilyKey]);
            }
            if(!is_null($filterSP['isBoosted']) && (empty($lily['lily:isBoosted']) || $lily['lily:isBoosted'][0] !== $filterSP['isBoosted'])){
                unset($lilies[$lilyKey]);
            }
        }
        unset($lily);

        // ソート判別
        $sortKey = request()->get('sort', 'name');
        $sortingType = SORT_STRING;
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
                $sortingType = SORT_NUMERIC;
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
            case 'height':
                $sort = 'schema:height';
                $sortingType = SORT_NUMERIC;
                $additional = [
                    'key' => $sort,
                    'suffix' => 'cm'
                ];
                break;
            case 'weight':
                $sort = 'schema:weight';
                $sortingType = SORT_NUMERIC;
                $additional = [
                    'key' => $sort,
                    'suffix' => 'kg'
                ];
                break;
            case 'bloodType':
                $sort = 'lily:bloodType';
                $additional = [
                    'key' => $sort,
                    'suffix' => '型'
                ];
                break;
            case 'birthdate':
                $sort = 'schema:birthDate';
                $additional = [
                    'key' => $sort,
                    'type' => 'date'
                ];
                break;
            case 'birthdatediff':
                $sort = 'birthDateDiff';
                $sortingType = SORT_NUMERIC;
                $additional = [
                    'key' => 'schema:birthDate',
                    'type' => 'date'
                ];
                // 今日と誕生日の差を計算して array に追加する
                foreach ($lilies as $lilyKey => $lily){
                    $today = Carbon::now();
                    if (!empty($lily['schema:birthDate'][0])) {
                        // 今年の誕生日を表すインスタンスを作成
                        $birthday = Carbon::createFromFormat('Y--m-d', $today->year.$lily['schema:birthDate'][0]);

                        // もし今年の誕生日がすぎていたら、誕生日に1年足す
                        if ($birthday->isPast()) {
                            $birthday->addYear();
                        }

                        $diff = $today->diffInDays($birthday);
                        $lily['birthDateDiff'] = [$diff];
                        $lilies[$lilyKey] = $lily;
                    }
                }
                unset($lily);
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
            $lily_sortKeyForKeyUnknown[] = !empty($lily[$sort][0]) ? 0 : 1;
            $lily_sortKeyKana[] = $lily['lily:nameKana'][0] ?? '-';
            $lily_sortKeyForKanaUnknown[] = !empty($lily['lily:nameKana'][0]) ? 0 : 1;
        }
        unset($lily);
        // リリィのソート
        array_multisort($lily_sortKeyForKeyUnknown, SORT_ASC, SORT_NUMERIC,
            $lily_sortKey, $order, $sortingType,
            $lily_sortKeyForKanaUnknown, SORT_ASC, SORT_NUMERIC,
            $lily_sortKeyKana, $order, SORT_STRING, $lilies);

        $sortKey = substr(request()->get('order', 'asc'), 0, 1).'-'.$sortKey;

        // アイコンデータ取得
        $icons = array();
        foreach (getImage('icon') as $icon){
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(string $slug)
    {
        // リダイレクト判定
        if(array_key_exists($slug, config('lemonade.redirect.lily'))){
            return redirect(route('lily.show', ['lily' => config('lemonade.redirect.lily.'.$slug)]))
                ->with('message', 'このリリィはURLが変更されたため、新しいURLに自動で転送されました。');
        }

        $triples_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
    {
        VALUES ?subject { lilyrdf:$slug }
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name lily:resource lily:usedIn lily:performIn lily:additionalInformation }
        lilyrdf:$slug ?rp ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:productID schema:name rdf:type }
        lilyrdf:$slug lily:charm/lily:resource ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name rdf:type }
        lilyrdf:$slug lily:relationship/lily:resource ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { schema:name rdf:type }
        lilyrdf:$slug schema:sibling/lily:resource ?subject.
        ?subject ?predicate ?object.
    }
    UNION
    {
        VALUES ?predicate { lily:genre schema:name schema:alternateName schema:startDate rdf:type }
        lilyrdf:$slug lily:cast/lily:performIn ?subject.
        ?subject ?predicate ?object.
    }
}
SPARQL
);
        $triples = sparqlToArray($triples_sparql);

        $approve_type = ['lily:Lily', 'lily:Teacher', 'lily:Character', 'lily:Madec'];
        if(empty($triples['lilyrdf:'.$slug]['rdf:type'][0]) or !in_array($triples['lilyrdf:'.$slug]['rdf:type'][0], $approve_type) ) abort(404, '該当するリリィのデータが存在しません');

        $triples_model = Triple::whereLilySlug($slug)->get();
        foreach ($triples_model as $triple){
            $triples['lilyrdf:'.$slug][$triple->predicate][] = $triple->object;
        }

        // キャスト欄の出演作一覧を、初演日・公開日・配信開始日の速い順にソート
        foreach ($triples['lilyrdf:'.$slug]['lily:cast'] ?? array() as $cast){
            // キャスト名がない場合のスキップ
            if(empty($triples[$cast]['schema:name'][0])) continue;

            $play_sortKey = [];
            $play_sortKeyForKeyUnknown = [];
            foreach ($triples[$cast]['lily:performIn'] ?? array() as $play) {
                $startDate = $triples[$play]['schema:startDate'][0];
                if (empty($startDate)) {
                    $play_sortKey[] = NULL;
                    $play_sortKeyForKeyUnknown[] = 1;
                } else {
                    $play_sortKey[] = convertDateString($triples[$play]['schema:startDate'][0]);
                    $play_sortKeyForKeyUnknown[] = 0;
                }
            }
            array_multisort($play_sortKeyForKeyUnknown, SORT_ASC, SORT_NUMERIC, $play_sortKey, SORT_ASC, SORT_REGULAR, $triples[$cast]['lily:performIn']);
        }

        // アイコン・メモリアデータ取得
        $icons = getImage(['icon'], $slug);

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
