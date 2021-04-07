<?php

namespace App\Http\Controllers;

use App\Models\Lily;
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
    ?subject a lily:Legion;
             ?predicate ?object
  }
}
SPARQL
);

        $lilies = array();
        foreach (Lily::all() as $lily){
            $lilies[$lily->slug] = $lily;
        }

        $legions = sparqlToArray($legions_sparql);

        ksort($legions);

        return view('legion.index', compact('legions', 'lilies'));
    }

    public function show($legionSlug){

        $legion_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    lilyrdf:$legionSlug ?predicate ?object.
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

        $lilies = array();
        foreach (Lily::all() as $lily){
            $lilies[$lily->slug] = $lily;
        }

        if (empty($legion)) abort(404, "指定されたレギオンのデータは現時点で存在しません");

        return view('legion.show', compact('legion', 'lilies', 'legionSlug'));
    }
}
