<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CharmController extends Controller
{
    public function index(){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>

SELECT DISTINCT ?subject ?predicate ?object
WHERE{
  {
    ?subject a ?type;
             ?predicate ?object.
    FILTER(?type IN(lily:Charm, lily:Corporation))
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

        ksort($charms);

        return view('charm.index', compact('charms', 'corporation'));
    }

    public function show($slug){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>

SELECT DISTINCT ?subject ?predicate ?object
WHERE{
  {
    lilyrdf:$slug a lily:Charm;
                  ?predicate ?object.
    BIND(lilyrdf:$slug as ?subject)
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
    lilyrdf:$slug a lily:Charm;
                  lily:user/lily:charm ?userData.
    ?userData ?predicate ?object.
    BIND(?userData as ?subject)
  }
}

SPARQL
);
        $charm = sparqlToArray($sparql);

        if(empty($charm) || empty($charm['lilyrdf:'.$slug])) abort(404, "指定されたCHARMのデータが存在しません");

        return view('charm.show', compact('charm', 'slug'));
    }
}
