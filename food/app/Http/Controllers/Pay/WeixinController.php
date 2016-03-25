<?php
namespace App\Http\Controllers\Pay;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Pay as PayServer;
use App\Http\Controllers\ApiController;
class WeixinController extends ApiController
{

	var $paymentServer;

	public function __construct()
	{
		$this->payServer = new PayServer();
	}

	public function anyIndex(){
		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}
		/*
		if(!Request::has('out_trade_no') || !Request::has('goods_name') || !Request::has('total_fee') || !Request::has('payment_type')){
			return Response::json($this->response(10005));
		}

		$out_trade_no = Request::get('out_trade_no');
		$goods_name   = Request::get('goods_name');
		$total_fee    = Request::get('total_fee');
		$payment_type = Request::get('payment_type');
		$user_id      =   $this->loginUser->id;
		 */
		$param = Request::all();
		$param['user_id'] = $this->loginUser->id;
		$param['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];

		header('Content-type: text/html');

		// 微信公众号支付
		if(Request::has('platform') && Request::get('platform')=='mp'){
			$param['notify_url'] = Request::url().'/callback-mp';
			return $this->payServer->post('/weixinmp/pay', $param);
		}else{ // 微信移动支付
			$param['notify_url'] = Request::url().'/callback';
			return $this->payServer->post('/weixin/pay', $param);
		}

		//return $this->payServer->weixinPay($out_trade_no,$goods_name,$total_fee,$payment_type,$user_id);
	}


	public function anyCallback(){
		$param    =   Request::all();
		if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
			$param['HTTP_RAW_POST_DATA'] = $GLOBALS["HTTP_RAW_POST_DATA"];
		}
		$data =  $this->payServer->post('/weixin/callback', $param);
		header('Content-type: text/html');
		if($data == 'fail'){
			return 'fail';
		}elseif($data == 'success'){
			return 'success';
		}else{
			return Response::json($this->response(0));
		}
	}


	public function anyCallbackMp(){
		$param    =   Request::all();
		if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
			$param['HTTP_RAW_POST_DATA'] = $GLOBALS["HTTP_RAW_POST_DATA"];
		}
		$data =  $this->payServer->post('/weixinmp/callback', $param);
		header('Content-type: text/html');
		if($data == 'fail'){
			return 'fail';
		}elseif($data == 'success'){
			return 'success';
		}else{
			return Response::json($this->response(0));
		}
	}

	/*
	 * jssdk signature
	 */
	public function anySign(){
		$param    =   Request::all();
		return $this->payServer->post('/weixinmp/sign', $param);
	}
}
