<?php
/**
 * User: zhangtaichao
 * Date: 15/11/4
 * Time: 上午11:15
 */

namespace App\Service;
use Illuminate\Support\Facades\Session;
use App\Http\Models\User\LoginModel;


class UserService {

    public static function getCurrentUser() {
        $userInfo = Session::get('user');
        if(empty($userInfo) || !is_array($userInfo)) {
           return null;
        } else {
			// 单点登录,检查session id
			$loginM = new LoginModel();
			$rs = $loginM->checkSessionId($userInfo['id'], Session::getId());
			if(!$rs){
				return null;
			}
            return $userInfo;
        }
    }
    public static function getCurrentUserId() {
        $userInfo = self::getCurrentUser();
        if(empty($userInfo)) {
            return null;
        } else {
            return $userInfo['id'];
        }
    }

    public static function updateSessionUser($array) {
        if(!is_array($array)) {
            return ;
        }
        Session::put('user',$array);
    }

    public static function addParamToSessionUser($param) {
        $user = self::getCurrentUser();
        if(empty($user) || !is_array($param)) {
            return;
        }
        self::updateSessionUser(array_merge($user,$param));
    }

    public static function setIPInfo($ip) {
        $user = self::getCurrentUser();
        if(empty($user)) {
            return false;
        }
        if(!isset($user['is_real']) || $user['is_real'] != '0') {
            $res = IPService::find($ip);
            if(empty($res)) {
                return false;
            } else {
                if(count($res) > 2) {
                    if($res['province'] == $res['city']) {
                        array_splice($res,0,2);
                    } else {
                        array_splice($res,0,1);
                    }
                }
                $addr = implode(' ',$res);
                $param = [
                    'ip' => $ip,
                    'ip_address' => $addr
                ];
                self::addParamToSessionUser($param);
                return true;
            }
        }
    }

}
