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
);

foreach($anyAction as $v){
	$app->get($v[0],$v[1]); 
	$app->post($v[0],$v[1]); 
}
