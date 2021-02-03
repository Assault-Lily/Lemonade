<?php

namespace App\Http\Controllers;

use App\Models\Lily;
use App\Models\Triple;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            $lily = Lily::whereSlug($slug)->with('triples')->firstOrFail();
            $triples = array();
            foreach ($lily->triples as $triple){
                $triples[$triple->predicate] = $triple->object;
            }
        }catch (ModelNotFoundException $e){
            abort(404, '該当するデータが存在しません');
        }

        dd($triples);
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
