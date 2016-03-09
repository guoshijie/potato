<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\Order as OrderServer;
use \Api\Server\Cart as CartServer;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Log;
class OrderController extends ApiController
{

	var $orderServer;

	public function __construct()
	{
		parent::__construct();

		$this->orderServer = new OrderServer();
		$this->CartServer = new CartServer();
	}


	/*
	 * 添加商品到购物车
	 */
	public function addCart(){
		if(!$this->isLogin()){ return Response::json($this->response(99999)); }

		Log::info(print_r(Request::all(),1));
		$messages = $this->vd([
			'inv_payee' => 'required',
			'goods' => 'required',
		]);
		if($messages!='') return Response::json($this->response(10005, $messages)); 

		// 兼容特殊情况
		if(Request::isJson()) {
			$goods = json_encode(Request::json('goods'));
		}else{
			$goods	= Request::get('goods');
		}

		return $this->CartServer->addCart($this->loginUser->id, $goods);
	}

	/*
	 * 清空购物车
	 */
	public function clearCart(){
		if(!$this->isLogin()) return Response::json($this->response(99999));

		return $this->CartServer->clear($this->loginUser->id);
	}


	/*
	 * 查看购物车列表
	 */
	public function getCartList(){
		if(!$this->isLogin()) return Response::json($this->response(99999));

		$user_id    =   $this->loginUser->id;

		return $this->CartServer->getCartList(Request::all(), $user_id);
	}


	/*
	 * 提交订单
	 */
	public function orderConfirm(){
		Log::info(print_r(Request::all(),1));
		
		$messages = $this->vd([
			'inv_payee' => 'required',
			'goods' => 'required',
		]);
		if($messages!='') return Response::json($this->response(10005, $messages)); 

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		// 兼容特殊情况
		if(Request::isJson()) {
			$goods = json_encode(Request::json('goods'));
			$inv_payee  = Request::json('inv_payee');
		}else{
			$inv_payee  = Request::get('inv_payee');
			$goods      = Request::get('goods');
		}

		$user_id    = $this->loginUser->id;
		Log::info(print_r($goods,1));

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

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id    =   $this->loginUser->id;
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

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id    =   $this->loginUser->id;
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

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id    =   $this->loginUser->id;
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

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id        =   $this->loginUser->id;
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

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id        =   $this->loginUser->id;
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

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id    =   $this->loginUser->id;
		$suppliers_id   = Request::get('suppliers_id');

		return $this->orderServer->getSuppliers($user_id,$suppliers_id);
	}


	/*
	 * 购物车数量
	 */
	public function getCartNum(){

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id    =   $this->loginUser->id;

		return $this->CartServer->getCartNum($user_id);
	}


	/*
	 * 订单数量
	 */
	public function getOrderNum(){
		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id    =   $this->loginUser->id;
		return $this->orderServer->getOrderNum($user_id);
	}


}
