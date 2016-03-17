<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;             //输入输出类
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class ApiController extends Controller
{

	public function __construct( ){
	}

	protected $loginUser;
	protected function isLogin(){
		if(Request::isJson()) {  // 兼容特殊情况
			$token = Request::json('token');
		} elseif(Request::has('token')) {
			$token = Request::get('token');
		}

		if(isset($token)){
			$userToken = Config::get('cache.token_prefix') . $token;
			if(Cache::has($userToken)){
				$user = (object)Cache::get($userToken);
				if(isset($user->id)){
					$user->token = $token;
					$this->loginUser = $user;
					return true;
				}
			}
		}
		return false;
	}


    /**
    * 定义响应数据规范
    * 语言:zh[中文简体]、en[英文]
    *
    * @param    string  $code   状态码
    * @param    string  $msg    状态码
    * @param    string  $data   状态码
    * @return   array
    */
    public function response( $code, $msg = '', $data = null ) {
		if( '' == $msg ) {
			$codeMsg = $this->getCodeMsg();
			if( !array_key_exists( $code, $codeMsg ) ) {
				return 'code is non-existent';
			}
			$msg = $codeMsg[ $code ]['zh'];
		}

        $ret = new \stdClass();
        $ret->code  = (int)$code;
        $ret->msg   = (string)$msg;

	    if(null != $data){
		    $ret->data  = $data;
	    }
        return $ret;
    }

	/*
	 * 定义通用报错列表
	*
	* @return	array
	*/
	public function getCodeMsg(){
		return include(app_path().'/../resources/code_msg.php');
	}

	/*
	 * validate
	 * $rules = ['code' => 'required|min:4']
	 * $tip = ['code'=>'验证码'];
	 */
	protected function vd($rules, $tip=array()){
		// 自定义错误信息，不谢用默认的
		$selfMessages = array(
			'required' => '请填写 :attribute ;',
			'same'    => 'The :attribute and :other must match;',
			'size'    => 'The :attribute must be exactly :size;',
			'between' => 'The :attribute must be between :min - :max;',
			'in'      => 'The :attribute must be one of the following types: :values ;',
		);

		foreach($rules as $k=>$vr){
			if(isset($tip[$k])){
				$selfMessages[$k.'.'.$vr] = str_replace(':attribute', $tip[$k], $selfMessages[$vr]);
			}
		}

		$validate = Validator::make(Request::all(), $rules, $selfMessages);
		$messages = $validate->messages()->all();
		return implode(' ', $messages);
	}

}
