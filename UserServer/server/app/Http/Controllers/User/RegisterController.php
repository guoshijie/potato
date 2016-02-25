<?php

/*
 * 手机号注册控制器---模块化2.0
 * author：liangfeng@shinc.net
 */

namespace App\Http\Controllers\User;
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use App\Http\Models\User\SmsModel;      //短信块模型
use App\Http\Models\User\RegisterModel; //注册块模型
use App\Http\Models\User\CommonsModel;  //公共模型

class RegisterController extends ApiController
{

	protected $commontMdel;

	public function __construct(){
		$this->commontMdel = new CommonsModel();
	}
	/**
	 * 发送短信接口
	 * @param string $tel 电话号码
	 * @return json
	 */
	public function sendVerifyCode(Request $request) {
		if (!$request->has('tel')) {
			return $this->response(10015);
		}

		$SmsM      = new SmsModel();
		if ($this->commontMdel->checkUser($request->get('tel'))){
			return $this->response(10014);
		}
		$data = $SmsM->sendVerifyCode($request->get('tel'));
		return $this->response($data);
	}


	/**
	 *	校验验证码是否正确
	 *	@param  $code 验证码
	 *	@param  $tel  手机号
	 *	@return json
	 */
	public function checkVerify(Request $request){
		if( !$request->has('code') || !$request->has('tel') ){
			return $this->response('10005');
		}
		$code = $request->get('code');
		$tel  = $request->get('tel');

		$data = $this->commontMdel->checkVerifyCode( $tel , $code  );
		//判断验证码是否正确
		if( !$data ) {
			return $this->response(20208);
		}else{
			return $this->response(20210);
		}
	}


	/**
	 *	用户注册
	 *	@param  $tel
	 *	@param  $password
	 */
	public function register(Request $request) {

		if (!$request->has('tel') || !$request->has('password') || !$request->has('code')){
			return $this->response(10005);
		}
		$tel        = $request->get('tel');
		$password   = $request->get('password');
		$code       = $request->get('code');

		$registerM  = new RegisterModel();
		if (  $this->commontMdel->checkUser($tel)) {
			return $this->response(20206);
		}
		$check      = $this->commontMdel->checkVerifyCode( $tel , $code  );
		if(!$check){
			return $this->response(20208);
		}
		$data       = $registerM->addUser($tel, $password );
		if ($data) {
			return $this->response(1,'注册成功');
		} else{
			return $this->response(0,'注册失败');
		}
	}



}