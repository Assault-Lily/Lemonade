<?php

return [

    /* Application Version */
    'version' => '2-prt Branch-RDF',

    'mastodon' => 'https://mstdn.miyacorata.net/@assaultlily',
    'developer' => [
        'twitter' => 'miyacorata',
        'github' => 'miyacorata'
    ],
    'repository' => 'https://github.com/miyacorata/lemonade',

    'officialUrls' => [
        'acus' => 'https://www.assaultlily.com/character/{no}.html/',
        'anime' => 'https://anime.assaultlily-pj.com/character/{slug}',
        'lb' => 'https://assaultlily.bushimo.jp/character/{slug}/',
    ],

    'fumi' => [
        'twitter' => 'assault_lily',
    ],

    'sparqlEndpoint' => env('SPARQL_ENDPOINT','http://localhost:3030'),

    'rdfPrefix' => [
        'schema' => 'http://schema.org/',
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
        'lily' => 'https://lily.fvhp.net/rdf/IRIs/lily_schema.ttl#',
        'lilyrdf' => 'https://lily.fvhp.net/rdf/RDFs/detail/'
    ],
];
