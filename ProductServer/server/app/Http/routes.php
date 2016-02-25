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
    abort(404);
});


//demo
//$app->get('/demo/index',"Demo\DemoController@index");

//$app->get('/index',"ApiController@index");


/*
 * product
 */

$app->get('/product/product/index',"Product\ProductController@index");   //获取商品列表


$app->get('/product/product/detail',"Product\ProductController@detail");   //获取商品详情页

