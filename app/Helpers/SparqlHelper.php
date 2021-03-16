<?php

/**
 * @param $query string SPARQL Query
 * @param $predicateReplace bool Replace predicate to prefixed string
 * @return object
 * @throws \Illuminate\Http\Client\RequestException
 * @throws \Illuminate\Http\Client\ConnectionException
 */
function sparqlQuery(string $query, bool $predicateReplace = true): object
{

    $res = Http::timeout(3)->get(config('lemonade.sparqlEndpoint'), [
        'format' => 'json',
        'query' => $query
    ])->throw()->body();

    if($predicateReplace){
        foreach (config('lemonade.rdfPrefix') as $prefix => $uri){
            $res = str_replace($uri, $prefix.':', $res);
        }
    }

    return json_decode($res);

}

/**
 * @param $query string SPARQL Query
 * @param $predicateReplace bool Replace predicate to prefixed string
 * @return object
 */
function sparqlQueryOrDie(string $query, bool $predicateReplace): object
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
