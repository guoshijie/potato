<?php

/*
 * 登录控制器---模块化2.0
 * author：liangfeng@shinc.net
 */

namespace App\Http\Controllers\User;
use  App\Http\Controllers\ApiController;//导入基类
use Illuminate\Http\Request;          //输入输出类
use App\Http\Models\User\LoginModel; //登录块模型
use App\Http\Models\System\IpFactoryModel;
use App\Service\UserService;
use App\Http\Models\User\CommonsModel;  //公共模型

class LoginController extends ApiController
{
	protected $commontMdel;

	public function __construct(){
		$this->commontMdel = new CommonsModel();
	}

	/**
	 * 用户手机号密码登录
	 *
	 * @param string $tel 用户手机号
	 * @param string $password 密码
	 * @return json
	 */
	public function loginTel(Request $request) {

		if( !$request->has( 'tel' ) ||  !$request->has( 'password' ) ) {
			return $this->response('10005');
		}
		$tel        = $request->get( 'tel' );
		$password   = $request->get( 'password' );

		$loginM     = new LoginModel();

		//获取用户信息
		$userInfo   = $this->commontMdel->getUserInfoByMobile( $tel );

		if( empty( $userInfo ) ) {
			return $this->response('20202');
		}
		$userInfo = $userInfo[0];

		//判断帐号是否锁定
		if( 1 == $userInfo->locked ) {
			return $this->response('20204');
		}

		//记录登录次数
		$num  = $loginM->addLoginCount($request->get('token'));

		//登录次数超过6此拒绝锁定账户
		if( $num > 6  ) {
			//锁定帐号
			//$loginM->lockedUser( $userInfo->id );
			//return $this->response('20203');
		}
		$userPassword = $this->commontMdel->encryptPassword($password,$userInfo->salt);

		//判断用户密码是否正确
		if( $userInfo->password  != $userPassword ) {
			//echo $userInfo->password;exit;
			return $this->response('20202');
		}

		// 更新登录信息
		$loginM->updUser($userInfo->id, array('session_id'=> $request->get('token') ));

		unset($userInfo->password);
		unset($userInfo->salt);
		unset($userInfo->locked);

		$loginM->clearLoginCount();
		//记录日志
		$loginM->writeUserLog($userInfo->id, 'login', 'success');
		$this->dealDummyUser($userInfo);
		return $this->response( '1','成功',$userInfo);
	}

	/**
	 * 处理假用户IP
	 */
	public function dealDummyUser($userInfo) {
		if(empty($userInfo) || $userInfo->is_real != '0') {
			return ;
		}
		if(empty($userInfo->ip_address) && empty($userInfo->ip)) {
			$ipf = new IpFactoryModel();
			$ip = $ipf->getRandomIp();
			$addr = '';
			if($ip->province == $ip->city) {
				$addr = $addr . $ip->province . ' ';
			} else {
				if(!empty($ip->province)) {
					$addr = $addr . $ip->province . ' ';
				}
				if(!empty($ip->city)) {
					$addr = $addr . $ip->city . ' ';
				}
			}

			if(!empty($ip->county)) {
				$addr = $addr . $ip->county;
			}
			$param = [
				'sh_user_id' => $userInfo->id,
				'ip' => $ip->ip,
				'ip_address' => $addr
			];
			$um = $this->commontMdel;

			$uf = $um->findByUserId($userInfo->id);
			if(empty($uf)) {
				$um->add($param);
			} else {
				$um->update($uf->id,$param);
			}
			$tosession = [
				'is_real' => '0',
				'ip' => $ip->ip,
				'ip_address' => $addr
			];
			UserService::addParamToSessionUser($tosession);
			return $ip;
		} else {
			$tosession = [
				'is_real' => '0',
				'ip' => $userInfo->ip,
				'ip_address' => $userInfo->ip_address
			];
			UserService::addParamToSessionUser($tosession);
		}
	}
}
