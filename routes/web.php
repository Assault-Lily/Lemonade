<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ImageDataController;
use App\Http\Controllers\Admin\LilyDataController;
use App\Http\Controllers\Admin\LilyRdfController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\TripleDataController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CharmController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\LegionController;
use App\Http\Controllers\LilyController;
use App\Http\Controllers\OGPController;
use App\Http\Controllers\PlayController;
use App\Http\Controllers\QRController;
use Illuminate\Support\Facades\Route;

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

Route::get('/menu', [InfoController::class, 'menu'])->name('menu');

Route::get('/imedic', [InfoController::class, 'generateImeDic'])->name('imedic');

Route::get('/rdfDescribe/{resource}', [InfoController::class, 'rdfDescribe'])->name('rdfDescribe');

Route::middleware('throttle:30,1')->group(function (){
    Route::get('/ogp/{type}/{title}.jpg', [OGPController::class, 'generate'])->name('ogp');
});

Route::get('/qr', [QRController::class, 'generate'])->name('qr');

Route::resource('/lily', LilyController::class, ['only' => ['index','show']]);
Route::resource('/legion', LegionController::class, ['only' => ['index', 'show']]);
Route::resource('/book', BookController::class, ['only' => ['index', 'show']]);
Route::resource('/play', PlayController::class, ['only' => ['index', 'show']]);
Route::resource('/charm', CharmController::class, ['only' => ['index', 'show']]);

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function (){
    Route::get('/', [AdminController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');

    require __DIR__.'/auth.php';

    Route::resource('/lily', LilyDataController::class);

    Route::resource('/triple', TripleDataController::class);

    Route::get('/image/createByJson', [ImageDataController::class, 'createByJson'])->name('image.createByJson');
    Route::get('/image/bulkUpdate', [ImageDataController::class, 'bulkUpdate'])->name('image.bulkUpdate');
    Route::patch('/image/bulkUpdate', [ImageDataController::class, 'bulkUpdateExec'])->name('image.bulkUpdateExec');
    Route::post('/image/storeJson', [ImageDataController::class, 'storeJson'])->name('image.storeJson');
    Route::resource('/image', ImageDataController::class);

    Route::resource('/notice', NoticeController::class);

    Route::get('/rdf',  [LilyRdfController::class, 'index'])->name('rdf.index');
    Route::get('/rdf/lily', [LilyRdfController::class, 'lily'])->name('rdf.lily');
    Route::post('/rdf/lily',[LilyRdfController::class, 'lilySync'])->name('rdf.lilySync');


});

Route::get('/ed/403', [InfoController::class, 'ed403']);
