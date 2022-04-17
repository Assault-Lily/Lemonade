<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CharmController extends Controller
{
    public function index(){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE{
  {
    VALUES ?predicate { schema:name schema:manufacturer schema:productID rdf:type }
    ?subject a lily:Charm;
             ?predicate ?object.
  }
  UNION
  {
    VALUES ?predicate { schema:name rdf:type }
    ?subject a lily:Corporation;
             ?predicate ?object.
  }
}
SPARQL
);
        $sparqlArray = sparqlToArray($sparql);

        $charms = array();
        $corporation = array();

        foreach ($sparqlArray as $key => $item){
            if($item['rdf:type'][0] === 'lily:Charm') $charms[$key] = $item;
            if($item['rdf:type'][0] === 'lily:Corporation') $corporation[$key] = $item;
        }

        // ソート判別
        $sortKey = request()->get('sort', 'name');
        switch ($sortKey){
            case 'name':
                $sort = 'schema:name';
                break;
            case 'name_en':
                $sort = 'schema:name@en';
                break;
            case 'manufacturer':
                $sort = 'schema:manufacturer';
                break;
            case 'product_no':
                $sort = 'schema:productID';
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

        $charm_sortKey = array();
        $charm_sortKeyForKeyUnknown = array();
        $charm_sortKeyKana = array();
        $charm_sortKeyForKanaUnknown = array();
        foreach ($charms as $charm){
            // ソート用キー配列生成
            $charm_sortKey[] = implode(',' ,$charm[$sort] ?? ['-']);
            $charm_sortKeyForKeyUnknown[] = !empty($charm[$sort][0]) ? 0 : 1;
            $charm_sortKeyKana[] = $charm['schema:name'][0] ?? '-';
            $charm_sortKeyForKanaUnknown[] = !empty($charm['schema:name'][0]) ? 0 : 1;
        }
        unset($charm);

        array_multisort($charm_sortKeyForKeyUnknown, SORT_ASC, SORT_NUMERIC,
            $charm_sortKey, $order, $sortType ?? SORT_STRING,
            $charm_sortKeyForKanaUnknown, SORT_ASC, SORT_NUMERIC,
            $charm_sortKeyKana, $order, SORT_STRING, $charms);

        $sortKey = substr(request()->get('order', 'asc'), 0, 1).'-'.$sortKey;

        return view('charm.index', compact('charms', 'corporation', 'sortKey'));
    }

    public function show($slug){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>

SELECT ?subject ?predicate ?object
WHERE{
  {
    VALUES ?subject { lilyrdf:$slug }
    ?subject ?predicate ?object.
  }
  UNION
  {
    VALUES ?rp { lily:user schema:manufacturer lily:isVariantOf lily:hasVariant }
    VALUES ?predicate { schema:name lily:charm rdf:type }
    lilyrdf:$slug ?rp ?subject.
    ?subject ?predicate ?object.
  }
  UNION
  {
    VALUES ?predicate { lily:resource lily:usedIn lily:additionalInformation }
    lilyrdf:$slug lily:user/lily:charm ?subject.
    ?subject lily:resource lilyrdf:$slug;
             ?predicate ?object.
  }
}

SPARQL
);
        $charm = sparqlToArray($sparql);

        if(empty($charm) || empty($charm['lilyrdf:'.$slug])) abort(404, "指定されたCHARMのデータが存在しません");

        return view('charm.show', compact('charm', 'slug'));
    }
}
