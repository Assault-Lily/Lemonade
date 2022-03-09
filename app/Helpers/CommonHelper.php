<?php

/**
 * 日付変換関数
 *
 * YYYY-MM-DD形式の日付からCarbonインスタンスを返す
 * 西暦年の指定がない(--MM-DD)場合2005年と仮定しインスタンスを返す
 * RDFが応答する誕生日等の日付情報用
 *
 * @param string $string
 * @return \Carbon\Carbon|false|null
 */
function convertDateString(string $string){
    if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $string)){
        return \Carbon\Carbon::make($string);
    }else if(preg_match('/--\d{2}-\d{2}/', $string)){
        return \Carbon\Carbon::make('2005-'.mb_substr($string,2));
        // Why 2005? -> see https://hobby.dengeki.com/news/953396
    }else{
        return false;
    }
}

/**
 * プレフィックス除去関数
 *
 * RDFの述語等のプレフィックスを除去する
 * 文字列の最初のコロン(:)までを判定し除去する
 *
 * @param string $key Prefix included string
 * @return string
 */
function removePrefix(string $key): string
{
    return explode(':', $key, 2)[1] ?? $key;
}

/**
 * 学年文字列変換関数
 *
 * 小学校から通しで登録された学年を6334制に相当する学年に変換し文字列として返す
 * デフォルトでは接尾辞に”年”、初等部及び中等部に相当する学年ではそれを接頭辞につけて返すが、
 * 接尾辞と接頭辞は引数によりコントロール可能である。
 * 必要であればオフセットを明示的に指定することができる。
 *
 * @param string $grade 学年(数字のみで構成される文字列)
 * @param string $prefix 学年接頭辞 (ex: '中等部')
 * @param int|null $offset 学年手動オフセット
 * @param string $suffix 学年接尾辞 (default: '年')
 * @return string
 */
function convertGradeString(string $grade, string $prefix = '', int $offset = null, string $suffix = '年'): string
{
    if(is_null($offset)){ // オフセット未セット時
        if($grade <= 6){
            $prefix = $prefix ?: '初等部';
        }
        if(7 <= $grade && $grade <= 9){ // 中等部
            $prefix = $prefix ?: '中等部';
            $grade -= 6;
        }
        elseif(10 <= $grade && $grade <= 12){ // 高等部
            $grade -= 9;
        }
    }
    return $prefix.($grade + $offset).$suffix;
}
