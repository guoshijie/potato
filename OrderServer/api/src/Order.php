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
	const HOST = "order.server.potato";

	public function __construct()
	{
		parent::__construct(self::HOST);
	}

	/*
	 * 提交订单
	 */
	public function orderConfirm($user_id,$inv_payee,$goods){
		return $this->getData("/order/order/order-confirm?user_id=" . $user_id."&inv_payee=".$inv_payee."&goods=".$goods);
	}


	/*
	 * 获取订单列表
	 */
	public function getOrderList($page,$user_id,$status){
		return $this->getData("/order/order/order-list?page=" . $page."&user_id=".$user_id."&status=".$status);
	}



	/*
	 * 获取订单详情
	 */
	public function getOrderDetail($user_id,$order_no,$sub_order_no){
		return $this->getData("/order/order/order-detail?user_id=" . $user_id."&order_no=".$order_no."&sub_order_no=".$sub_order_no);
	}



	/*
	 * 取消订单
	 */
	public function cancelOrderByOrderNo($user_id,$order_no){
		return $this->getData("/order/order/order-cancel?user_id=" . $user_id."&order_no=".$order_no);
	}

	/*
	 * 取消订单
	 */
	public function cancelOrderBySubOrderNo($user_id,$sub_order_no){
		return $this->getData("/order/order/order-cancel?user_id=" . $user_id."&sub_order_no=".$sub_order_no);
	}



	/*
	 * 确认收货
	 */
	public function confirmReceiving($user_id,$sub_order_no){
		return $this->getData("/order/order/confirm-receiving?user_id=" . $user_id."&sub_order_no=".$sub_order_no);
	}



	/*
	 * 联系卖家
	 */
	public function getSuppliers($user_id,$suppliers_id){
		return $this->getData("/order/order/suppliers?user_id=" . $user_id."&suppliers_id=".$suppliers_id);
	}


	/*
	 * 订单数量
	 */
	public function getOrderNum($user_id){
		return $this->getData("/order/order/order-count?user_id=" . $user_id);
	}


	/*
	 * 无特殊情况通用此方法调用server
	 */
	public function post($action, $params){
		return  $this->postData($action, $params);
	}



}
