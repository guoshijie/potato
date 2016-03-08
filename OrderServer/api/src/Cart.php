<?php namespace Api\Server;
/*
 * 获取商品服务功能接口
 * author：liangfeng
 */
use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class Cart extends Api
{
	const HOST = "order.server.potato";

	public function __construct()
	{
		parent::__construct(self::HOST);
	}


	/*
	 * 添加商品到购物车
	 */
	public function addCart($user_id,$goods){
		$data['user_id'] = $user_id;
		$data['goods'] = $goods;
		return $this->postData("/cart/add", $data);
		//return $this->getData("/cart/add-cart?user_id=" . $user_id."&goods=".$goods);
	}


	/*
	 * 查看购物车列表
	 */
	public function getCartList($user_id){
		return $this->getData("/cart/list?user_id=" . $user_id);
	}

	/*
	 * 购物车数量
	 */
	public function getCartNum($user_id){
		return $this->getData("/cart/num?user_id=" . $user_id);
	}

	public function clear($user_id){
		return $this->getData("/cart/clear?user_id=" . $user_id);
	}
}
