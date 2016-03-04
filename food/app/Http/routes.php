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

Route::group( array('prefix' => $version),function() {
	Route::any('/', function () {
		return view('welcome');
	});

	Route::any('/welcome/hello', "WelcomeController@hello");
	Route::any('/welcome/intro', "WelcomeController@intro");

	Route::any('/sms','UserController@verify');                 //http://food.laraver.com/sms?tel=13661162115

	Route::any('/check-sms','UserController@checkVerify');      //http://food.laraver.com/check-sms?tel=18612579961&code=579465

	Route::any('/user/register','UserController@register');    //http://food.laraver.com/user/register?tel=18612579961&password=123456&code=579465

	Route::any('/token','UserController@token');                //http://food.laraver.com/token

	Route::any('/user/login','UserController@login');           //http://food.laraver.com/user/login?tel=18612579961&password=123456&key=b8233e5d48c14aa9a9374161939f390&form_token=5e1033a21b814493174315ccc5bae9ad

	Route::any('/user/reset-verify','UserController@resetVerify');  //http://food.laraver.com/user/reset-verify?tel=18612579961

	Route::any('/user/reset','UserController@reset');           //http://food.laraver.com/user/reset?password=12345678

	Route::any('/user/edit','UserController@headPic');          //http://food.laraver.com/user/edit?head_pic=www.baidu.com

	Route::any('/user/create-address','UserController@addAddress');     //http://food.laraver.com/user/create-address?name=liangfeng&tel=18612579961&district=beijing&address=%E6%BE%B6%E9%98%B3%E5%8C%BA%E9%83%BD%E7%AC%AC%E4%B8%89%E5%AD%A3&head_name=%E4%B8%96%E5%92%8C%E7%A7%91%E6%8A%80&code=458991

	Route::any('/user/get-address','UserController@showShopList');      //http://food.laraver.com/user/get-address

	Route::any('/user/edit-address','UserController@editShop');     //http://food.laraver.com/user/edit-address?name=%E6%A2%81%E6%9E%AB&tel=18612579961&district=%E5%8C%97%E4%BA%AC%E5%B8%82&address=%E6%BE%B6%E9%98%B3%E5%8C%BA%E9%83%BD%E7%AC%AC%E4%B8%89%E5%AD%A3&head_name=%E4%B8%96%E5%92%8C%E7%A7%91%E6%8A%80&code=756507&is_default=1&id=210

	Route::any('/user/delete-address','UserController@destroyShop');        //http://food.laraver.com/user/delete-address?address_id=209

	Route::any('/user/get-address-default','UserController@getAddressDefault'); //http://food.laraver.com/user/get-address-default

	Route::any('/user/set-address-default','UserController@setAddressDefault'); //http://food.laraver.com/user/get-address-default

	Route::any('/user/logout','UserController@logout');         //http://food.laraver.com/user/logout?uid=2

	Route::any('/user/qiniu/token','UserController@getQiniuToken');         //http://food.laraver.com/user/logout?uid=2



	Route::any('/goods','GoodsController@index');       //http://food.laraver.com/goods?page=1

	Route::any('/goods/detail','GoodsController@detail');   //http://food.laraver.com/goods/detail?goods_id=146


	Route::any('/order/add','OrderController@addCart');  //添加购物车

	Route::any('/order/cart','OrderController@getCartList');    //购物车列表

	Route::any('/order/confirm','OrderController@orderConfirm');    //提交订单

	Route::any('/order/order','OrderController@getOrderList');  //订单列表

	Route::any('/order/detail','OrderController@getOrderDetail');   //订单详情

	Route::any('/order/cancel/order','OrderController@cancelOrderByOrderNo');   //根据订单号取消订单

	Route::any('/order/cancel/suborder','OrderController@cancelOrderBySubOrderNo');  //根据子订单号取消订单

	Route::any('/order/receiving','OrderController@confirmReceiving');  //确认收货

	Route::any('/order/suppliers','OrderController@getSuppliers');   //联系卖家

	Route::any('/cart/num','OrderController@getCartNum');   //购物车数量

	Route::any('/order/num','OrderController@getOrderNum');   //订单数量
});
