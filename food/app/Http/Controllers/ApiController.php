<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Support\Facades\Session;

class ApiController extends Controller
{

	public function __construct(){

		if(Request::has('token')){
			Session::setId(Request::only('session_id'));
		}

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
    public function response( $code, $msg = '', $data = array() ) {
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

	    if(!empty($data)){
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


}
