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

$anyAction = array(
	array('/alipay/result','AlipayController@result'),
	array('/alipay/callback','AlipayController@callback'),
	array('/cash','CashController@index'),

	// 微信移动支付
	array('/weixin/pay','WeixinController@pay'),
	array('/weixin/callback','WeixinController@callback'),
	array('/weixin/sign','WeixinController@sign'),

	// 微信公众号支付
	array('/weixinmp/pay','WeixinmpController@pay'),
	array('/weixinmp/callback','WeixinmpController@callback'),
	array('/weixinmp/sign','WeixinmpController@sign'),
);

foreach($anyAction as $v){
	$app->get($v[0],$v[1]); 
	$app->post($v[0],$v[1]); 
}




$app->get('/', function () use ($app) {
    echo "<h1 align='center' style='margin-top: 20%;'>Hello World!</h1>";
});
