<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Carbon\Carbon;
use Exception;
use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MainController extends Controller
{
    public function index(){
        try {
            // LuciaDB 更新情報の取得
            $rdf_feed = simplexml_load_file(config('lemonade.rdf.repository').'/commits/deploy.atom');

            // LuciaDB から (rdf:type, schema:name@ja) のペアを取得
            $name_list_rdf = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  VALUES ?predicate { schema:name rdf:type }
  VALUES ?type { lily:Lily lily:Teacher lily:Madec lily:Character lily:Legion lily:Taskforce lily:Charm lily:Book lily:Play lily:AnimeSeries }
  ?subject ?predicate ?object ;
           a          ?type .
  FILTER(!isLiteral(?object) || lang(?object) = "ja")
  FILTER(!isBlank(?subject))
}ORDER BY DESC(strlen(?object))
SPARQL
);
            $name_list = sparqlToArray($name_list_rdf);

            // 更新情報エントリのタイトル中に LuciaDB に schema:name として登録されている文字列があったら、その部分をLemonadeの詳細ページへのリンクに置換する
            foreach ($rdf_feed->entry as $entry) {
                foreach ($name_list as $name_key => $name_item) {
                    $name_temp = $name_item['schema:name'][0];
                    if ($name_temp === "L") continue;
                    if (strpos($entry->title, $name_temp)) {
                        $slug = str_replace('lilyrdf:', '', $name_key);
                        switch ($name_item['rdf:type'][0]) {
                            case "lily:Lily":
                            case "lily:Teacher":
                            case "lily:Madec":
                            case "lily:Character":
                                $path = "/lily/{$slug}";
                                break;
                            case "lily:Legion":
                            case "lily:Taskforce":
                                $path = "/legion/{$slug}";
                                break;
                            case "lily:Charm":
                                //誤ったリンクを防ぐため、置換する文字列を変化させる
                                $name_temp = preg_replace("/^.{0,1}+\K/us", "\u{2020}", $name_temp);
                                $path = "/charm/{$slug}";
                                break;
                            case "lily:Book":
                                $path = "/book/{$slug}";
                                break;
                            case "lily:Play":
                                $path = "/play/{$slug}";
                                break;
                            case "lily:AnimeSeries":
                                $path = "/anime/{$slug}";
                                break;
                            default:
                                break;
                        }
                        $a_tag = "<a href={$path}>{$name_temp}</a>";
                        $entry->title = mb_ereg_replace($name_item['schema:name'][0], $a_tag, $entry->title);
                    }
                }
                //置換終了後の文字列は戻しておく
                $entry->title = str_replace("\u{2020}", "", $entry->title);
            }
        } catch (Exception $exception){
            $rdf_feed = null;
        }

        $today = Carbon::now()->format('--m-d');
        $birthday_rdf = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    ?subject a ?type;
             ?predicate ?object;
             schema:birthDate "$today"^^<http://www.w3.org/2001/XMLSchema#gMonthDay>.
    FILTER(?type IN(lily:Lily, lily:Teacher, lily:Madec, lily:Character))
  }
  UNION
  {
    ?subject a lily:Legion;
            ?predicate ?object.
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
            if(in_array($triple['rdf:type'][0], ['lily:Lily', 'lily:Teacher', 'lily:Madec', 'lily:Character'])){
                $lilies[$key] = $triple;
                // アイコンを取得するリリィのリスト
                $image_pull_list[] = removePrefix($key);
            }else{
                $legions[$key] = $triple;
            }
        }

        // アイコン取得
        if(!empty($image_pull_list)){
            foreach (getImage('icon', $image_pull_list) as $image){
                $images['lilyrdf:'.$image->for][] = $image;
            }
        }

        // お知らせ取得
        $notices = Notice::whereImportance('100')->orderBy('updated_at')->get();

        $birthday = $lilies;

        return view('main.home', compact('rdf_feed', 'name_list', 'birthday', 'legions', 'images', 'notices'));
    }

    public function menu(){
        return view('main.menu');
    }

    public function generateImeDic(){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?name ?nameKana ?givenName ?givenNameKana ?garden ?legion ?type
WHERE {
  {
    ?subject schema:name ?name;
             lily:nameKana ?nameKana;
             schema:givenName ?givenName;
             lily:givenNameKana ?givenNameKana;
             a ?type.
    OPTIONAL{ ?subject lily:garden ?garden. }
    OPTIONAL{ ?subject lily:legion/schema:name ?legion. FILTER(LANG(?legion) = 'ja') }
    FILTER(LANG(?name) = 'ja')
    FILTER(LANG(?givenName) = 'ja')
    FILTER(?type IN(lily:Lily, lily:Madec, lily:Teacher, lily:Character))
  }
}
SPARQL
);
        $sparqlArray = array();
        $types = [
            'Lily' => 'リリィ',
            'Madec' => 'マディック',
            'Teacher' => '教導官',
            'Character' => 'その他人物',
        ];
        foreach ($sparql->results->bindings as $line){
            $sparqlArray[$line->subject->value] = [
                'name' => $line->name->value,
                'nameKana' => str_replace('・', '', $line->nameKana->value),
                'givenName' => $line->givenName->value,
                'givenNameKana' => $line->givenNameKana->value,
                'garden' => $line->garden->value ?? '',
                'legion' => $line->legion->value ?? '',
                'type' => $types[removePrefix($line->type->value)] ?? '登場人物'
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
    <dict>
        <key>phrase</key>
        <string>{$line['name']}</string>
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

        if(request()->get('format','') === 'dctx'){

            $dictionaryGUID = config('lemonade.dictionaryGUID');
            if(empty($dictionaryGUID))
                abort(500, '辞書生成に必要なGUIDが設定されていません。');

            $softwareInfo = [
                'appRepository' => config('lemonade.repository'),
                'rdfRepository' => config('lemonade.rdf.repository'),
                'dicRoute' => route('imedic'),
                'dicVersion' => now()->format('ym'),
                'copyright' => 'Copyright'
            ];

            $dctx = <<<DCTX
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<ns1:Dictionary xmlns:ns1="http://www.microsoft.com/ime/dctx">
	<ns1:DictionaryHeader>
		<ns1:DictionaryGUID>$dictionaryGUID</ns1:DictionaryGUID>
		<ns1:DictionaryLanguage>ja-jp</ns1:DictionaryLanguage>
		<ns1:DictionaryVersion>{$softwareInfo['dicVersion']}</ns1:DictionaryVersion>
		<ns1:CommentInsertion>true</ns1:CommentInsertion>
		<ns1:DictionaryInfo Language="ja-jp">
		    <ns1:ShortName>アサルトリリィ辞書</ns1:ShortName>
			<ns1:LongName>アサルトリリィオープン拡張辞書</ns1:LongName>
			<ns1:Description>Dataset powered by LuciaDB
{$softwareInfo['rdfRepository']}

Dictionary generated by Lemonade
{$softwareInfo['dicRoute']}

Original dataset is released under the Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
https://creativecommons.org/licenses/by-nc-sa/4.0/</ns1:Description>
			<ns1:Copyright>Data curation : fvh-P and contributors.
Original works : AZONE INTERNATIONAL and acus</ns1:Copyright>
			<ns1:CommentHeader1>称</ns1:CommentHeader1>
			<ns1:CommentHeader2>所属レギオン</ns1:CommentHeader2>
			<ns1:CommentHeader3>所属ガーデン</ns1:CommentHeader3>
		</ns1:DictionaryInfo>
		<ns1:SourceURL>{$softwareInfo['dicRoute']}</ns1:SourceURL>
	</ns1:DictionaryHeader>
DCTX;
            foreach ($sparqlArray as $line){
                $lineDic = [
                    'legion' => 'LG'.($line['legion'] ?: '情報なし'),
                    'garden' => $line['garden'] ?: 'ガーデン情報なし',
                ];
                $dctx .= <<<DCTX
	<ns1:DictionaryEntry>
		<ns1:PartOfSpeech>Name-Personal</ns1:PartOfSpeech>
		<ns1:OutputString>{$line['name']}</ns1:OutputString>
		<ns1:InputString>{$line['nameKana']}</ns1:InputString>
		<ns1:CommentData1>{$line['type']}</ns1:CommentData1>
		<ns1:CommentData2>{$lineDic['legion']}</ns1:CommentData2>
		<ns1:CommentData3>{$lineDic['garden']}</ns1:CommentData3>
		<ns1:Priority>0</ns1:Priority>
		<ns1:ReverseConversion>true</ns1:ReverseConversion>
		<ns1:CommonWord>true</ns1:CommonWord>
	</ns1:DictionaryEntry>
    <ns1:DictionaryEntry>
		<ns1:PartOfSpeech>Name-Given</ns1:PartOfSpeech>
		<ns1:OutputString>{$line['givenName']}</ns1:OutputString>
		<ns1:InputString>{$line['givenNameKana']}</ns1:InputString>
		<ns1:CommentData1>{$line['type']}</ns1:CommentData1>
		<ns1:CommentData2>{$lineDic['legion']}</ns1:CommentData2>
		<ns1:CommentData3>{$lineDic['garden']}</ns1:CommentData3>
		<ns1:Priority>0</ns1:Priority>
		<ns1:ReverseConversion>true</ns1:ReverseConversion>
		<ns1:CommonWord>true</ns1:CommonWord>
	</ns1:DictionaryEntry>
	<ns1:DictionaryEntry>
		<ns1:PartOfSpeech>ShortCut</ns1:PartOfSpeech>
		<ns1:OutputString>{$line['name']}</ns1:OutputString>
		<ns1:InputString>{$line['givenNameKana']}</ns1:InputString>
		<ns1:CommentData1>{$line['type']}</ns1:CommentData1>
		<ns1:CommentData2>{$lineDic['legion']}</ns1:CommentData2>
		<ns1:CommentData3>{$lineDic['garden']}</ns1:CommentData3>
		<ns1:Priority>0</ns1:Priority>
		<ns1:ReverseConversion>true</ns1:ReverseConversion>
		<ns1:CommonWord>true</ns1:CommonWord>
	</ns1:DictionaryEntry>
DCTX;
            }
            $dctx .= <<<DCTX
</ns1:Dictionary>
DCTX;
            return response($dctx)->withHeaders([
                'Content-Type' => 'text/xml'
            ]);
        }

        $dicString = "";
        foreach ($sparqlArray as $line){
            $comment = $line['type'].' LG'.($line['legion'] ?: '情報なし').' '.($line['garden'] ?: 'ガーデン情報なし');
            $dicString .= str_replace('・','',$line['nameKana'])."\t".$line['name']."\t人名\t".$comment.PHP_EOL;
            $dicString .= $line['givenNameKana']."\t".$line['givenName']."\t名\t".$comment.PHP_EOL;
            $dicString .= $line['givenNameKana']."\t".$line['name']."\t短縮よみ\t".$comment.PHP_EOL;
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
            return response()->download($name, 'assaultlily-dic.zip')->deleteFileAfterSend(true);
        }

        return view('main.imedic', compact('dicString'));
    }

    public function rdfDescribe(string $resource = null){
        if (is_null($resource)) abort(400, "リソースが指定されていません");

        $turtle = http::get(config('lemonade.sparqlEndpoint'), [
            'format' => 'turtle',
            'query' => <<<SPARQL
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
DESCRIBE lilyrdf:$resource
SPARQL
        ])->throw(function ($response, $e){
            report($e);
            abort(502, "SPARQLエンドポイントが正しく応答しませんでした。");
        })->body();

        if(mb_strlen(trim(preg_replace('/^@prefix.+$/im','',$turtle))) === 0)
            abort(404, "該当するリソースが存在しません");

        return view('main.rdfDescribe', compact('turtle', 'resource'));
    }

    public function queryEditor(Request $request) {
        $query = $request->input('query') ?? <<<SPARQL
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>

SELECT ?name
WHERE {
    ?lily rdf:type lily:Lily;
          lily:nameKana ?namekana.
    FILTER(CONTAINS(?namekana, "あ"))
    ?lily schema:name ?name.
    FILTER(lang(?name) = "ja")
}

SPARQL;
        return view('main.queryEditor', compact('query'));
    }

    public function ed403(){
        abort(403);
    }
}
