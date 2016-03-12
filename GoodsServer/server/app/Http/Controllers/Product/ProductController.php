<?php
/*
 * 控制器用例
 */
namespace App\Http\Controllers\Product;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Http\Response;           //响应类
use App\Http\Models\Product\ProductModel;

class ProductController extends ApiController
{
	protected $_model;
	public function __construct(){
		$this->_model = new ProductModel();
	}


	/*
	 * 获取商品列表
	 */
	public function index(Request $request){

		$pageinfo = $this->pageinfo($request);

		$data =  $this->_model->getProductList($pageinfo->offset , $pageinfo->length);
		if($data!==false){
			if(empty($data)){
				return json_encode($this->response('1','获取成功'));
			}
			return json_encode($this->response('1','获取成功',array('product_list'=>$data)));
		}else{
			return $this->response(0);
		}
	}


	/*
	 * 根据商品ID获取商品详情
	 */
	public function detail(Request $request){
		if(!$request->has('goods_id')){
			return $this->response(10005);
		}
		$goods_id    =   $request->get('goods_id');

		$data       =   $this->_model->getProductById($goods_id);
		if($data){
			return $this->response(1,'获取成功',$data);
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

	/*
	 * 计算商品总价
	 * $arrGoodsNum	 array('goods_id value'=>'num value')
	 *
	 */
	public function getTotalPrice(Request $request){
		$arrGoodsNum = $request->get('goods_nums');
		$goods_ids = array_keys($arrGoodsNum);
		$goodsNumList = $this->_model->getGoodsPrice($goods_ids);
		$priceList = array();
		foreach($goodsNumList as $v){
			$priceList[$v->goods_id] = $v->shop_price * $arrGoodsNum[$v->goods_id];
		}
		return $this->response(1, '成功',$priceList);
	}

	/*
	 * 添加商品
	 */
	public function add(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}
		$messages = $this->vd([
			'category_id' => 'required',
			'suppliers_id' => 'required',
			'goods_name' => 'required',
			'img_url' => 'required',
			'description' => 'required',
			'shop_price' => 'required',
			], $request);
		if($messages!='') return $this->response(10005, $messages);

		$data = array();

		$data['sh_category_id']		= $request->get('category_id');
		$data['goods_name']			= $request->get('goods_name');

		//这里因为可以提交多个图片,但暂时只用一个
		$goodsImg					= $request->get('img_url');
		$data['goods_img']			= is_array($goodsImg) ? $goodsImg[0] : $goodsImg;

		$data['goods_desc']			= $request->get('description');
		$data['shop_price']			= $request->get('shop_price');

		if( $request->has('specs')){
			$data['specs']		= $request->get('specs');
		}
		if( $request->has('goods_num')){
			$data['goods_num']		= $request->get('goods_num');
		}

		$data['is_real']			= 1;
		if( $request->has('is_real')){
			$data['is_real']		= $request->get('is_real');
		}

		$data['market_price']		= 0;
		if( $request->has('market_price')){
			$data['market_price']	= $request->get('market_price');
		}

		$data['purchase_url']		= 0;
		if( $request->has('purchase_url')){
			$data['purchase_url']	= $request->get('purchase_url');
		}

		$data['create_time']		= time();

		$goodsModule = new ProductModel();

		$result = $goodsModule->addGoods($data);

		if($result){
		//	$goodsModule->addGoodsPic($result , $goodsImg);
		}

		$returnData['selected'] = "goods";
		if($result){
			return $this->response(1);
		}else{
			return $this->response(0);
		}
	}
}
