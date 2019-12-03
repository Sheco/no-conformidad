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

Route::middleware(['auth', '2fa'])->group(function() {
    Route::redirect('/', '/docs');
    Route::redirect('/home', '/docs');
    
    Route::get ('docs', 'DocumentosController@index');
    Route::get ('docs/status', 'DocumentosController@index');
    Route::get ('docs/status/{status}', 'DocumentosController@index');
    Route::get ('docs/filtros', 'DocumentosController@filtros');
    Route::post('docs/filtros', 'DocumentosController@filtrosGuardar');
    Route::get ('docs/crear', 'DocumentosController@crear')
        ->middleware('can:crearDocumentos,App\Documento');
    Route::post('docs/crear', 'DocumentosController@guardar');
    Route::post('docs/{documento}/asignarResponsable', 
        'DocumentosController@asignarResponsable');
    Route::post('docs/{documento}/agregarPropuesta', 
        'DocumentosController@agregarPropuesta');
    Route::post('docs/propuesta/{propuesta}/rechazar', 
        'DocumentosController@rechazarPropuesta');
    Route::post('docs/propuesta/{propuesta}/aceptar', 
        'DocumentosController@aceptarPropuesta');
    Route::post('docs/{documento}/corregir', 
        'DocumentosController@corregir');
    Route::post('docs/{documento}/verificar', 
        'DocumentosController@verificar');
    Route::post('docs/{documento}/cerrar', 
        'DocumentosController@cerrar');
    Route::get ('docs/{documento}/logs', 'DocumentosController@logs')
        ->middleware('can:ver,documento');
    Route::get ('docs/archivo/{archivo}', 'DocumentosController@archivo')
        ->middleware('can:ver,archivo');
    Route::get ('docs/{documento}', 'DocumentosController@ver')
        ->middleware('can:ver,documento');

    Route::get ('twofactor', 'TwoFactorController@index');
    Route::post('twofactor', 'TwoFactorController@enable');
    Route::delete('twofactor', 'TwoFactorController@disable');

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
        Route::post('admin/users/{user}/twofactor/disable',
            'Admin\UsersController@twofactorDisable');

        Route::resource('admin/departamentos', 'Admin\DepartamentosController');
        Route::resource('admin/tipos', 'Admin\TiposController');
    });
});   
