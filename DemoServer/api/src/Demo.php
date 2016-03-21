<?php namespace Api\Server;
/*
 * description：
 * author：
 */
use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class Demo extends Api
{
	const HOST = "demo.server.potato";

	public function __construct()
	{
		parent::__construct(self::HOST);
	}

	public function post($action, $params){
		return  $this->postData($action, $params);
	}

}
