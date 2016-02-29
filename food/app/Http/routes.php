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
//Route::get("/", "HomeController@showWelcome");
Route::resource('/user','UserController');
Route::put('/user/login','UserController@login');
Route::get('/user/logout','UserController@logout');

