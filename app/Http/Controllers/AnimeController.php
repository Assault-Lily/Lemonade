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

    public function seriesShow(string $series, Request $request)
    {
        $details = sparqlToArray(sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE{
  {
    VALUES ?subject { lilyrdf:$series }
    ?subject ?predicate ?object;
             a lily:AnimeSeries.
  }
  UNION
  {
    VALUES ?predicate {
      schema:episodeNumber lily:subtitle lily:parentheses lily:subtitleColor schema:datePublished
    }
    lilyrdf:$series schema:episode ?subject.
    ?subject ?predicate ?object.
  }

}
SPARQL
));
        if(empty($details) || empty($details['lilyrdf:'.$series]) === 0)abort(404, "該当するデータが見つかりません");

        return view('anime.series', compact('details', 'series'));
    }
}
