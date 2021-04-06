<?php

namespace App\Http\Controllers;

use App\Models\Lily;
use Illuminate\Http\Request;

class LegionController extends Controller
{
    public function index(Request $request){

        $legions_sparql = sparqlQuery(<<<SPARQL
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

        $legions = array();
        foreach ($legions_sparql->results->bindings as $triple){
            if(!empty($triple->object->{'xml:lang'}) && $triple->object->{'xml:lang'} !== 'ja'){
                // 日本語以外の目的語については述語に言語サフィックスをつける
                $legions[$triple->subject->value][$triple->predicate->value.'@'.$triple->object->{'xml:lang'}][] =
                    $triple->object->value;
            }else{
                $legions[$triple->subject->value][$triple->predicate->value][] = $triple->object->value;
            }
        }

        ksort($legions);

        return view('legion.index', compact('legions', 'lilies'));
    }

    public function show($legion){
        return view();
    }
}
