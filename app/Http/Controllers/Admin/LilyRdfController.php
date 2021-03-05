<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Request;

class LilyRdfController extends Controller
{
    public function index(){
        return view('admin.rdf.index');
    }

    public function check(Request $request){

        try {
            $rdf = new \SimpleXMLElement(str_replace(':','__',\request()->input('rdf')));
        }catch (\Exception $e){
            return back()->withInput()->withErrors('XML パースエラー');
        }

        $triples = [];

        foreach($rdf->rdf__Description as $lily){

            $lily_i = (string)$lily->attributes()->rdf__about;

            foreach($lily as $triple){
                $predicate = str_replace('__',':',$triple->getName());
                $predicate .= (!empty($triple->attributes()->xml__lang) ? '_'.$triple->attributes()->xml__lang : '');
                $triples[$lily_i][$predicate] = (!empty($triple[0]) ? $triple[0] : str_replace('__',':',$triple->attributes()->rdf__resource));
            }
        }

        return view('admin.rdf.check', compact('triples'));
    }
}
