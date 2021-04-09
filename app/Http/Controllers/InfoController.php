<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index(){
        try {
            $rdf_feed = simplexml_load_file(config('lemonade.rdf.repository').'/commits/deploy.atom');
        }catch (Exception $exception){
            $rdf_feed = null;
        }

        return view('main.home', compact('rdf_feed'));
    }

    public function ed403(){
        abort(403);
    }
}
