<?php

/**
 * @param $query string SPARQL Query
 * @return object
 * @throws \Illuminate\Http\Client\RequestException
 * @throws \Illuminate\Http\Client\ConnectionException
 */
function sparqlQuery(string $query): object
{

    return Http::timeout(3)->get(config('lemonade.sparqlEndpoint'), [
        'format' => 'json',
        'query' => $query
    ])->throw()->object();

}

/**
 * @param $query string SPARQL Query
 * @return object
 */
function sparqlQueryOrDie(string $query): object
{
    try {
        $res = sparqlQuery($query);
    }catch (\Illuminate\Http\Client\ConnectionException $e){
        $message = "SPARQLエンドポイントに接続できませんでした。\n\n";
        abort(502, $message);
    }catch (\Illuminate\Http\Client\RequestException $e){
        $message = "SPARQLエンドポイントから無効な応答が返されました。\n\n";
        $message .= 'SPARQL endpoint returned '.$e->getCode();
        abort(502, $message);
    }
    return $res;
}
