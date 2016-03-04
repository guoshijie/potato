<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Order as OrderServer;
use App\Http\Controllers\ApiController;
class OrderController extends ApiController
{

	var $orderServer;

	public function __construct()
	{
		$this->orderServer = new OrderServer();
	}


	protected function checkUser(){
		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		return $this->loginUser->id;
	}


	/*
	 * 添加商品到购物车
	 */
	public function addCart(){

		if(!Request::has('goods_id') || !Request::has('goods')){
			return Response::json($this->response(10005));
		}

		$user_id    = $this->checkUser();
		$goods      = Request::get('goods');

		return $this->orderServer->addCart($user_id,$goods);
	}


	/*
	 * 查看购物车列表
	 */
	public function getCartList(){

		$user_id    = $this->checkUser();

		return $this->orderServer->getCartList($user_id);
	}


	/*
	 * 提交订单
	 */
	public function orderConfirm(){

		if(!Request::has('inv_payee') || !Request::has('goods')){
			return Response::json($this->response(10005));
		}

		$user_id    = $this->checkUser();
		$inv_payee  = Request::get('inv_payee');
		$goods      = Request::get('goods');

		return $this->orderServer->orderConfirm($user_id,$inv_payee,$goods);
	}


	/*
	 * 获取订单列表
	 */
	public function getOrderList(){
		if(!Request::has('status')){
			return Response::json($this->response(10005));
		}
		if(!Request::has('page')){
			$page   = 1;
		}else{
			$page   = Request::get('page');
		}


		$user_id    = $this->checkUser();
		$status     = Request::get('status');

		return $this->orderServer->getOrderList($page,$user_id,$status);
	}


	/*
	 * 获取订单详情
	 */
	public function getOrderDetail(){

		if(!Request::has('order_no') || !Request::has('sub_order_no')){
			return Response::json($this->response(10005));
		}

		$user_id    = $this->checkUser();
		$order_no   = Request::get('order_no');
		$sub_order_no  = Request::get('sub_order_no');

		return $this->orderServer->getOrderDetail($user_id,$order_no,$sub_order_no);
	}


	/*
	 * 取消大订单
	 */
	public function cancelOrderByOrderNo(){
		if(!Request::has('order_no')){
			return Response::json($this->response(10005));
		}

		$user_id    = $this->checkUser();
		$order_no   = Request::get('order_no');

		return $this->orderServer->cancelOrderByOrderNo($user_id,$order_no);
	}


	/*
	 * 取消子订单
	 */
	public function cancelOrderBySubOrderNo(){

		if(!Request::has('sub_order_no')){
			return Response::json($this->response(10005));
		}

		$user_id    = $this->checkUser();
		$sub_order_no   = Request::get('sub_order_no');

		return $this->orderServer->cancelOrderBySubOrderNo($user_id,$sub_order_no);
	}


	/*
	 * 确认收货
	 */
	public function confirmReceiving(){

		if(!Request::has('sub_order_no')){
			return Response::json($this->response(10005));
		}

		$user_id    = $this->checkUser();
		$sub_order_no   = Request::get('sub_order_no');

		return $this->orderServer->confirmReceiving($user_id,$sub_order_no);
	}

	/*
	 * 联系卖家
	 */
	public function getSuppliers(){

		if(!Request::has('suppliers_id')){
			return Response::json($this->response(10005));
		}

		$user_id    = $this->checkUser();
		$suppliers_id   = Request::get('suppliers_id');

		return $this->orderServer->getSuppliers($user_id,$suppliers_id);
	}


	/*
	 * 购物车数量
	 */
	public function getCartNum(){

		$user_id    = $this->checkUser();

		return $this->orderServer->getCartNum($user_id);
	}


	/*
	 * 订单数量
	 */
	public function getOrderNum(){
		if(!Request::has('type')){
			return Response::json($this->response(10005));
		}
		$user_id    = $this->checkUser();
		$type       = Request::get('type');

		return $this->orderServer->getOrderNum($user_id,$type);
	}



}
