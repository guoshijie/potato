<?php namespace Api\Server;
/*
 * 获取商品服务功能接口
 * author：liangfeng
 */
use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class Goods extends Api
{
	const TEST = "TEST";
	const GOODS_HOST = "goods.server.com";

	public function __construct()
	{
		parent::__construct(Goods::GOODS_HOST);
	}



	/*
	 * 获取商品列表
	 */
	public function index($page){
		return $this->getData("/product/product/index?page=" . $page);
	}



	/*
	 * 获取商品详情页
	 */
	public function detail($goods_id){
		return $this->getData("/product/product/detail?goods_id=" . $goods_id);
	}




}