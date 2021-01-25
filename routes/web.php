<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\LilyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [InfoController::class, 'index']);

Route::resource('/lily', LilyController::class, ['only' => ['index', 'show']]);

Route::get('/ed/403', [InfoController::class, 'ed403']);

Route::get('/eyecatch', function(){
    return response()->view('eyecatch');
});
