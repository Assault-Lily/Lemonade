<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayController extends Controller
{
    public function index(){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE{
  {
    ?subject a lily:Play;
             ?predicate ?object.
  }
}

SPARQL
);
        $plays = sparqlToArray($sparql);

        $startDateKey = array();
        foreach ($plays as $play){
            $startDateKey[] = $play['schema:startDate'][0] ?? '9999-99-99';
        }

        array_multisort($startDateKey, SORT_DESC, SORT_STRING, $plays);

        return view('play.index', compact('plays'));
    }

    public function show($playSlug){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE{
  {
    VALUES ?subject { lilyrdf:$playSlug }
    ?subject a lily:Play;
             ?predicate ?object.
  }
  UNION
  {
    lilyrdf:$playSlug lily:cast ?subject.
    ?subject ?predicate ?object.
  }
  UNION
  {
    VALUES ?predicate { schema:name rdf:type }
    lilyrdf:$playSlug lily:cast/lily:performAs ?subject.
    ?subject ?predicate ?object.
  }
}

SPARQL
);
        $play = sparqlToArray($sparql);

        if(empty($play) || empty($play['lilyrdf:'.$playSlug]) === 0)abort(404, "該当する公演データが見つかりません");

        return view('play.show', compact('play', 'playSlug'));
    }
}
