<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>

SELECT ?subject ?predicate ?object
WHERE {
  {
    ?subject a lily:Book;
             ?predicate ?object.
  }
}
SPARQL
);
        $books = sparqlToArray($sparql);
        return view('book.index', compact('books'));
    }

    public function show($bookSlug){
        $sparql = sparqlQueryOrDie(<<<SPARQL
PREFIX lily: <https://luciadb.assaultlily.com/rdf/IRIs/lily_schema.ttl#>
PREFIX lilyrdf: <https://luciadb.assaultlily.com/rdf/RDFs/detail/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX schema: <http://schema.org/>

SELECT ?subject ?predicate ?object
WHERE {
  {
    VALUES ?subject { lilyrdf:$bookSlug }
    ?subject ?predicate ?object.
  }
  UNION
  {
    VALUES ?predicate { schema:name lily:legion lily:garden lily:grade rdf:type }
    lilyrdf:$bookSlug schema:character ?subject.
    ?subject ?predicate ?object.
  }
}
SPARQL
);
        $sparqlArray = sparqlToArray($sparql);

        if(empty($sparqlArray) || empty($sparqlArray['lilyrdf:'.$bookSlug])) abort(404, '該当する書籍のデータが存在しません');

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
