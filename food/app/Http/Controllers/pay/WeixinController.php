<?php
namespace App\Http\Controllers\Pay;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Payment as PaymentServer;
use App\Http\Controllers\ApiController;
class WeixinController extends ApiController
{

	var $paymentServer;

	public function __construct()
	{
		$this->paymentServer = new PaymentServer();
	}

	public function anyIndex(){

		if(!Request::has('out_trade_no') || !Request::has('goods_name') || !Request::has('total_fee') || !Request::has('payment_type')){
			return Response::json($this->response(10005));
		}

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$out_trade_no = Request::get('out_trade_no');
		$goods_name   = Request::get('goods_name');
		$total_fee    = Request::get('total_fee');
		$payment_type = Request::get('payment_type');
		$user_id      =   $this->loginUser->id;

		header('Content-type: text/html');
		return $this->paymentServer->weixinPay($out_trade_no,$goods_name,$total_fee,$payment_type,$user_id);
	}


	public function anyCallback(){

		$param    =   Request::all();

		$data =  $this->paymentServer->weixin($param);
		header('Content-type: text/html');
		if($data == 'fail'){
			return 'fail';
		}elseif($data == 'success'){
			return 'success';
		}else{
			return Response::json($this->response(0));
		}

	}


}
