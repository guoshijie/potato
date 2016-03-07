<?php
/*
 * 订单服务
 * author:liangfeng@shinc.net
 */
namespace App\Http\Controllers\Order;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use App\Http\Models\Order\OrderModel;

/**
 * Class OrderController
 * @package App\Http\Controllers\Order
 */
class OrderController extends ApiController
{
	protected $_model;
	public function __construct(){
		$this->_model = new OrderModel();
	}

	/*
	 * 订单确认(下单)
	 * param $user_id   string  用户ID
	 * param $goods     array   商品参数(goods_id,goods_num)
	 * param $inv_payee string  发票抬头
	 */
	public function orderConfirm(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

//		$goods = array(
//			array(
//				'goods_id'  =>2,
//				'goods_num' =>2
//			),
////			array(
////				'goods_id'  =>10,
////				'goods_num' =>2
////			),
////			array(
////				'goods_id'  =>148,
////				'goods_num' =>1
////			),
////			array(
////				'goods_id'  =>150,
////				'goods_num' =>2
////			)
//		);

		//二维数组，存商品ID和商品数量
		if(!$request->has('goods') ){
			return $this->response(40004);
		}



		$user_id    = $request->get('user_id');
		$goods      = json_decode($request->get('goods'));

		//debug($goods);
		if(!is_array($goods)){
			return $this->response(10023);
		}

		$inv_payee  = $request->has('inv_payee') ? $request->get('inv_payee') : '';
		$data = $this->_model->orderConfirmByUser($user_id,$goods,$inv_payee);

		if($data == -1){
			return $this->response(40005);
		}elseif($data == -2){
			return $this->response(10023);
		}elseif($data == -3){
			return $this->response(20001);
		}elseif($data == -4){
			return $this->response(10006);
		}elseif($data == -5){
			return $this->response(10003);
		}elseif($data == -6){
			return $this->response(40001);
		}else{
			return $this->response(1,'成功',$data);
		}

	}



	/*
	 * 获取订单列表
	 * param $user_id  string  用户ID
	 * param $satus    string  订单状态(1=未支付,2=待收货，3=已完成,4=已撤销)
	 * param $offset   string  分页开始位置
	 * param $length   string  分页显示长度
	 */
	public function getOrderList(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}
		if(!$request->has('status') ){
			return $this->response(40007);
		}

		$pageinfo = $this->pageinfo($request);
		$user_id    = $request->get('user_id');
		$status     = $request->get('status');

		if($status == 1){
			$data = $this->_model->getOrderListByNoPay($user_id,$pageinfo->offset, $pageinfo->length);
		}elseif($status == 2){
			$data = $this->_model->getOrderListByWaiting($user_id,$pageinfo->offset, $pageinfo->length);
		}elseif($status == 3){
			$data = $this->_model->getOrderListByFinish($user_id,$pageinfo->offset, $pageinfo->length);
		}elseif($status == 4){
			$data = $this->_model->getOrderListByCancel($user_id,$pageinfo->offset, $pageinfo->length);
		}else{
			return $this->response(0);
		}

		if($data){
			return $this->response(1,'成功',$data);
		}else{
			return $this->response(0);
		}

	}



	/*
	 * 根据订单ID获取订单详细信息
	 * param    $user_id    string  用户ID
	 * param    $order_id   string  订单ID
	 */
	public function getOrderDetail(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

		if(!$request->has('order_no') || !$request->has('sub_order_no')){
			return $this->response(10005);
		}



		$user_id        = $request->get('user_id');
		$order_no       = $request->get('order_no');
		$son_order_no   = $request->get('sub_order_no');

		$data = $this->_model->getOrderDetailByOrderId($user_id,$order_no,$son_order_no);

		if($data){
			return $this->response(1,'成功',$data);
		}else{
			return $this->response(0);
		}

	}



	/*
	 * 取消订单
	 * @param order_no      string  订单ID
	 * @param sub_order_no  string  子订单ID
	 */
	public function cancelOrder(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

		$user_id = $request->get('user_id');

		if($request->has('order_no')){
			$order_no = $request->get('order_no');
			$data     = $this->_model->cancelOrderNo($user_id,$order_no);

			//debug($data);
		}

		if($request->has('sub_order_no')){
			$sub_order_no = $request->get('sub_order_no');
			$data     = $this->_model->cancelSubOrderNo($user_id,$sub_order_no);
		}


		if(!isset($order_no) && !isset($sub_order_no)){
			return $this->response(10005);
		}

		if(!isset($data)){
			return $this->response(0);
		}

		if($data){
			return $this->response(1,'成功',$data);
		}else{
			return $this->response(40009);
		}
	}


	/*
	 * 确认收货
	 */
	public function confirmReceiving(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

		if(!$request->has('sub_order_no')){
			return $this->response(10005);
		}

		$user_id  = $request->get('user_id');
		$sub_order_no = $request->get('sub_order_no');
		$data     = $this->_model->confirmReceivingOrder($user_id,$sub_order_no);

		if($data){
			return $this->response(1,'成功',$data);
		}else{
			return $this->response(40009);
		}

	}


	/*
	 * 联系供应商
	 *
	 */
	public function getSuppliers(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}
		if(!$request->has('suppliers_id')){
			return $this->response(10005);
		}

		$suppliers_id  = $request->get('suppliers_id');
		$data     = $this->_model->getSuppliersInformation($suppliers_id);

		if($data){
			return $this->response(1,'成功',$data);
		}else{
			return $this->response(0);
		}

	}


	/**
	 * @param Request $request
	 * @return array
	 */
	public function getOrderNum(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

		if(!$request->has('type')){
			return $this->response(10005);
		}

		$user_id  = $request->get('user_id');
		$type     = $request->get('type');
		$data     = $this->_model->getOrderNumByUserId($user_id,$type);

		if($data){
			return $this->response(1,'成功',$data);
		}else{
			return $this->response(0);
		}

	}


	/**
	 * @param     $request
	 * @param int $length
	 * @return \stdClass
	 */
	private function pageinfo($request,$length=20){
		$pageinfo               = new \stdClass;
		$pageinfo->length       = $request->has('length') ? $request->get('length') : $length;;
		$pageinfo->page         = $request->has('page') ? $request->get('page') : 1;
		$pageinfo->offset		= $pageinfo->page<=1 ? 0 : ($pageinfo->page-1) * $pageinfo->length;
		//$page->totalNum     = (int)Product::getInstance()->getPurchaseTotalNum();
		$pageinfo->totalNum     = 0;
		$pageinfo->totalPage    = ceil($pageinfo->totalNum/$pageinfo->length);

		return $pageinfo;
	}

}