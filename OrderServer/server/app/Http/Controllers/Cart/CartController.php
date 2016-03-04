<?php
/*
 * 购物车模块
 */
namespace App\Http\Controllers\Cart;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Http\Response;           //响应类
use App\Http\Models\Cart\CartModel;

class CartController extends ApiController
{
	protected $_model;
	public function __construct(){
		$this->_model = new CartModel();
	}


	/*
	 * 添加商品到购物车
	 * param $user_id       string  用户ID
	 * param $goods_ids     array   商品ID和商品数量
	 */
	public function addCart(Request $request){

		if(!$request->has('user_id') || !$request->has('goods_id') || !$request->has('goods_num')){
			return $this->response(10005);
		}

		$user_id    = $request->get('user_id');
		$goods_id   = $request->get('goods_id');
		$goods_num   = $request->get('goods_num');


		return json_encode($this->_model->addGoodsToCart($user_id,$goods_id,$goods_num));

	}


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