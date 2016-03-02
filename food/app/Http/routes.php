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
$version = '/v1';

Route::get($version.'/', function () {
	return view('welcome');
});

Route::get($version.'/welcome/hello', "WelcomeController@hello");
Route::get($version.'/welcome/intro', "WelcomeController@intro");


Route::get('/sms','UserController@verify');                 //http://food.laraver.com/sms?tel=13661162115

Route::get('/check-sms','UserController@checkVerify');      //http://food.laraver.com/check-sms?tel=18612579961&code=579465

Route::get('/user/register','UserController@register');    //http://food.laraver.com/user/register?tel=18612579961&password=123456&code=579465

Route::get('/token','UserController@token');                //http://food.laraver.com/token

Route::get('/user/login','UserController@login');           //http://food.laraver.com/user/login?tel=18612579961&password=123456&key=b8233e5d48c14aa9a9374161939f390&form_token=5e1033a21b814493174315ccc5bae9ad

Route::get('/user/reset-verify','UserController@resetVerify');  //http://food.laraver.com/user/reset-verify?tel=18612579961

Route::get('/user/reset','UserController@reset');           //http://food.laraver.com/user/reset?password=12345678

Route::get('/user/edit','UserController@headPic');          //http://food.laraver.com/user/edit?head_pic=www.baidu.com

Route::get('/user/create-address','UserController@addAddress');     //http://food.laraver.com/user/create-address?name=liangfeng&tel=18612579961&district=beijing&address=%E6%BE%B6%E9%98%B3%E5%8C%BA%E9%83%BD%E7%AC%AC%E4%B8%89%E5%AD%A3&head_name=%E4%B8%96%E5%92%8C%E7%A7%91%E6%8A%80&code=458991

Route::get('/user/get-address','UserController@showShopList');      //http://food.laraver.com/user/get-address

Route::get('/user/edit-address','UserController@editShop');     //http://food.laraver.com/user/edit-address?name=%E6%A2%81%E6%9E%AB&tel=18612579961&district=%E5%8C%97%E4%BA%AC%E5%B8%82&address=%E6%BE%B6%E9%98%B3%E5%8C%BA%E9%83%BD%E7%AC%AC%E4%B8%89%E5%AD%A3&head_name=%E4%B8%96%E5%92%8C%E7%A7%91%E6%8A%80&code=756507&is_default=1&id=210

Route::get('/user/delete-address','UserController@destroyShop');        //http://food.laraver.com/user/delete-address?address_id=209

Route::get('/user/get-address-default','UserController@getAddressDefault'); //http://food.laraver.com/user/get-address-default

Route::get('/user/logout','UserController@logout');         //http://food.laraver.com/user/logout?uid=2

Route::get('/goods','GoodsController@index');
