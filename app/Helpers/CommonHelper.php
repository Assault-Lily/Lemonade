<?php

/**
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
 * @param string $key Prefix included string
 * @return string
 */
function removePrefix(string $key): string
{
    return explode(':', $key, 2)[1] ?? $key;
}

/**
 * @param string $grade Grade
 * @param string $prefix Grade prefix (ex: '中等部')
 * @param int|null $offset Grade manually offset
 * @param string $suffix Grade suffix (default: '年')
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
