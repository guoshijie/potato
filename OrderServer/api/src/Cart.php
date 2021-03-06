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
	public function getCartList($params, $user_id){
		$is_select = isset($params['is_select']) ? '&is_select='.$params['is_select'] : '';
		return $this->getData("/cart/list?user_id=" . $user_id.$is_select);
	}

	/*
	 * 购物车数量
	 */
	public function getCartGoodsNum($user_id, $goods_ids=array()){
		if(!empty($goods_ids)){
			return $this->postData("/cart/num?user_id=" . $user_id, $goods_ids);
		}else{
			return $this->getData("/cart/num?user_id=" . $user_id);
		}
	}

	public function clear($user_id){
		return $this->getData("/cart/clear?user_id=" . $user_id);
	}

	public function preOrder($user_id,$goods){
		$data['user_id'] = $user_id;
		$data['goods'] = $goods;
		return $this->postData("/cart/pre-order", $data);
	}
}
