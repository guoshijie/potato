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
	array('/alipay/result','Alipay\CallbackController@result'),
	array('/alipay/callback','Alipay\CallbackController@callback'),
	array('/cash','CashController@index'),
);

foreach($anyAction as $v){
	$app->get($v[0],$v[1]); 
	$app->post($v[0],$v[1]); 
}

/*
 * Callback
 */
$app->get('/weixin/api',"Weixinpay\WeixinApiController@wxPay");
$app->post('/weixin/api',"Weixinpay\WeixinApiController@wxPay");

$app->get('/weixin/callback',"Weixinpay\WeixinApiController@callback");
$app->post('/weixin/callback',"Weixinpay\WeixinApiController@callback");





$app->get('/', function () use ($app) {
    echo "<h1 align='center' style='margin-top: 20%;'>Hello World!</h1>";
});
