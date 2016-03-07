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


//demo
//$app->get('/demo/index',"Demo\DemoController@index");

//$app->get('/index',"ApiController@index");


/*
 * Callback
 */

$app->get('/alipay/callback',"Alipay\CallbackController@getTest");   //添加商品到购物车



