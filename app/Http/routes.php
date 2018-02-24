<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/','FrontController@index');
Route::get('semaforo','FrontController@semaforo');
Route::get('mapa','FrontController@mapa');
Route::get('agendador','FrontController@agendador');
Route::get('software','FrontController@software');

/*Rutas parámetros*/
Route::get('parametros','FrontController@parametros');
Route::get('grupos','GrupoController@listing');
Route::resource('grupo','GrupoController');
Route::get('subgrupos','SubgrupoController@listing');
Route::resource('subgrupo','SubgrupoController');

/*Rutas indicadores*/
Route::resource('indicador','IndicadorController');
Route::get('indicadores','IndicadorController@listing');
Route::get('usuarios','UserController@listing');
Route::get('unidads','UnidadController@listing');

/*Rutas agenda*/
Route::resource('agenda','AgendaController');
Route::get('agendas','AgendaController@listing');

Route::get('admin','FrontController@admin');
Route::resource('user','UserController');
Route::resource('login','loginController');
Route::get('logout','loginController@logout');
Route::resource('genero','GeneroController');
Route::get('generos','GeneroController@listing');
Route::resource('lider','LiderController');
Route::resource('puesto','PuestoController');
Route::resource('votante','VotanteController');
Route::get('liders','LiderController@listing');
Route::get('votantes','VotanteController@listing');

Route::get('edad/{edad?}',function($edad=0){
	return "La edad del usuario es ".$edad;
});

/*Route::get('controlador', 'TryController@index');
Route::resource('movie','MovieController');

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);*/
