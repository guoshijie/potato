<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    echo "<h1 align='center' style='margin-top: 20%;'>Hello World!</h1>";
});


$app->get('/alipay/result',"Alipay\CallbackController@result");   //添加商品到购物车
$app->post('/alipay/result',"Alipay\CallbackController@result");   //添加商品到购物车

/*
 * Callback
 */


$app->get('/alipay/callback',"Alipay\CallbackController@callback");   //添加商品到购物车
$app->post('/alipay/callback',"Alipay\CallbackController@callback");   //添加商品到购物车

$app->get('/weixin/api',"Weixinpay\WeixinApiController@wxPay");
$app->post('/weixin/api',"Weixinpay\WeixinApiController@wxPay");

$app->get('/weixin/callback',"Weixinpay\WeixinApiController@callback");
$app->post('/weixin/callback',"Weixinpay\WeixinApiController@callback");




