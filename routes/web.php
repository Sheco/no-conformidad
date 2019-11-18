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
    
    Route::get ('docs', 'DocumentosController@index');
    Route::get ('docs/status', 'DocumentosController@index');
    Route::get ('docs/status/{status}', 'DocumentosController@index');
    Route::get ('docs/filtros', 'DocumentosController@filtros');
    Route::post('docs/filtros', 'DocumentosController@filtrosGuardar');
    Route::post('docs/crear', 'DocumentosController@guardar')
        ->middleware('can:crear,App\Documento');
    Route::get ('docs/crear', 'DocumentosController@crear')
        ->middleware('can:crear,App\Documento');
    Route::post('docs/{documento}/asignarResponsable', 
        'DocumentosController@asignarResponsable')
        ->middleware('can:asignarResponsable,documento');
    Route::post('docs/{documento}/agregarPropuesta', 
        'DocumentosController@agregarPropuesta')
        ->middleware('can:agregarPropuesta,documento');
    Route::post('docs/propuesta/{propuesta}/rechazar', 
        'DocumentosController@rechazarPropuesta')
        ->middleware('can:rechazar,propuesta');
    Route::post('docs/propuesta/{propuesta}/aceptar', 
        'DocumentosController@aceptarPropuesta')
        ->middleware('can:aceptar,propuesta');
    Route::post('docs/{documento}/corregir', 'DocumentosController@corregir')
        ->middleware('can:corregir,documento');
    Route::post('docs/{documento}/verificar', 'DocumentosController@verificar')
        ->middleware('can:verificar,documento');
    Route::post('docs/{documento}/cerrar', 'DocumentosController@cerrar')
        ->middleware('can:cerrar,documento');
    Route::get ('docs/{documento}/logs', 'DocumentosController@logs')
        ->middleware('can:ver,documento');
    Route::get ('docs/archivo/{archivo}', 'DocumentosController@archivo')
        ->middleware('can:ver,archivo');
    Route::get ('docs/{documento}', 'DocumentosController@ver')
        ->middleware('can:ver,documento');

    Route::middleware(['role:admin'])->group(function() {
        Route::resource('admin/users', 'Admin\UsersController');
        Route::post('admin/users/{user}/delRole/{role}',
            'Admin\UsersController@delRole');
        Route::post('admin/users/{user}/addRole',
            'Admin\UsersController@addRole');
        Route::post('admin/users/{user}/delDepartamento/{departamento}',
            'Admin\UsersController@delDepartamento');
        Route::post('admin/users/{user}/addDepartamento',
            'Admin\UsersController@addDepartamento');
        Route::get('admin/users/{user}/logs',
            'Admin\UsersController@logs');

        Route::resource('admin/departamentos', 'Admin\DepartamentosController');
        Route::resource('admin/tipos', 'Admin\TiposController');
    });
});   
