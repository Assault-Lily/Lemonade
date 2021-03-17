<?php

namespace App\Http\Controllers;

use App\Models\Lily;
use App\Models\Triple;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;

class LilyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lilies = Lily::orderBy('name_y')->get();
        $triples = array();
        foreach (Triple::all() as $triple){
            $triples[$triple->lily_id][$triple->predicate] = $triple->object;
        }
        return response()->view('lily.index', compact('lilies','triples'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //public function create()
    //{
    //    //
    //}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //public function store(Request $request)
    //{
    //    //
    //}

    /**
     * Display the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            $lily = Lily::whereSlug($slug)->with('triples')->firstOrFail();
            $triples = array();
            $triples['_last_update'] = $lily->updated_at;
            foreach ($lily->triples as $triple){
                $triples['lilyrdf:'.$slug][$triple->predicate][] = $triple->object;
                if($triple->updated_at->gte($triples['_last_update'])) $triples['_last_update'] = $triple->updated_at;
            }
        }catch (ModelNotFoundException $e){
            abort(404, '該当するデータが存在しません');
        }

        $rdf_error = null;

        try {
            $triples_sparql = sparqlQuery(<<<SPARQL
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    lilyrdf:$slug ?predicate ?object.
    FILTER(!isLiteral(?object) || LANG(?object) IN ('','ja'))
    BIND(lilyrdf:$slug AS ?subject)
  }
  UNION
  {
    lilyrdf:$slug ?rp ?ro.
    FILTER(!isLiteral(?ro)).
    ?ro ?predicate ?object.
    FILTER(LANG(?object) IN ('','ja'))
    BIND(?ro as ?subject)
  }
}
SPARQL
);
            foreach ($triples_sparql->results->bindings as $triple){
                $triples[$triple->subject->value][$triple->predicate->value][] = $triple->object->value;
            }
        }catch (ConnectionException $e){
            $rdf_error = $e;
        }catch (RequestException $e){
            $rdf_error = $e;
        }

        /*foreach ($triples as $t_sub_key => $t_sub){
            foreach ($t_sub as $t_pre_key => $t_pre){
                foreach ($t_pre as $t_obj => $value){
                    if (str_starts_with($value, 'lilyrdf:')){
                        $triples[$t_sub_key][$t_pre_key][$t_obj] = $triples[$value]['schema:name'][0];
                    }
                }
            }
        }*/

        return view('lily.show', compact('lily', 'triples', 'rdf_error'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function edit($id)
    //{
    //    //
    //}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function update(Request $request, $id)
    //{
    //    //
    //}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function destroy($id)
    //{
    //    //
    //}
}
