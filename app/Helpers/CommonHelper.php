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
