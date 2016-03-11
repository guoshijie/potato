<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\User as UserServer;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use \Api\Server\AutoId;
//use \Api\Server\AdvertServer\Banner;
use \App\Libraries\Curl;
use App\Http\Controllers\ApiController;
use App\Http\Models\UserModel;
class UserController extends ApiController
{

	var $userServer;
	var $autoIdServer;

	public function __construct()
	{
		parent::__construct();
		$this->userServer = new UserServer();
		//$this->autoIdServer = new AutoId();
	}


	public function verify(){

		if(!Request::has('tel')){
			return Response::json($this->response(10005));
		}

		$tel_input    =   Request::get('tel');

		$tel    =   $this->isMobile($tel_input);

		if(!$tel){
			return Response::json($this->response(10022));
		}

		return $this->userServer->getVerify($tel_input);

	}


	public function checkVerify(){
		if(!Request::has('tel') || !Request::has('code')){
			return Response::json($this->response(10005));
		}

		$tel    =   Request::get('tel');
		$code   =   Request::get('code');

		return $this->userServer->checkVerify($tel,$code);
	}


	public function register(){
		if (!Request::has('tel') || !Request::has('password') || !Request::has('code')){
			return Response::json($this->response(10005));
		}

		$tel        =   Request::get('tel');
		$password   =   Request::get('password');
		$code       =   Request::get('code');

		return $this->userServer->register($tel,$password,$code);
	}


	public function token(){
		return $this->userServer->getToken();
	}

	public function login(){
		if (!Request::has('tel') || !Request::has('password') ){
			return Response::json($this->response(10005));
		}

		$tel        = Request::get('tel');
		$password   = Request::get('password');
		$token		= str_random(40);

		$userInfo   = $this->userServer->login($tel,$password, $token);
		$userInfo   = json_decode($userInfo);

//		$this->pr($userInfo->data->data->id);
		if($userInfo->code == 1){
			//存储session
			$user_session = array(
				'id' 		=> $userInfo->data->id,
				'is_real'	=> $userInfo->data->is_real
			);
			$login_info = array(
				'user_id' 		=> $userInfo->data->id,
				'tel'		=> $userInfo->data->tel,
				'nick_name'	=> $userInfo->data->nick_name,
				'head_pic'	=> $userInfo->data->head_pic
			);

			$address	= $this->userServer->getAddressDefault($userInfo->data->id);
			$address	= json_decode($address);
		//header("Content-type:text/html;charset=utf-8");
			if($address->code){
				$login_info['default_address'] = $address->data;
			}


			if($userInfo->data->is_real){
				//config('session.lifetime',432000);
			}

			// cache 方式存储登录信息
			$prefix = Config::get('cache.token_prefix');
			Cache::put($prefix.$token, $user_session, 60*24*30*12);
			Cache::forget($prefix.$userInfo->data->old_token);

			//$this->pr(Session::getId());
			//记录session_id 作单点登录验证
			//$userM->updateSessionId( $userInfo->data->data->id, Session::getId() );

			//$userInfo = $userM->getUserInfoByMobile($tel);
			$login_info['token'] = $token;
			return Response::json($this->response(1,'登录成功',$login_info));
		}else{
			return Response::json($userInfo);
		}

	}



	public function resetVerify(){
		if(!Request::has('tel')){
			return Response::json($this->response(10005));
		}

		$tel_input    =   Request::get('tel');

		$tel          =   $this->isMobile($tel_input);

		if(!$tel){
			return Response::json($this->response(10022));
		}

		return $this->userServer->resetVerify($tel_input);
	}


	public function reset(){
		if(!Request::has('tel') || !Request::has('password') || !Request::has('code')){
			return Response::json($this->response(10005));
		}


		$tel        =   Request::get('tel');
		$password   =   Request::get('password');
		$code       =   Request::get('code');

		return $this->userServer->reset($tel,$password,$code);
	}


	public function headPic(){
		if(!Request::has('head_pic') || !Request::has('head_pic')){
			return Response::json($this->response(10005));
		}

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}
		$user_id    =   $this->loginUser->id;

