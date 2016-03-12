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
	array('/product/product/index','Product\ProductController@index'),
	array('/product/product/detail','Product\ProductController@detail'),
	array('/get-total-price','Product\ProductController@getTotalPrice'),

	array('/product/add', 'Product\ProductController@add'),
	array('/category/add', 'Product\CategoryController@add'),
);

foreach($anyAction as $v){
	$app->get($v[0],$v[1]); 
	$app->post($v[0],$v[1]); 
}


