<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegionController extends Controller
{
    public function index(Request $request){

        $legions_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    VALUES ?type { lily:Legion lily:Taskforce }
    VALUES ?predicate { schema:name schema:alternateName lily:legionGrade lily:numberOfMembers lily:disbanded rdf:type }
    ?subject a ?type;
             ?predicate ?object.
  }
}
SPARQL
);

        $legions = sparqlToArray($legions_sparql);

        // ソート判別
        $sortKey = request()->get('sort', 'name');
        switch ($sortKey){
            case 'name':
                $sort = 'schema:name';
                break;
            case 'name_en':
                $sort = 'schema:name@en';
                break;
            case 'grade':
                $sort = 'lily:legionGrade';
                break;
            case 'members_count':
                $sort = 'lily:numberOfMembers';
                $sortType = SORT_NUMERIC;
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

        $legion_sortKey = array();
        $legion_sortKeyForKeyUnknown = array();
        $legion_sortKeyKana = array();
        $legion_sortKeyForKanaUnknown = array();
        foreach ($legions as $legion){
            // ソート用キー配列生成
            $legion_sortKey[] = implode(',' ,$legion[$sort] ?? ['-']);
            $legion_sortKeyForKeyUnknown[] = !empty($legion[$sort][0]) ? 0 : 1;
            $legion_sortKeyKana[] = $legion['schema:name'][0] ?? '-';
            $legion_sortKeyForKanaUnknown[] = !empty($legion['schema:name'][0]) ? 0 : 1;
        }
        unset($legion);

        array_multisort($legion_sortKeyForKeyUnknown, SORT_ASC, SORT_NUMERIC,
            $legion_sortKey, $order, $sortType ?? SORT_STRING,
            $legion_sortKeyForKanaUnknown, SORT_ASC, SORT_NUMERIC,
            $legion_sortKeyKana, $order, SORT_STRING, $legions);

        $sortKey = substr(request()->get('order', 'asc'), 0, 1).'-'.$sortKey;

        return view('legion.index', compact('legions', 'sortKey'));
    }

    public function show($legionSlug){

        $legion_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>

SELECT ?subject ?predicate ?object
WHERE {
  {
    VALUES ?subject { lilyrdf:$legionSlug }
    ?subject ?predicate ?object.
  }
  UNION
  {
    VALUES ?predicate { schema:name lily:nameKana lily:garden lily:grade lily:legion lily:legionJobTitle lily:rareSkill lily:color }
    lilyrdf:$legionSlug schema:member|schema:alumni|lily:submember ?subject.
    ?subject ?predicate ?object.
  }
  UNION
  {
    VALUES ?predicate { schema:name }
    lilyrdf:$legionSlug schema:member/lily:legion|schema:alumni/lily:legion ?subject.
    ?subject ?predicate ?object.
  }
}
SPARQL
);

        $legion = sparqlToArray($legion_sparql);

        $approve_type = ['lily:Legion', 'lily:Taskforce'];
        if (empty($legion['lilyrdf:'.$legionSlug]) or !in_array($legion['lilyrdf:'.$legionSlug]['rdf:type'][0], $approve_type)) abort(404, "指定されたレギオンのデータは現時点で存在しません");

        $icons = array();
        foreach (getImage('icon') as $icon){
            $icons['lilyrdf:'.$icon->for][] = $icon;
        }

        return view('legion.show', compact('legion', 'legionSlug', 'icons'));
    }
}
