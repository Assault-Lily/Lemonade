<?php

/**
 * @param string $query SPARQL Query
 * @param int $timeout
 * @param bool $predicateReplace Replace predicate to prefixed string
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
 * @param string $query SPARQL Query
 * @param int $timeout
 * @param bool $predicateReplace Replace predicate to prefixed string
 * @return object
 */
function sparqlQueryOrDie(string $query, int $timeout = 5, bool $predicateReplace = true): object
{
    try {
        $res = sparqlQuery($query, $timeout, $predicateReplace);
    }catch (\Illuminate\Http\Client\ConnectionException $e){
        Log::channel()->critical('SPARQL-EP 接続エラー'.PHP_EOL.$e->getMessage());
        $message = "SPARQLエンドポイントに接続できませんでした。\n管理者までご連絡ください。";
        abort(502, $message);
        exit();
    }catch (\Illuminate\Http\Client\RequestException $e){
        if (($e->response->status() ?? 500) === 503){
            $message = "SPARQLエンドポイントがメンテナンス中か混雑しています。2分ほど待って再度お試しください。\n";
            $message .= "解消されない場合は管理者までご連絡ください。\n";
            abort(response()->view('errors.maintenance',['exception' => $e, 'message' => $message]));
        }else{
            $message = "SPARQLエンドポイントから無効な応答が返されました。\n";
            $message .= "通常と異なるエラーです。至急管理者までご連絡ください。\n";
            Log::channel('slack')->critical('SPARQL-EP 無効応答'.PHP_EOL.$e->getMessage());
        }
        $message .= PHP_EOL.'SPARQL endpoint returned '.$e->getCode();
        abort(502, $message);
        exit();
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
