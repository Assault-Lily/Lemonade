<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function seriesIndex(Request $request)
    {
        $series = sparqlToArray(sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
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
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
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
        if(empty($details) || empty($details['lilyrdf:'.$series]))
            abort(404, "該当するシリーズデータが見つかりません");

        return view('anime.series', compact('details', 'series'));
    }

    public function episodeShow(string $series, string $episode, Request $request)
    {
        $details = sparqlToArray(sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE{
  {
    VALUES ?subject { lilyrdf:$episode }
    ?subject ?predicate ?object;
             schema:partOfSeries lilyrdf:$series;
             a lily:AnimeEpisode.
  }
  UNION
  {
    VALUES ?predicate {
      schema:name lily:color
    }
    lilyrdf:$episode schema:character ?subject.
    ?subject ?predicate ?object.
  }

}
SPARQL
));
        if(empty($details) || empty($details['lilyrdf:'.$episode]))
            abort(404, "該当する各話データが見つかりません");

        return view('anime.episode', compact('details', 'series', 'episode'));
    }
}
