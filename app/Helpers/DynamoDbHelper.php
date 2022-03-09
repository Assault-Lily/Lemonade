<?php

/**
 * DynamoDB 画像取得関数
 *
 * DynamoDB
 *
 * @param string | array $type 取得画像種別(icon等) 配列指定可
 * @param string | array $for 対象リソースネーム 配列指定可
 */
function getImage(string|array $type, $for = null){
    if(config('dynamodb.disable')){
        session()->now('headError', "アイコン画像表示が一時的に無効化されています\n復旧までお待ち下さい");
        return collect([]);
    }else{
        try {
            /* あんまり意味ない気がしてきた
             * if(config('dynamodb.default') === 'production'){
                $endpoint = 'https://dynamodb.'.config('dynamodb.connections.production.region').'.amazonaws.com';
            }else{
                $endpoint = config('dynamodb.connections.'.config('dynamodb.default').'.endpoint');
            }
            Http::timeout(config('dynamodb.defaultTimeout', 2))->get($endpoint)->throw();*/

            if(is_array($type)){
                $return = \App\Models\Image::whereIn('type', $type);
            }else{
                $return = \App\Models\Image::where('type', $type);
            }
            if(!empty($for) && is_array($for)){
                $return = $return->whereIn('for', $for);
            }elseif(!empty($for)){
                $return = $return->where('for', $for);
            }
            return $return->get();
        }catch (\Aws\DynamoDb\Exception\DynamoDbException|\Illuminate\Http\Client\ConnectionException $e){
            report($e);
            session()->now('headError', "画像データの取得に失敗しました アイコン画像表示は無効化されています\n管理者までお知らせください");
            return collect([]);
        }
    }
}
