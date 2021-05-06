<?php

/**
 * @param $query string SPARQL Query
 * @param $predicateReplace bool Replace predicate to prefixed string
 * @return object
 * @throws \Illuminate\Http\Client\RequestException
 * @throws \Illuminate\Http\Client\ConnectionException
 */
function sparqlQuery(string $query, int $timeout = 5, bool $predicateReplace = true): object
{

    $res = Http::timeout($timeout)->get(config('lemonade.sparqlEndpoint'), [
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
function sparqlQueryOrDie(string $query, int $timeout = 5, bool $predicateReplace = true): object
{
    try {
        $res = sparqlQuery($query, $timeout, $predicateReplace);
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

function sparqlToArray(object $sparqlObject): array
{
    $result = array();
    foreach ($sparqlObject->results->bindings as $triple){
        if(!empty($triple->object->{'xml:lang'}) && $triple->object->{'xml:lang'} !== 'ja'){
            // 日本語以外の目的語については述語に言語サフィックスをつける
            $result[$triple->subject->value][$triple->predicate->value.'@'.$triple->object->{'xml:lang'}][] =
                $triple->object->value;
        }else{
            $result[$triple->subject->value][$triple->predicate->value][] = $triple->object->value;
        }
    }
    return $result;
}
