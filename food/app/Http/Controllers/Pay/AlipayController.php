<?php
namespace App\Http\Controllers\Pay;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Payment as PaymentServer;
use \Api\Server\Pay as PayServer;
use App\Http\Controllers\ApiController;
class AlipayController extends ApiController
{

	var $paymentServer;

	public function __construct()
	{
		$this->paymentServer = new PaymentServer();
	}

	public function anyCallback(){
		$param    =   Request::all();
		/*
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
		 */

		$pay =  $this->paymentServer->alipay($param);
		$pay = json_decode($pay);
		//header('Content-type: text/html');
		if($pay->code){
			return $pay->msg;
		}else{
			return 'fail';
		}

	}

	/*
	 * 支付结果
	 */
	public function anyResult(){
		$params = Request::all();
		$server = new PayServer();
		return $server->post('/alipay/result', $params);
	}
}
