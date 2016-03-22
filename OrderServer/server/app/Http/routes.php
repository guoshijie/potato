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
 * cart
 */

$app->get('/cart/add',"Cart\CartController@addCart");   //添加商品到购物车
$app->post('/cart/add',"Cart\CartController@addCart");   //添加商品到购物车

$app->get('/cart/list',"Cart\CartController@getCartList");   //查看购物车列表

$app->get('/cart/num',"Cart\CartController@getCartGoodsNum");   //获取购物车数量
$app->post('/cart/num',"Cart\CartController@getCartGoodsNum");   //获取购物车数量

$app->get('/cart/clear',"Cart\CartController@clear");   //添加商品到购物车


/*
 * order
 */
$app->get('/order/order/order-confirm',"Order\OrderController@orderConfirm");   //提交订单

$app->get('/order/order/order-list',"Order\OrderController@getOrderList");   //获取订单列表

$app->get('/order/order/order-detail',"Order\OrderController@getOrderDetail");   //获取订单详情

$app->get('/order/order/order-cancel',"Order\OrderController@cancelOrder");   //取消订单

$app->get('/order/order/confirm-receiving',"Order\OrderController@confirmReceiving");   //确认收货

$app->get('/order/order/suppliers',"Order\OrderController@getSuppliers");   //联系卖家

$app->get('/order/order/order-count',"Order\OrderController@getOrderNum");   //订单数量

$app->post('/order/paid',"Order\OrderController@paid");   // 完成支付
$app->get('/order/paid',"Order\OrderController@paid");   // 完成支付
