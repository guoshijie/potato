<?php
/*
 * 控制器用例
 */
namespace App\Http\Controllers\Product;    //定义命名空间
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Http\Response;           //响应类
use App\Http\Models\Product\ProductModel;
use App\Http\Models\Product\CategoryModel;

class CategoryController extends ApiController
{
	protected $_model;
	public function __construct(){
		$this->_model = new ProductModel();
	}

	/*
	 * 商品分类列表
	 */
	public function getCategory(){
		$categoryModel = new CategoryModel();
		$list = $categoryModel->getGoodsCategoryList();
		$data['list'] = $list;
		return Response::view('admin.goods.category' , $data);
	}

	/*
	 * 添加分类
	 */
	public function add(Request $request){
		if(!$request->has('user_id') ){
			return $this->response(10018);
		}
		$messages = $this->vd([
			'cat_name' => 'required',
			'description' => 'required',
			], $request);

		if($messages!='') return $this->response(10005, $messages);

		$categoryModel = new CategoryModel();

		$newData['cat_name']	= $request->get('cat_name');
		$newData['description'] = $request->get('description');
		if($request->has('pid')){
			$newData['pid']			= $request->get('pid');
		}
		if($request->has('img_url')){
			$imgUrl					= $request->get('img_url');
			$newData['img_url']		= $imgUrl[0];
		}

		$newId = $categoryModel->addGoodsCategory($newData);
		return $this->response(1);
	}

	/*
	 * 编辑分类
	 */
	public function anyCategoryEdit(){
		if( !$request->has('id')){
			return Response::view('admin.goods.category_edit' , array('msg'=>'id错误'));
		}
		$categoryModel = new CategoryModel();
		if( !$request->has('cat_name')){
			$id = $request->get('id');
			$data['detail'] = $categoryModel->getGoodsCategoryDetail($id);
			return Response::view('admin.goods.category_edit' , $data);
		}

		$newData['id']			= $request->get('id');
		$newData['cat_name']	= $request->get('cat_name');
		$newData['description'] = $request->get('description');
		if($request->has('img_url')){
			$imgUrl					= $request->get('img_url');
			$newData['img_url']		= $imgUrl[0];
		}

		$newId = $categoryModel->editGoodsCategory($newData);

		return Redirect::to('/admin/goods/category-edit');
	}

	/*
	 *
	 */
	public function anyCategoryDel(){
		if( !$request->has('id') ){
			return Response::json( $this->response( '10005' ) );
		}
		$id = $request->get('id');
		$categoryModel = new CategoryModel();
		$isD = $categoryModel->delGoodsCategory($id);
		if($isD===false){
			$response = $this->response(0,'删除失败，请重试');
		}elseif($isD===0){
			$response = $this->response(0,'有子类存在，请先删除子类');
		}else{
			$response = $this->response(1,'删除成功');
		}
		return Response::json( $response );
	}
}
