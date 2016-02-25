<?php
/**
 * 用户信息控制器
 *
 * @author		liangfeng@shinc.net
 * @version		v1.0
 * @copyright	shinc
 */
namespace App\Http\Controllers\User;		// 定义命名空间
use App\Http\Controllers\ApiController;    //导入基类
use Illuminate\Http\Request;                //输入输出类
use App\Http\Models\User\ResetModel;			//引入model

class UserController extends ApiController
{

	public function __construct() {

	}

	/**
	 * 修改用户头像
	 *
	 * @param string $head_pic 用户头像
	 * @return json
	 */

	public function editHeadPic(Request $request) {
		if( !$request->has('head_pic') || !$request->has('user_id') ) {
			return $this->response(10005);
		}
		$user = new ResetModel();
		$head_pic = $request->get( 'head_pic' );
		$user_id  = $user->findByUserId($request->get('user_id'));

		if(!$user_id){
			return $this->response(10013);
		}
		$res = $user->updateHeadPic( $user_id->id, $head_pic );
		if( $res ) {
			return $this->response(1);
		}else{
			return $this->response(0);
		}

	}

}