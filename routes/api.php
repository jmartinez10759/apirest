<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('alumnos','AlumnosController@index');
Route::get('alumnos/{id}','AlumnosController@show');
Route::post('alumnos/create','AlumnosController@store');
Route::put('alumnos/update/{id}','AlumnosController@update');
Route::delete('alumnos/delete/{id}','AlumnosController@destroy');


Route::get('empleados', 'EmpleadosController@index');
Route::get('empleados/{id}', 'EmpleadosController@show');
Route::post('empleados', 'EmpleadosController@store');
Route::put('empleados/{id}', 'EmpleadosController@update');
Route::delete('empleados/{id}', 'EmpleadosController@destroy');