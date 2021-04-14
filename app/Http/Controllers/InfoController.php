<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;

class InfoController extends Controller
{
    public function index(){
        try {
            $rdf_feed = simplexml_load_file(config('lemonade.rdf.repository').'/commits/deploy.atom');
        }catch (Exception $exception){
            $rdf_feed = null;
        }

        $birthday = array();
        $rdf_error = null;

        $today = Carbon::now()->format('--m-d');
        $birthday_rdf = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    ?subject a lily:Lily;
             ?predicate ?object;
             schema:birthDate ?birthDate.
    FILTER(?birthDate = "$today"^^<http://www.w3.org/2001/XMLSchema#gMonthDay>)
  }
  UNION
  {
    ?legion a lily:Legion;
            ?predicate ?object.
    BIND(?legion as ?subject)
  }
}
SPARQL
);
        $birthday = sparqlToArray($birthday_rdf);

        $lilies = array();
        $legions = array();

        // レギオンとリリィの振り分け
        foreach ($birthday as $key => $triple){
            if($triple['rdf:type'][0] === 'lily:Lily'){
                $lilies[$key] = $triple;
            }else{
                $legions[$key] = $triple;
            }
        }

        $birthday = $lilies;

        return view('main.home', compact('rdf_feed', 'birthday', 'legions'));
    }

    public function menu(){
        return view('main.menu');
    }

    public function ed403(){
        abort(403);
    }
}
