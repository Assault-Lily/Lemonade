<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>

SELECT ?subject ?predicate ?object
WHERE {
  {
    ?subject a ?type;
             ?predicate ?object.
    FILTER(?type IN(lily:Lily, lily:Book)).
  }
}
SPARQL
);
        $sparqlArray = sparqlToArray($sparql);

        $books = array();
        $lilies = array();

        foreach ($sparqlArray as $key => $item){
            if($item['rdf:type'][0] === 'lily:Lily'){
                $lilies[$key] = $item;
            }elseif($item['rdf:type'][0] === 'lily:Book'){
                $books[$key] = $item;
            }
        }

        return view('book.index', compact('books', 'lilies'));
    }

    public function show($bookSlug){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://lily.fvhp.net/rdf/RDFs/detail/>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    lilyrdf:$bookSlug a lily:Book;
                  ?predicate ?object.
    BIND(lilyrdf:$bookSlug as ?subject)
  }
  UNION
  {
    lilyrdf:$bookSlug schema:character ?lily.
    ?lily ?predicate ?object.
    BIND(?lily as ?subject)
  }
}
SPARQL
);
        $sparqlArray = sparqlToArray($sparql);

        if(count($sparqlArray) === 0) abort(404, '該当する書籍のデータが存在しません');

        $book = array();
        $lilies = array();

        foreach ($sparqlArray as $key => $item){
            if($item['rdf:type'][0] === 'lily:Lily'){
                $lilies[$key] = $item;
            }elseif($item['rdf:type'][0] === 'lily:Book'){
                $book = $item;
            }
        }

        if(!empty($book['schema:isbn'][0])){
            $coverUrl = 'https://cover.openbd.jp/'.str_replace('-','',$book['schema:isbn'][0]).'.jpg';
            if(\Http::head($coverUrl)->successful()){
                $book['book.thumbnail'][] = $coverUrl;
            }
        }

        return view('book.show',compact('book' ,'lilies', 'bookSlug'));
    }
}