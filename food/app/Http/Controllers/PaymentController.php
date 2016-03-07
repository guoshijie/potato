<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Payment as PaymentServer;
use App\Http\Controllers\ApiController;
class PaymentController extends ApiController
{

	var $paymentServer;

	public function __construct()
	{
		$this->paymentServer = new PaymentServer();
	}


	public function alipay(){

		//$param    =   Request::all();
		$verify_result = Array(
			'discount'            => 0.00,
			'payment_type'        => 1,
			'subject'             => 'iPhone6 4.7英寸 128G',
			'trade_no'            => '2015101300001000340065',
			'buyer_email'         => 13240372487,
			'gmt_create'          => '2015-10-13 19:43:03',
			'gmt_payment'         => '2015-10-13 19:43:03',
			'notify_type'         => 'trade_status_sync',
			'quantity'            => 1,
			'notify_id'           => '',
			'out_trade_no'        => 'G304714535525622',
			'seller_id'           => 2088911708976095,
			'notify_time'         => '2015-10-13 19:43:04',
			'body'                => 2,
			'trade_status'        => 'TRADE_SUCCESS',
			'is_total_fee_adjust' => 'N',
			'total_fee'           => '12160.00',
			'seller_email'        => 'admin@shinc.net',
			'price'               => 0.01,
			'buyer_id'            => 2088712044133340,
			'use_coupon'          => 'N',
			'sign_type'           => 'RSA',
			'sign'                => 'mVd5lMtnAXEhSjv7ZQfQjeVkTH8kJGm2Hj/9CbZwAf32Us//aiDFSn9xmzlYQIcAt/HsJmMb/dU/FaTkXoBeMB21z+RPcYiizLjtpaxjgEhr75O9ESZVbxzLqiBxAh2J7eieBYofd4P03+PeQNZyVZV2Xm7jhi/t5cqMfVUZp8A='
		);

		$data =  $this->paymentServer->alipay($verify_result);
		header('Content-type: text/html');
		debug($data);
		if($data == 'fail'){
			return 'fail';
		}elseif($data == 'success'){
			return 'success';
		}else{

			return Response::json($this->response(0));
		}

	}


	public function weixinPay(){

		$param    =   Request::all();
		header('Content-type: text/html');
		return $this->paymentServer->weixinPay($param);
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
