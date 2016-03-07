<?php namespace Api\Server;
/*
 * 获取商品服务功能接口
 * author：liangfeng
 */
use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class Payment extends Api
{
	const HOST = "payment.server.potato";

	public function __construct()
	{
		parent::__construct(Payment::HOST);
	}


	/*
	 * 获取支付宝回调通知
	 */
	public function alipay($verify_result){
		return  $this->postData("/alipay/callback",$verify_result);

	}


	/*
	 * 微信预订单
	 */
	public function weixinPay($param){
		return $this->postData("/weixin/api",$param);
	}


	/*
	 * 微信回调通知
	 */
	public function weixin($param){
		return $this->postData("/weixin/callback",$param);
	}




}
