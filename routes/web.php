<?php

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

Auth::routes(['register'=>false]);

Route::middleware(['auth'])->group(function() {
    $redirectDocs = function() { return redirect('/docs'); };
    Route::get('/', $redirectDocs);
    Route::get('/home', $redirectDocs);
    
    Route::get('/docs', 'DocumentosController@index');
    Route::get('/docs/status/{status}', 'DocumentosController@index');
    Route::get('/docs/ver/{documento}', 'DocumentosController@ver');
    Route::post('/docs/asignarResponsable', 'DocumentosController@asignarResponsable');
});   
