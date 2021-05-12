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

        ksort($charms);

        return view('charm.index', compact('charms', 'corporation'));
    }

    public function show($slug){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE{
  {
    VALUES ?subject { lilyrdf:$slug }
    ?subject ?predicate ?object.
  }
  UNION
  {
    VALUES ?rp { lily:user schema:manufacturer lily:isVariantOf lily:hasVariant }
    VALUES ?predicate { schema:name lily:charm }
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
