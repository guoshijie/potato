<?php namespace Api\Server;
/*
 * 获取商品服务功能接口
 * author：liangfeng
 */
use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class Order extends Api
{
	const TEST = "TEST";
	const ORDER_HOST = "order.server.potato";

	public function __construct()
	{
		parent::__construct(Order::ORDER_HOST);
	}


	/*
	 * 添加商品到购物车
	 */
	public function addCart($page){
		return $this->getData("/order/cart/add-cart?page=" . $page);
	}


	/*
	 * 查看购物车列表
	 */
	public function getCartList($page){
		return $this->getData("/order/cart/get-cart-list?page=" . $page);
	}


	/*
	 * 提交订单
	 */
	public function orderConfirm($page){
		return $this->getData("/order/order/order-confirm?page=" . $page);
	}


	/*
	 * 获取订单列表
	 */
	public function getOrderList($page){
		return $this->getData("/order/order/order-list?page=" . $page);
	}



	/*
	 * 获取订单详情
	 */
	public function getOrderDetail($page){
		return $this->getData("/order/order/order-detail?page=" . $page);
	}



	/*
	 * 取消订单
	 */
	public function cancelOrder($page){
		return $this->getData("/order/order/order-cancel?page=" . $page);
	}




	/*
	 * 确认收货
	 */
	public function confirmReceiving($page){
		return $this->getData("/order/order/confirm-receiving?page=" . $page);
	}



	/*
	 * 联系卖家
	 */
	public function getSuppliers($goods_id){
		return $this->getData("/order/order/suppliers?goods_id=" . $goods_id);
	}




}