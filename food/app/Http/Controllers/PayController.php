<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Payment as PaymentServer;
use App\Http\Controllers\ApiController;
class PayController extends ApiController
{

	var $paymentServer;

	public function __construct()
	{
		$this->paymentServer = new PaymentServer();
	}

	public function alipay(){
		$param    =   Request::all();
		$param = array (
				'out_trade_no' => 'G310798172248611',
				'discount' => '0.00',
				'payment_type' => '1',
				'subject' => '拾谷采购',
				'trade_no' => '2016031021001004240265278302',
				'buyer_email' => '13661383294',
				'gmt_create' => '2016-03-10 11:18:34',
				'notify_type' => 'trade_status_sync',
				'quantity' => '1',
				'seller_id' => '2088911708976095',
				'notify_time' => '2016-03-10 11:32:37',
				'body' => 'mmzt9W4eNFzs6TlZr9GwjkEkOQ6ibcVb8peJn3az',
				'trade_status' => 'TRADE_SUCCESS',
				'is_total_fee_adjust' => 'N',
				'total_fee' => '0.01',
				'gmt_payment' => '2016-03-10 11:18:36',
				'seller_email' => 'admin@shinc.net',
				'price' => '0.01',
				'buyer_id' => '2088802658468245',
				'notify_id' => 'aaec910822b2c8e09ff94d870df83f0huq',
				'use_coupon' => 'N',
				'sign_type' => 'RSA',
				'sign' => 'FwSws/O3u3O+0bLTuevOZwgGyNmEepP1BC3fs1qiYAl5RwhwBICq2nwazR5Rd2WYyOmdA8a6thv1Wcdy2TPTydPUH5LAjgMIRv0fCLwFyO9OFl0mM8WWzo+gHOfvihLOE/2ZFj4mqwduB4caH4NygjaAoJnos3mMDcbbwWj3C6A=',
			);

		$pay =  $this->paymentServer->alipay($param);
		$pay = json_decode($pay);
		//header('Content-type: text/html');
		if($pay->code){
			return $pay->msg;
		}else{
			return 'fail';
		}

	}


	public function weixinPay(){

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


	public function weixin(){

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
