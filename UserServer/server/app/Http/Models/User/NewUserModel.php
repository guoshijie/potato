<?php
namespace App\Http\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
class NewUserModel extends Model {

	/**
	 * 重置密码
	 * @param string $tel 电话号码
	 * @param string $password 新密码
	 * @return boolean
	 */
	public function updatePwd($tel, $password) {
		return DB::table('user')->where('tel', $tel)->update(array('password' => $password));
	}


	/**
	 * 检测登录信息
	 *
	 * @param	string	$tel 	手机号
	 * @param	string	$password 	密码
	 * @return	array
	 */
	public function checkLogin($tel, $password) {
		$num = DB::table('user')->where('tel', $tel)->count();
		if ($num <= 0) return array('code' => '-1', 'msg' => '帐号不存在,请先注册!');
		$num = DB::table('user')->where('tel', $tel)->where('password', $password)->count();
		if ($num <= 0) return array('code' => '0', 'msg' => '密码错误');
		else return array('code' => '1', 'msg' => '登录成功');
	}


	/**
	* 用户注册
	*  
	*
	*/
	public function addUser( $tel, $password ) {
		date_default_timezone_set('PRC');
		$createTime = time();
		$data = DB::table('user')->insert(array(
			'tel' => $tel,
			'password' => $password,
			'create_time' => $createTime
		));
        return $data;
	}
 

}