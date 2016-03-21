<?php
/*
 * Lumen微框架Api接口开发基类
 * 安全验证、登录验证、错误机制、response响应机制、日记记录
 * from  :www.sexyphp.com
 * author:lianger
 */
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends BaseController
{

    /*
	|--------------------------------------------------------------------------
	| Default Api Controller
	|--------------------------------------------------------------------------
	| API控制器，所有接口的父类。用于通用的验证和数据处理
	|
	*/
    public function __construct(Request $request){
    }

    /**
     * 定义响应数据规范
     * 语言:zh[中文简体]、en[英文]
     *
     * @param 	string	$code 	状态码
     * @param 	string	$msg 	状态码
     * @param 	string	$data 	状态码
     * @return	array
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
	 *	 *	 validate
	 *		 *
	 *			 * */
	protected function vd($rules, $request, $tip=array()){
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

		$validate = Validator::make($request->all(), $rules,$selfMessages);
		$messages = $validate->messages()->all();
		return implode(' & ', $messages);
	}

}
