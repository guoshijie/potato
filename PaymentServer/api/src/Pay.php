<?php namespace Api\Server;
/*
 * 获取商品服务功能接口
 * author：liangfeng
 */
use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class Pay extends Api
{
	const HOST = "payment.server.potato";

	public function __construct()
	{
		parent::__construct(Payment::HOST);
	}

	/*
	 * 无特殊情况通用此方法调用server
	 */
	public function post($action, $params){
		return  $this->postData($action, $params);
	}

}
