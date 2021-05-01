<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class LegionController extends Controller
{
    public function index(Request $request){

        $legions_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>

SELECT ?subject ?predicate ?object
WHERE {
  {
    ?subject a ?type;
             ?predicate ?object.
    FILTER(?type IN(lily:Legion, lily:Taskforce))
  }
}
SPARQL
);

        $legions = sparqlToArray($legions_sparql);

        ksort($legions);

        return view('legion.index', compact('legions'));
    }

    public function show($legionSlug){

        $legion_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    lilyrdf:$legionSlug a ?type;
                        ?predicate ?object.
    FILTER(?type IN(lily:Legion, lily:Taskforce))
    BIND(lilyrdf:$legionSlug as ?subject)
  }
  UNION
  {
    lilyrdf:$legionSlug schema:member ?member.
    ?member ?predicate ?object.
    BIND(?member as ?subject)
  }
  UNION
  {
    lilyrdf:$legionSlug schema:alumni ?alumni.
    ?alumni ?predicate ?object.
    BIND(?alumni as ?subject)
  }
  UNION
  {
    lilyrdf:$legionSlug lily:submember ?submember.
    ?submember ?predicate ?object.
    BIND(?submember as ?subject)
  }
}
SPARQL
);

        $legion = sparqlToArray($legion_sparql);

        if (empty($legion) || empty($legion['lilyrdf:'.$legionSlug])) abort(404, "指定されたレギオンのデータは現時点で存在しません");

        $icons = array();
        foreach (Image::whereType('icon')->get() as $icon){
            $icons['lilyrdf:'.$icon->for][] = $icon;
        }

        return view('legion.show', compact('legion', 'legionSlug', 'icons'));
    }
}
