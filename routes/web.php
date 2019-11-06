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
    Route::redirect('/', '/docs');
    Route::redirect('/home', '/docs');
    
    Route::get('/docs', 'DocumentosController@index');
    Route::get('/docs/status/{status}', 'DocumentosController@index');
    Route::get('/docs/ver/{documento}', 'DocumentosController@ver');
    Route::post('/docs/crear', 'DocumentosController@guardar');
    Route::get('/docs/crear', 'DocumentosController@crear');
    Route::post('/docs/{documento}/asignarResponsable', 'DocumentosController@asignarResponsable');
    Route::post('/docs/{documento}/agregarPropuesta', 'DocumentosController@agregarPropuesta');
    Route::post('/docs/{propuesta}/rechazarPropuesta', 'DocumentosController@rechazarPropuesta');
    Route::post('/docs/{propuesta}/aceptarPropuesta', 'DocumentosController@aceptarPropuesta');
    Route::post('/docs/{documento}/corregir', 'DocumentosController@corregir');
    Route::post('/docs/{documento}/verificar', 'DocumentosController@verificar');
    Route::post('/docs/{documento}/cerrar', 'DocumentosController@cerrar');

    Route::middleware(['role:admin'])->group(function() {
        Route::resource('/admin/users', 'Admin\UsersController');
        Route::post('/admin/users/{user}/delRole/{role}',
            'Admin\UsersController@delRole');
        Route::post('/admin/users/{user}/addRole',
            'Admin\UsersController@addRole');
        Route::post('/admin/users/{user}/delDepartamento/{departamento}',
            'Admin\UsersController@delDepartamento');
        Route::post('/admin/users/{user}/addDepartamento',
            'Admin\UsersController@addDepartamento');

    });
});   
