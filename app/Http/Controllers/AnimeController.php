<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function seriesIndex(Request $request)
    {
        $series = sparqlToArray(sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE{
  ?subject ?predicate ?object;
           a lily:AnimeSeries.
}
SPARQL
));

        return view('anime.index', compact('series'));
    }

    public function seriesShow(Request $request)
    {
        //
    }
}
