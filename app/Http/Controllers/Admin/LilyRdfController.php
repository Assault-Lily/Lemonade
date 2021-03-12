<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class LilyRdfController extends Controller
{
    public function index(){
        return view('admin.rdf.index');
    }

    public function lily(Request $request){

        $client = new Client();
        try {
            $res = $client->get(config('lemonade.sparqlEndpoint'), [
                'timeout' => 3,
                'query' => [
                    'format' => 'json',
                    'query' => <<<SPARQL
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
                ]
            ]);
        }catch (ConnectException $e){
            $message = "SPARQLエンドポイントに接続できませんでした。\n\n";
            $message .= $e->getHandlerContext()['error'];
            abort(502, $message);
        }catch (BadResponseException $e){
            $message = "SPARQLエンドポイントから無効な応答が返されました。\n\n";
            $message .= 'SPARQL endpoint returned '.$e->getCode();
            abort(502, $message);
        }

        $lilies = json_decode($res->getBody())->results->bindings;
        //dd(json_decode($res->getBody())->results->bindings);

        return view('admin.rdf.lily-check', compact('lilies'));
    }
}
