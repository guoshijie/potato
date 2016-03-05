<?php
/**
 * 设置商户信息操作---模块化2.0
 *
 * @author		liangfeng@shinc.net
 * @version		v1.0
 * @copyright	shinc
 */

namespace App\Http\Controllers\Shop;			// 定义命名空间

use App\Http\Controllers\ApiController;         //导入基类
use Illuminate\Http\Request;                    //输入输出类
use App\Http\Models\User\CommonsModel;          //公共模型
use App\Http\Models\Shop\ShopModel;     //引入model

/**
 * 	controller的写法：首字母大写，于文件名一致。 继承的父类需引入
 */ 
class ShopController extends ApiController
{
	private $_model;    //定义private私有成员变量，通常$_ 开头
	protected $commontMdel;

	public function __construct()
	{
		$this->_model = new ShopModel();
		$this->commontMdel = new CommonsModel();
	}
	

	/**
	 * 获取中国地区表
	 * @return    json
	 */
	public function area()
	{
		$data = $this->_model->getArea();
		if ($data) {
			return $this->response(1, '获取成功', $data);
		} else {
			return $this->response(0);
		}
	}


	/**
	 * 添加商品地址
	 * @description 一个手机号仅支持绑定一个商品地址，新增商品地址设置为默认商品地址
	 * @param user_id   用户id
	 * @param name      联系人
	 * @param mobile    手机号码
	 * @param area      地区
	 * @param address   详细地址
	 * @return    json
	 */
	public function createShop(Request $request)
	{
		$messages = $this->vd([
			'name' => 'required',
			'tel' => 'required',
			//'code' => 'required',
			], $request);

		if($messages!=''){
			return $this->response(10005, $messages);
		}
/*
		if (!$request->has('name') || !$request->has('tel') || !$request->has('district') || !$request->has('address') || !$request->has('head_name') || !$request->has('user_id') || !$request->has('code')) {
			return $this->response(10005);
		}
 */
		$user_id   = $request->get('user_id');
		$name      = $request->get('name');
		$tel       = $request->get('tel');
		$district  = $request->get('district');
		$address   = $request->get('address');
		$head_name = $request->get('head_name');
		$code = $request->has('code') ? $request->get('code') : 0;
		//根据用户id获取用户信息
		$Users = $this->commontMdel->getUserInfoByUserId($user_id);
		if (!$Users) {
			return $this->response(0);
		} else {
			//根据手机号查询是否已添加
//			$isAddress = $this->_model->getAddressByTel($tel);
//			if ($isAddress) {
//				return $this->response(20212);
//			}

			$check      = $this->commontMdel->checkVerifyCode( $tel , $code  );
			if(!$check){
				return $this->response(20208);
			}

			$data = $this->_model->addAddress($user_id, $name, $tel, $district, $address, $head_name);
			if ($data) {
				return $this->response(1, '添加成功', $data);
			} else {
				return $this->response(0, '添加失败');
			}
		}
	}

	/*
	 * 获取默认收货地址
	 */
	public function getShopDefault(Request $request)
	{
		$messages = $this->vd([
			'user_id' => 'required',
			], $request);

		if($messages!=''){
			return $this->response(10018, $messages);
		}




		$userId = $request->get('user_id');
		$data = $this->_model->getDefaultAddress($userId);
		if ($data) {
			return $this->response(1, '获取成功', $data);
		} else {
			return $this->response(0, '获取失败');
		}
	}


	/**
	 * 根据用户id获取收货地址列表
	 * @param user_id   用户id
	 * @return    json
	 */
	public function showShopList(Request $request)
	{
		if (!$request->has('user_id')) {
			return $this->response(10018);
		}

		$pageinfo = $this->pageinfo($request);


		$userId = $request->get('user_id');
		$data = $this->_model->getAddressList($userId,$pageinfo->offset , $pageinfo->length);
		if ($data) {
			return $this->response(1, '获取成功', $data);
		} else {
			return $this->response(0, '获取失败');
		}
	}


	/**
	 * 根据收货地址id修改收货信息(也可修改为默认收货地址)
	 * @param address_id    收货地址id
	 * @param name        收货人姓名
	 * @param mobile        手机号码
	 * @param area        地区
	 * @param address    详细地址
	 * @return    json
	 */
	public function editShop(Request $request)
	{
		if (!$request->has('id') || !$request->has('name') || !$request->has('tel') || !$request->has('district') || !$request->has('address') || !$request->has('isDefault') || !$request->has('head_name') || !$request->has('user_id') || !$request->has('code')) {
			return $this->response(10005);
		}

		//name=梁枫&tel=18612579961&district=北京市&address=澶阳区都第三季&head_name=世和科技&code=458991&is_default=1&id=210

		//$user_id,$name,$tel,$district,$address,$head_name,$code,$is_default,$id
		$user_id    = $request->get('user_id');
		$address_id = $request->get('id');
		$name       = $request->get('name');
		$tel        = $request->get('tel');
		$district   = $request->get('district');
		$address    = $request->get('address');
		$isDefault  = $request->get('isDefault');
		$head_name  = $request->get('head_name');
		$code       = $request->get('code');
		//判断收货地址是否存在
		$isAddressId = $this->_model->isAddressById($address_id);
		if (!$isAddressId) {
			return $this->response(20211);
		}
		//判断除该address_id外的手机号是否与修改的手机号冲突
//		$issetPhone = $this->_model->issetAddressPhone($address_id, $tel, $user_id);
//		if ($issetPhone) {
//			return $this->response(20212);
//		}

		$check      = $this->commontMdel->checkVerifyCode( $tel , $code  );
		if(!$check){
			return $this->response(20208);
		}

		//修改收货地址
		$data = $this->_model->editAddressByAddressId($user_id, $address_id, $name, $tel, $district, $address, $isDefault, $head_name);
		if ($data) {
			return $this->response(1, '修改成功', $data);
		} else {
			return $this->response(0, '修改失败');
		}
	}



	/*
	 * 设置默认收货地址
	 */
	public function addressDefault(Request $request){
		if (!$request->has('id') || !$request->has('user_id')){
			return $this->response(10005);
		}

		$user_id    = $request->get('user_id');

		$data = $this->_model->setDefault($user_id,$request->get('id'));

		if ($data) {
			return $this->response(1, '设置成功', $data);
		} else {
			return $this->response(0, '设置失败');
		}
	}




	/**
	 * 根据收货地址id删除收货信息
	 * @param address_id   收货地址id
	 * @return    json
	 */
	public function destroyShop(Request $request)
	{
		if (!$request->has('address_id')) {
			return $this->response(10005);
		}
		if (!$request->has('user_id')) {
			return $this->response(10018);
		}
		$user_id    = $request->get('user_id');
		$address_id = $request->get('address_id');
		$data       = $this->_model->DeleteAddressByAddressId($address_id, $user_id);
		if ($data) {
			return $this->response(1, '删除成功', $data);
		} else {
			return $this->response(0, '删除失败');
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
