<?php

function sparqlQuery($query): object
{

    return Http::timeout(3)->get(config('lemonade.sparqlEndpoint'), [
        'format' => 'json',
        'query' => $query
    ])->throw()->object();

}

function sparqlQueryOrDie($query){
    try {
        $res = sparqlQuery($query);
    }catch (\GuzzleHttp\Exception\ConnectException $e){
        $message = "SPARQLエンドポイントに接続できませんでした。\n\n";
        $message .= $e->getHandlerContext()['error'];
        abort(502, $message);
    }catch (\GuzzleHttp\Exception\BadResponseException $e){
        $message = "SPARQLエンドポイントから無効な応答が返されました。\n\n";
        $message .= 'SPARQL endpoint returned '.$e->getCode();
        abort(502, $message);
    }
    return $res;
}
