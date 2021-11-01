<?php

/**
 * @param string $type
 * @param string | array $for
 */
function getImage(string|array $type, $for = null){
    try {
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
    }catch (\Aws\DynamoDb\Exception\DynamoDbException $e){
        report($e);
        session()->now('headError', "画像データの取得に失敗しました アイコン画像表示は無効化されています\n管理者までお知らせください");
        return collect([]);
    }
}
