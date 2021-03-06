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
    $app->abort(404);
});


//demo
//$app->get('/demo/index',"Demo\DemoController@index");

//$app->get('/index',"ApiController@index");


/*
 * user register
 */

$app->get('/user/register/send-register-verify',"User\RegisterController@sendVerifyCode");   //获取验证码

//{
//    "code": 1,
//    "msg": "成功"
//}


$app->get('/user/register/check-verify',"User\RegisterController@checkVerify");  //校验验证码

//{
//    "code": 20210,
//    "msg": "验证码正确"
//}

$app->get('/user/register/register',"User\RegisterController@register"); //用户注册


/*
 * user login
 */
$app->get('/user/login/token',"User\TokenController@token");      //获取token

$app->get('/user/login/login-tel',"User\LoginController@loginTel");   //手机号登录


/*
 * user reset
 */
$app->get('/user/reset/get-verify',"User\ResetController@sendVerifyCode");      //获取验证码
//$app->get('/user/reset/check-verify',"User\ResetController@checkVerify");      //校验验证码

$app->get('/user/reset/reset-password',"User\ResetController@resetPwd");      //修改密码

$app->get('/user/reset/edit-head',"User\UserController@editHeadPic");      //修改头像

$app->post('/user/qiniu/token',"User\UserController@uploadQiniuToken");      //七牛上传图片token
$app->get('/user/qiniu/token',"User\UserController@uploadQiniuToken");      //七牛上传图片token

$app->get('/user/opinion',"User\UserController@setOpinion");        //意见反馈
$app->post('/user/opinion',"User\UserController@setOpinion");        //意见反馈

/*
 * user address
 */
$app->get('/user/shop/create-shop',"Shop\ShopController@createShop");      //添加收货地址
$app->post('/user/shop/create-shop',"Shop\ShopController@createShop");      //添加收货地址

$app->get('/user/shop/get-shop',"Shop\ShopController@showShopList");      //获取收货地址列表

$app->get('/user/shop/edit-shop',"Shop\ShopController@editShop");      //修改收货信息

$app->get('/user/shop/delete-shop',"Shop\ShopController@destroyShop");      //删除收货信息

$app->get('/user/shop/get-shop-default',"Shop\ShopController@getShopDefault");      //获取默认收货信息

$app->get('/user/shop/set-shop-default',"Shop\ShopController@addressDefault");      //设置默认收货信息
