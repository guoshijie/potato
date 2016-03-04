<?php
/*
 * 购物车模块
 */
namespace App\Http\Controllers\Cart;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Http\Response;           //响应类
use App\Http\Models\Cart\CartModel;
use Illuminate\Support\Facades\Log;

class CartController extends ApiController
{
	protected $_model;
	public function __construct(){
		$this->_model = new CartModel();
	}


	/*
	 * 添加商品到购物车（多个）
	 * param $user_id       string  用户ID
	 * param $goods_ids     array   商品ID和商品数量
	 */
	public function addCart(Request $request){
		Log::info(print_r($request,1));

		if(!$request->has('user_id') || !$request->has('goods')){
			return $this->response(10005);
		}

//		$goods = array(
//			array(
//				'goods_id'  =>14,
//				'goods_num' =>2
//			),
//			array(
//				'goods_id'  =>131,
//				'goods_num' =>2
//			),
//			array(
//				'goods_id'  =>148,
//				'goods_num' =>1
//			),
////			array(
////				'goods_id'  =>150,
////				'goods_num' =>2
////			)
//		);



		$user_id    = $request->get('user_id');
		$goods      = $request->get('goods');

		//debug($goods);
		if(!is_array(json_decode($goods)) || empty($goods)){
			return $this->response(10023);
		}


		return json_encode($this->_model->addGoodsToCarts($user_id,$goods));

	}


	/*
	 * 添加商品到购物车(单个商品)
	 * param $user_id       string  用户ID
	 * param $goods_ids     array   商品ID和商品数量
	 */
//	public function addCart(Request $request){
//
//		if(!$request->has('user_id') || !$request->has('goods_id') || !$request->has('goods_num')){
//			return $this->response(10005);
//		}
//
//		$user_id    = $request->get('user_id');
//		$goods_id   = $request->get('goods_id');
//		$goods_num   = $request->get('goods_num');
//
//
//		return json_encode($this->_model->addGoodsToCart($user_id,$goods_id,$goods_num));
//
//	}


	/*
	 * 查看购物车列表
	 */
	public function getCartList(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

		$user_id    = $request->get('user_id');

		$data =  $this->_model->getCartListByUserId($user_id);
		if($data){
			return $this->response('1','获取成功',$data);
		}else{
			return $this->response(0);
		}
	}



	/*
	 * 获取购物车数量
	 */
	public function getCartNum(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

		$user_id    = $request->get('user_id');

		$data =  $this->_model->getCartNumByUserId($user_id);
		if($data){
			return $this->response('1','获取成功',$data);
		}else{
			return $this->response(0);
		}
	}



	//分页
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