<?php namespace Api\Server;
/*
 * 获取用户信息服务功能接口
 * author：liangfeng
 */
use App\Libraries\Api;
use App\Libraries\Curl;
use Seld\JsonLint\JsonParser;

class User extends Api
{
	const TEST = "TEST";
	const USER_HOST = "user.server.potato";

	public function __construct()
	{
		parent::__construct(User::USER_HOST);
	}


	/*
	* 注册获取验证码
	* @param $tel  用户号
	*/
	public function getVerify($tel){
		return $this->getData("/user/register/send-register-verify?tel=" . $tel);
	}


	/*
	 * 检测验证码是否正确
	 * $param $tel  手机号
	 * $param $code 验证码
	 */
	public function checkVerify($tel,$code){
		return $this->getData("/user/register/check-verify?tel=" . $tel.'&code='.$code);
	}


	/*
	 * 手机号注册
	 */
	public function register($tel,$password,$code){
		return $this->getData("/user/register/register?tel=" . $tel.'&code='.$code.'&password='.$password);
	}


	/*
	 * 用户登录server接口
	 * @param $tel          用户号
	 * @param $password     密码
	 * @param $key          用户密码和token加密字符串
	 * @param $form_token          token字符串
	 */
	public function login($tel,$password){
		return $this->getData("/user/login/login-tel?tel=" . $tel.'&password='.$password);
	}


	/*
	 * 用户登录所需Tokenserver接口
	 * @param $tel          用户号
	 * @param $password     密码
	 * @param $key          用户密码和token加密字符串
	 * @param $form_token   token字符串
	 */
	public function getToken(){
		return $this->getData("/user/login/token");
	}


	/*
	 * 用户重置密码时获取验证码server接口
	 * @param $tel          用户号
	 */
	public function resetVerify($tel){
		return $this->getData("/user/reset/get-verify?tel=".$tel);
	}


	/*
	 * 用户重置密码
	 * @param $tel          用户号
	 */
	public function reset($tel,$password,$code){
		return $this->getData("/user/reset/reset-password?tel=".$tel."&password=".$password."&code=".$code);
	}


	/**
	 * 修改用户头像
	 *
	 * @param string $head_pic 用户头像
	 * @return json
	 */
	public function editHeadPic($head_pic,$user_id){
		return $this->getData("/user/reset/edit-head?head_pic=".$head_pic."&user_id=".$user_id);
	}


	/*
	 * 添加收货地址
	 */
	public function add_address($user_id,$name,$tel,$district,$address,$head_name,$code){
		return $this->getData("/user/shop/create-shop?user_id=".$user_id."&name=".$name."&tel=".$tel."&district=".$district."&address=".$address."&head_name=".$head_name."&code=".$code);
	}


	/*
	 * 获取收货地址列表
	 */
	public function showShopList($user_id,$page){
		return $this->getData("/user/shop/get-shop?user_id=".$user_id."&page=".$page);
	}


	/*
	 * 修改收货信息
	 */
	public function editShop($id,$user_id,$name,$tel,$district,$address,$head_name,$code,$is_default){
		return $this->getData("/user/shop/edit-shop?user_id=".$user_id."&name=".$name."&tel=".$tel."&district=".$district."&address=".$address."&head_name=".$head_name."&code=".$code."&isDefault=".$is_default."&id=".$id);
	}



	/*
	 * 删除收货信息
	 */
	public function destroyShop($address_id,$user_id){
		return $this->getData("/user/shop/delete-shop?address_id=".$address_id."&user_id=".$user_id);
	}

	/*
	 * 获取默认收货地址
	 */
	public function getAddressDefault($user_id){
		return $this->getData("/user/shop/get-shop-default?user_id=".$user_id);
	}


	/*
	 * 设置默认收货地址
	 */
	public function setAddressDefault($user_id,$id){
		return $this->getData("/user/shop/set-shop-default?user_id=".$user_id."&id=".$id);
	}


	/*
	 * 获取七牛Token
	 */
	public function qiniuToken(){
		return $this->getData("/user/qiniu/token“);
	}
	
}