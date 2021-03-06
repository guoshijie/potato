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
		Log::info(print_r($request->all(),1));

		if(!$request->has('user_id') || !$request->has('goods')){
			return $this->response(10005);
		}

		$user_id    = $request->get('user_id');
		$goods      = json_decode($request->get('goods'));

		if(!is_array($goods) || empty($goods)){
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
		$is_select = $request->has('is_select') ? $request->get('is_select') : null;

		$data =  $this->_model->getCartListByUserId($user_id, $is_select);
		if($data!==false){
			return $this->response('1','获取成功',$data);
		}else{
			return $this->response(0);
		}
	}



	/*
	 * 获取购物车数量
	 */
	public function getCartGoodsNum(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}

		$user_id    = $request->get('user_id');
		$goods_ids = $request->has('goods_ids') ? $request->get('goods_ids') : array();
		$data =  $this->_model->getCartGoodsNum($user_id, $goods_ids);
		if($data){
			return $this->response('1','获取成功',$data);
		}else{
			return $this->response(0);
		}
	}

	/*
	 *
	 */

	/*
	 * 清空购物车
	 */
	public function clear(Request $request){
		$messages = $this->vd([
				'user_id' => 'required',
			], $request);

		if($messages!=''){
			return $this->response(10005, $messages);
		}

		$cartM = new CartModel();
		$user_id    = $request->get('user_id');
		$rs = $cartM->clear($user_id);
		if($rs!==false && $rs!==null){
			return $this->response('1','清空成功');
		}else{
			return $this->response(0, '清空失败');
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
