<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index(){
        return view('main.home');
    }

    public function ed403(){
        abort(403);
    }
}
