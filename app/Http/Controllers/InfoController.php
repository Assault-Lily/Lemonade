<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Carbon\Carbon;
use Exception;
use ZipArchive;

class InfoController extends Controller
{
    public function index(){
        try {
            $rdf_feed = simplexml_load_file(config('lemonade.rdf.repository').'/commits/deploy.atom');
        }catch (Exception $exception){
            $rdf_feed = null;
        }

        $today = Carbon::now()->format('--m-d');
        $birthday_rdf = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    ?subject a lily:Lily;
             ?predicate ?object;
             schema:birthDate ?birthDate.
    FILTER(?birthDate = "$today"^^<http://www.w3.org/2001/XMLSchema#gMonthDay>)
  }
  UNION
  {
    ?legion a lily:Legion;
            ?predicate ?object.
    BIND(?legion as ?subject)
  }
}
SPARQL
);
        $birthday = sparqlToArray($birthday_rdf);

        $lilies = array();
        $legions = array();
        $image_pull_list = array();
        $images = array();

        // レギオンとリリィの振り分け
        foreach ($birthday as $key => $triple){
            if($triple['rdf:type'][0] === 'lily:Lily'){
                $lilies[$key] = $triple;
                // アイコンを取得するリリィのリスト
                $image_pull_list[] = str_replace('lilyrdf:','',$key);
            }else{
                $legions[$key] = $triple;
            }
        }

        // アイコン取得
        foreach (Image::whereType('icon')->whereIn('for',$image_pull_list)->get() as $image){
            $images['lilyrdf:'.$image->for][] = $image;
        }

        $birthday = $lilies;

        return view('main.home', compact('rdf_feed', 'birthday', 'legions', 'images'));
    }

    public function menu(){
        return view('main.menu');
    }

    public function generateImeDic(){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?name ?nameKana ?givenName ?givenNameKana ?garden ?legion
WHERE {
  {
    ?subject schema:name ?name;
             lily:nameKana ?nameKana;
             schema:givenName ?givenName;
             lily:givenNameKana ?givenNameKana;
             a lily:Lily.
    OPTIONAL{ ?subject lily:garden ?garden. }
    OPTIONAL{ ?subject lily:legion/schema:name ?legion. FILTER(LANG(?legion) = 'ja') }
    FILTER(LANG(?name) = 'ja')
    FILTER(LANG(?givenName) = 'ja')
  }
}
SPARQL
);
        $sparqlArray = array();
        foreach ($sparql->results->bindings as $line){
            $sparqlArray[$line->subject->value] = [
                'name' => $line->name->value,
                'nameKana' => $line->nameKana->value,
                'givenName' => $line->givenName->value,
                'givenNameKana' => $line->givenNameKana->value,
                'garden' => $line->garden->value ?? '',
                'legion' => $line->legion->value ?? '',
            ];
        }
        unset($line);

        if(request()->get('format','') === 'plist'){
            $plist = <<<PLIST
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<array>

PLIST;
            foreach ($sparqlArray as $line){
                $plist .= <<<PLIST
    <dict>
        <key>phrase</key>
        <string>{$line['name']}</string>
        <key>shortcut</key>
        <string>{$line['nameKana']}</string>
    </dict>
    <dict>
        <key>phrase</key>
        <string>{$line['givenName']}</string>
        <key>shortcut</key>
        <string>{$line['givenNameKana']}</string>
    </dict>

PLIST;
            }
            $plist .= <<<PLIST
</array>
</plist>
PLIST;

            return response($plist)->withHeaders([
                'Content-Type' => 'text/xml'
            ]);
        }

        $dicString = "";
        foreach ($sparqlArray as $line){
            $comment = 'リリィ LG'.($line['legion'] ?: '情報なし').' '.($line['garden'] ?: 'ガーデン情報なし');
            $dicString .= str_replace('・','',$line['nameKana'])."\t".$line['name']."\t人名\t".$comment.PHP_EOL;
            $dicString .= $line['givenNameKana']."\t".$line['givenName']."\t名\t".$comment.PHP_EOL;
        }
        $dicString = trim($dicString);

        if(request()->get('format','') === 'txt'){
            return response($dicString)->withHeaders([
                'Content-Type' => 'text/plain'
            ]);
        }

        if(request()->get('format','') === 'txt-sjis'){
            return response(mb_convert_encoding($dicString, 'SJIS'))->withHeaders([
                'Content-Type' => 'text/plain;charset=shift_jis'
            ]);
        }

        if(request()->get('format','') === 'zip'){
            $name = tempnam(sys_get_temp_dir(), 'AL_');
            $zip = new ZipArchive();
            if($zip->open($name, ZipArchive::OVERWRITE) === true){
                $zip->addFromString('assaultlily-dic.txt', $dicString);
                $zip->close();
            }else{
                abort(500, 'ZIPアーカイブの作成に失敗しました');
            }
            return response()->download($name, 'assaultlily-dic.zip');
        }

        return view('main.imedic', compact('dicString'));
    }

    public function ed403(){
        abort(403);
    }
}
