<?php
/**
 * 用户重置密码相关接口操作
 *
 * @author		xuguangjing@shinc.net
 * @version		v1.0
 * @copyright	shinc
 */
namespace App\Http\Controllers\User;		// 定义命名空间
use App\Http\Controllers\ApiController;    //导入基类
use Illuminate\Http\Request;                //输入输出类
use App\Http\Models\User\NewUserModel;		//引入model
use App\Http\Models\User\VerifyModel;
use App\Http\Models\User\CommonsModel;      //公共模型

class ResetController extends ApiController
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

		if (!$request->has('tel')){
			return $this->response(10015);
		}
		$userModel = new NewUserModel();

		if (!$this->commontMdel->checkUser($request->get('tel'))){
			return $this->response(10022);

		}
		$verifyModel = new VerifyModel();
		$data = $verifyModel->sendVerifyCode($request->get('tel'));
		return $this->response($data);
	}

	/**
	 * 验证短信并设置会话接口
	 * @param tel 电话
	 * @param code 验证码
	 * @return json
	 */
	public function checkVerify(Request $request) {

		if (!$request->has('tel') || !$request->has('code'))
			return $this->response(10005);

		$verifyModel = new VerifyModel();
		if ($verifyModel->checkVerify($request->get('tel'), $request->get('code'))) {
			return $this->response(1);
		} else {
			return $this->response(0);
		}
	}


	/**
	 * 修改密码接口
	 *
	 * @param password 新密码
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function resetPwd(Request $request) {
		if (!$request->has('tel') || !$request->has('password')){
			return $this->response(10005);
		}

		if (  !$this->commontMdel->checkUser($request->get('tel'))) {
			return $this->response(20206);
		}

		$userModel = new NewUserModel();
		$data = $userModel->updatePwd($request->get('tel'), $this->commontMdel->encryptPassword($request->get('password'),''));
		if ($data) {
			return $this->response(1);
		}else{
			return $this->response(0);
		}
	}
}