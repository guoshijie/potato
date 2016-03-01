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
Route::get('/', function () {
	return view('welcome');
});

Route::get('/welcome/hello', "WelcomeController@hello");
Route::get('/welcome/intro', "WelcomeController@intro");

//Route::resource('/user','UserController');
//Route::put('/user/login','UserController@login');
//Route::get('/user/logout','UserController@logout');
Route::get('/sms','UserController@verify');                 //http://food.laraver.com/sms?tel=13661162115
Route::get('/check-sms','UserController@checkVerify');      //http://food.laraver.com/check-sms?tel=18612579961&code=579465
Route::post('/user/register','UserController@register');    //http://food.laraver.com/user/register?tel=18612579961&password=123456&code=579465

Route::get('/token','UserController@token');                //http://food.laraver.com/token

Route::get('/user/login','UserController@login');           //

