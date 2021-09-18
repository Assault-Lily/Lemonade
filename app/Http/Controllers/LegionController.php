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

        ksort($legions);

        return view('legion.index', compact('legions'));
    }

    public function show($legionSlug){

        $legion_sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
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
    VALUES ?predicate { schema:name lily:nameKana lily:garden lily:grade lily:legion lily:legionJobTitle lily:rareSkill }
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
        foreach (Image::where('type', 'icon')->get() as $icon){
            $icons['lilyrdf:'.$icon->for][] = $icon;
        }

        return view('legion.show', compact('legion', 'legionSlug', 'icons'));
    }
}
