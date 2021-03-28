<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Request;
use App\Models\Lily;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;

class LilyRdfController extends Controller
{
    public function index(){
        return view('admin.rdf.index');
    }

    public function lily(){

        $lilies = $this->getLiliesViaSparql();

        $lilies_exists = array();
        foreach (Lily::all() as $lily){
            $lilies_exists[$lily->slug] = $lily;
        }

        return view('admin.rdf.lily-check', compact('lilies','lilies_exists'));
    }

    public function lilySync(){

        Lily::truncate();

        $lilies = $this->getLiliesViaSparql();
        $now = Carbon::now();

        $insert = array();

        foreach ($lilies as $lily){
            $insert[] = [
                'slug' => str_replace(config('lemonade.rdfPrefix.lilyrdf'),'', $lily->lily->value),
                'name' => $lily->name->value ?? null,
                'name_a' => $lily->nameen->value ?? null,
                'name_y' => $lily->namekana->value ?? null,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        Lily::insert($insert);

        // status share to Mastodon
        if(config('lemonade.mastodon.accessToken')){
            $share_text = "【お知らせ】リリィ基本データのRDF同期が行われました。\n".
                "現在".Lily::count()."のリリィが登録されています。\n".
                config('app.url')." (".$now->format('Y/m/d G:i:s e').")";
            $client = Http::withToken(config('lemonade.mastodon.accessToken'))
                ->post(config('lemonade.mastodon.server').'/api/v1/statuses',[
                    'status' => $share_text,
                    'visibility' => config('lemonade.mastodon.tootVisibility', 'public'),
                ]);
        }

        return redirect(route('admin.rdf.index'))->with('message','同期が完了しました');

    }

    private function getLiliesViaSparql(){

        $res = sparqlQueryOrDie(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>

SELECT ?lily ?name ?nameen ?namekana
WHERE {
  ?lily a lily:Lily;
        schema:name ?name.
  FILTER(LANG(?name) = 'ja')
  OPTIONAL{ ?lily schema:name ?nameen. FILTER(LANG(?nameen) = 'en') }
  OPTIONAL{ ?lily lily:nameKana ?namekana. }
}
SPARQL
, false);

        return $res->results->bindings;
    }
}
