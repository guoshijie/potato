<?php
/**
 * 获取token 
 *
 * @version		v1.0
 * @copyright	shinc
 */

namespace App\Http\Controllers\User;			// 定义命名空间

use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;            //输入输出类
use Illuminate\Support\Facades\Session; //session
use App\Libraries\TokenUtil;//引入token工具类

class TokenController extends ApiController {


	public function token(){
		$token = TokenUtil::getToken();
		if($token){
			return $this->response(1, '成功' ,$token);
		}else{
			return $this->response('10019');
		}
	}
}