		$head_pic   =   Request::get('head_pic');

		return $this->userServer->editHeadPic($head_pic,$user_id);
	}


	public function addAddress(){
		$messages = $this->vd([
			'name' => 'required',
			'tel' => 'required',
			'district' => 'required',
			'address' => 'required',
			'head_name' => 'required',
			//'code' => 'required',
		]);

		if($messages!='') return Response::json($this->response(10005, $messages)); 

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id    =   $this->loginUser->id;
		$name       =   Request::get('name');
		$tel        =   Request::get('tel');
		$district   =   Request::get('district');
		$address    =   Request::get('address');
		$head_name  =   Request::get('head_name');
		$code       =   Request::has('code') ? Request::get('code') : 1234;

		return $this->userServer->add_address($user_id,$name,$tel,$district,$address,$head_name,$code);
	}


	public function showShopList(){

		if(!Request::has('page')){
			$page   = 1;
		}else{
			$page       =   Request::get('page');
		}


		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}
		$user_id    =   $this->loginUser->id;

		return $this->userServer->showShopList($user_id,$page);
	}


	public function editShop(){
		if(!Request::has('name') || !Request::has('tel') || !Request::has('district') || !Request::has('address') || !Request::has('head_name') || !Request::has('code') || !Request::has('is_default') || !Request::has('id')){
			return Response::json($this->response(10005));
		}

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}
		$user_id    =   $this->loginUser->id;
		$name       =   Request::get('name');
		$tel        =   Request::get('tel');
		$district   =   Request::get('district');
		$address    =   Request::get('address');
		$head_name  =   Request::get('head_name');
		$code       =   Request::get('code');
		$is_default =   Request::get('is_default');
		$id         =   Request::get('id');

		return $this->userServer->editShop($id,$user_id,$name,$tel,$district,$address,$head_name,$code,$is_default);
	}


	public function destroyShop(){
		if(!Request::has('address_id')){
			return Response::json($this->response(10005));
		}

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}
		$user_id        =   $this->loginUser->id;

		$address_id     =   Request::get('address_id');

		return $this->userServer->destroyShop($address_id,$user_id);
	}


	public function getAddressDefault(){
		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}

		$user_id   = $this->loginUser->id;

		return $this->userServer->getAddressDefault($user_id);
	}


	/*
	 * 设置默认收货地址
	 */
	public function setAddressDefault(){

		if(!Request::has('address_id')){
			return Response::json($this->response(10005));
		}

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}
		$user_id        =   $this->loginUser->id;
		$address_id     =   Request::get('address_id');
		return $this->userServer->setAddressDefault($user_id,$address_id);
	}



	/**
	 * 退出登录
	 *
	 */
	public function  logout() {
		if(!$this->isLogin()){
			//return Response::json($this->response(99999));
			return Response::json( $this->response( 1 ,'退出成功') );
		}

		//clear session
		Session::flush();
		// clear Cache
		$prefix = Config::get('cache.token_prefix');
		$ss = Cache::forget($prefix.$this->loginUser->token);

		//记录日志
		//$userM = new UserModel();
		//$userM->writeUserLog($userId, 'logout', 'success');

		return Response::json( $this->response( 1 ,'退出成功') );
	}


	/*
	 * 获取七牛上传Token
	 */
	public function getQiniuToken(){
		return $this->userServer->qiniuToken();
	}


	/*
	 * 意见反馈
	 */
	public function setOpinion(){
		if(!Request::has('content')){
			return Response::json($this->response(10005));
		}

		if(!$this->isLogin()){
			return Response::json($this->response(99999));
		}
		$user_id        =   $this->loginUser->id;
		$content        =   Request::get('content');
		return $this->userServer->setOpinion($user_id,$content);
	}


	 /**
	 * 验证手机号是否正确
	 * @param int $mobile
	 */
	private function isMobile($mobile) {
		if (!is_numeric($mobile)) {
			return false;
		}
		return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
	}
}
