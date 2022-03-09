<?php

/**
 * SPARQL問い合わせ関数
 *
 * 設定されたSPARQLエンドポイントへクエリを発行する
 * 応答はJSON形式をデコードしたオブジェクトとして返される
 * クエリ誤りを含むエラー等は例外を返す
 *
 * @param string $query SPARQL クエリ
 * @param int $timeout タイムアウト
 * @param bool $predicateReplace 述語をPREFIXで置き換えるか
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
 * SPARQL問い合わせ関数
 *
 * 設定されたSPARQLエンドポイントへクエリを発行する
 * 応答はJSON形式をデコードしたオブジェクトとして返される
 * 例外発生時は全ての処理を中断しエラーメッセージを表示する
 * 500エラーの場合は(設定されていれば)アラートも併せて飛ばす
 *
 * @param string $query SPARQL クエリ
 * @param int $timeout タイムアウト
 * @param bool $predicateReplace 述語をプレフィックスで置き換えるか
 * @return object
 */
function sparqlQueryOrDie(string $query, int $timeout = 5, bool $predicateReplace = true): object
{
    try {
        $res = sparqlQuery($query, $timeout, $predicateReplace);
    }catch (\Illuminate\Http\Client\ConnectionException $e){
        report($e);
        Log::channel()->critical('SPARQL-EP 接続エラー'.PHP_EOL.$e->getMessage());
        $message = "SPARQLエンドポイントに接続できませんでした。\n管理者までご連絡ください。";
        abort(500, $message);
    }catch (\Illuminate\Http\Client\RequestException $e){
        if (($e->response->status() ?? 500) === 503){
            $message = "SPARQLエンドポイントがメンテナンス中か混雑しています。2分ほど待って再度お試しください。\n";
            $message .= "解消されない場合は管理者までご連絡ください。\n";
            abort(response()->view('errors.maintenance',['exception' => $e, 'message' => $message]));
        }else{
            report($e);
            $message = "SPARQLエンドポイントから無効な応答が返されました。\n";
            $message .= "通常と異なるエラーです。至急管理者までご連絡ください。\n";
            Log::channel('slack')->critical('SPARQL-EP 無効応答'.PHP_EOL.$e->getMessage());
        }
        $message .= PHP_EOL.'SPARQL endpoint returned '.$e->getCode();
        abort(500, $message);
    }
    return $res;
}

/**
 * SPARQLクエリ結果配列変換関数
 *
 * sparqlQuery()もしくはsparqlQueryOrDie()で得た結果をネストされた連想配列に変換する
 * ただし Subject(主語) Predicate(述語) Object(目的語) の順にSELECTされている必要がある
 * 言語指定がある場合日本語以外の場合に言語サフィックスをつけて返す
 *
 * @param object $sparqlObject
 * @return array
 */
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
