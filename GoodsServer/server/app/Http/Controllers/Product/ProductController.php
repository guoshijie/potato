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
		if($data){
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
}
