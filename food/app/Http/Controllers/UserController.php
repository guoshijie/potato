<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;          //输入输出类
use Illuminate\Support\Facades\Response;
use \Api\Server\User as UserServer;
use Illuminate\Support\Facades\Session;
//use \Api\Server\AutoId;
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

		$userM = new UserModel();

		$tel        =   Request::get('tel');
		$password   =   Request::get('password');

		$list       =  $this->userServer->login($tel,$password);

		//return $list;
		$list_array       =   json_decode($list);

//		$this->pr($list_array->data->data->id);
		if($list_array->code == 1){
			//存储session
			$user_session = array(
				'id' 		=> $list_array->data->data->id,
				'user_id' 	=> $list_array->data->data->id,
				'tel'		=> $list_array->data->data->tel,
				'nick_name'	=> $list_array->data->data->nick_name,
				'ip'	    => $list_array->data->data->ip,
				'ip_address'=> $list_array->data->data->ip_address,
				'is_real'	=> $list_array->data->data->is_real
			);

			if($list_array->data->data->is_real){
				config('session.lifetime',43200);
			}

			Session::put('user', $user_session);

			//$this->pr(Session::getId());
			//记录session_id 作单点登录验证
			$userM->updateSessionId( $list_array->data->data->id, Session::getId() );

			$userInfo = $userM->getUserInfoByMobile($tel);
			return Response::json($this->response(1,'登录成功',$userInfo[0]));
		}else{
			return Response::json($list_array);
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
		if(!Request::has('password')){
			return Response::json($this->response(10005));
		}

		if(!Session::has('user.id')){
			return Response::json($this->response(99999));
		}

		$tel        =   Session::get('user.tel');
		$password   =   Request::get('password');

		return $this->userServer->reset($tel,$password);
	}


	public function headPic(){
		if(!Request::has('head_pic') || !Request::has('head_pic')){
			return Response::json($this->response(10005));
		}

		if(!Session::has('user.id')){
			return Response::json($this->response(99999));
		}

		$user_id    =   Session::get('user.id');

		$head_pic   =   Request::get('head_pic');

		return $this->userServer->editHeadPic($head_pic,$user_id);
	}


	public function addAddress(){
		if(!Request::has('name') || !Request::has('tel') || !Request::has('district') || !Request::has('address') || !Request::has('head_name') || !Request::has('code')){
			return Response::json($this->response(10005));
		}

		if(!Session::has('user.id')){
			return Response::json($this->response(99999));
		}

		$user_id    =   Session::get('user.id');
		$name       =   Request::get('name');
		$tel        =   Request::get('tel');
		$district   =   Request::get('district');
		$address    =   Request::get('address');
		$head_name  =   Request::get('head_name');
		$code       =   Request::get('code');

		return $this->userServer->add_address($user_id,$name,$tel,$district,$address,$head_name,$code);
	}


	public function showShopList(){
		if(!Session::has('user.id')){
			return Response::json($this->response(99999));
		}

		if(!Request::has('page')){
			return Response::json($this->response(10005));
		}

		$user_id    =   Session::get('user.id');
		$page       =   Request::get('page');


		return $this->userServer->showShopList($user_id,$page);
	}


	public function editShop(){
		if(!Request::has('name') || !Request::has('tel') || !Request::has('district') || !Request::has('address') || !Request::has('head_name') || !Request::has('code') || !Request::has('is_default') || !Request::has('id')){
			return Response::json($this->response(10005));
		}

		if(!Session::has('user.id')){
			return Response::json($this->response(99999));
		}

		$user_id    =   Session::get('user.id');
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

		if(!Session::has('user.id')){
			return Response::json($this->response(99999));
		}

		$user_id    =   Session::get('user.id');

		$address_id   =   Request::get('address_id');

		return $this->userServer->destroyShop($address_id,$user_id);
	}


	public function getAddressDefault(){

		if(!Session::has('user.id')){
			return Response::json($this->response(99999));
		}

		$user_id   =   Session::get('user.id');

		return $this->userServer->getAddressDefault($user_id);
	}



	/**
	 * 退出登录
	 *
	 */
	public function  logout() {

		if( !Request::has('uid') ) {

			return Response::json( $this->response( '10005' ) );
		}

		$userId = Request::get( 'uid' );

		$userM = new UserModel();
		//clear session
		$this->clearSession();
		//记录日志
		$userM->writeUserLog($userId, 'logout', 'success');

		return Response::json( $this->response( 1 ) );
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


	function pr($data){
		echo "<pre>";

		print_r($data);

		echo "</pre>";

	}



	/**
	 * 清理session
	 */
	public function clearSession() {
		Session::flush();
	}





}
