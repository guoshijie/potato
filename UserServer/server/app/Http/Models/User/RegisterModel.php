<?php
/*
 * 手机号注册模型---模块化2.0
 * author：liangfeng@shinc.net
 */
namespace App\Http\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\User\CommonsModel;

class RegisterModel extends Model{

	/**
	 * 用户注册
	 */
	public function addUser( $tel, $password ) {

		$commont = new CommonsModel();
		$salt = '';
		$password = $commont->encryptPassword($password,$salt);
		date_default_timezone_set('PRC');
		$createTime = time();
		$randCode = $this->randCode();
		$data = DB::table('user')->insert(array(
			'tel'                   => $tel,
			'password' => $password,
			'nick_name'             => preg_replace("/^(\d{3})\d*?(\d{3})$/", "$1*****$2", $tel),
			'login_sms_code'        => $randCode,
			'login_sms_code_expire' => time() + 5 * 60,
			'create_time'           => $createTime,
			'os_type'				=> '00',
		));

		return $data;
	}


	/**
	 * 生成验证码
	 * @param number $len
	 * @return string
	 */
	private function randCode($len = 4) {
		$randString = '';
		for ($i=1;$i<=$len;$i++) $randString .= mt_rand(0, 9);
		return $randString;
	}
}
