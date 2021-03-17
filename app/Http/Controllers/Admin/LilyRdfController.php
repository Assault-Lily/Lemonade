<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Request;
use App\Models\Lily;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class LilyRdfController extends Controller
{
    public function index(){
        return view('admin.rdf.index');
    }

    public function lily(){

        $lilies = $this->getLiliesViaSparql();

        $lilies_exists = array();
        foreach (Lily::all() as $lily){
            $lilies_exists[$lily->slug] = $lily;
        }

        return view('admin.rdf.lily-check', compact('lilies','lilies_exists'));
    }

    public function lilySync(){

        Lily::truncate();

        $lilies = $this->getLiliesViaSparql();
        $now = Carbon::now();

        $insert = array();

        foreach ($lilies as $lily){
            $insert[] = [
                'slug' => str_replace(config('lemonade.rdfPrefix.lilyrdf'),'', $lily->lily->value),
                'name' => $lily->name->value,
                'name_a' => $lily->nameen->value,
                'name_y' => $lily->namekana->value,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        Lily::insert($insert);

        return redirect(route('admin.rdf.index'))->with('message','同期が完了しました');

    }

    private function getLiliesViaSparql(){

        $res = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>

SELECT ?lily ?name ?namekana ?nameen
WHERE {
  ?lily rdf:type lily:Lily;
        schema:name ?name;
        schema:name ?nameen;
        lily:nameKana ?namekana
  FILTER(lang(?name) = 'ja')
  FILTER(lang(?nameen) = 'en')
}
SPARQL
, false);

        return $res->results->bindings;
    }
}
