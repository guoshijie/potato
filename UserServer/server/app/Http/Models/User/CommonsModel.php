<?php
/*
 * 公共模型---模块化2.0
 * author：liangfeng@shinc.net
 */
namespace App\Http\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CommonsModel extends Model{

	/**
	 * 检测用户是否注册过
	 * @param string $tel 电话号码
	 * @return boolean
	 */
	public function checkUser($tel) {
		$data = DB::table('user')->where('tel', $tel)->count();
		if ($data > 0) return true;
		else return false;
	}

	/**
	 * 检测用户手机号是否存在
	 *
	 * @param string $tel 手机号
	 * @return boolean
	 */
	public function checkUserTel( $tel ) {

		$res = DB::table( 'user' ) ->select( 'id' )
			->where( 'tel', $tel )
			->get();

		if( empty( $res ) ) {
			return true;
		}else{
			return false;
		}
	}


	/**
	 * 检测短信验证码
	 *
	 * @param	string	$tel 手机号
	 * @param	string	$code	验证码
	 * @return boolean
	 */
	public function checkVerifyCode( $tel, $code ) {
		$res = DB::table( 'user_vertify_code' ) ->select('id')
			->where( 'tel', $tel )
			->where( 'vertify_code', strtoupper($code) )
			->where( 'live_time', '>', time() )
			->count();

		return  $res;
	}


	/*
	 * 根据用户ID获取用户信息
	 */
	public function findByUserId($user_id) {
		return DB::table('user')->where('id',$user_id)->first();
	}

	/**
	 * 根据手机号获取用户基本信息
	 *
	 * @param string $tel 手机号
	 * @return array 用户基本信息
	 */
	public function getUserInfoByMobile( $tel ) {
		$row = DB::table( 'user' )
			->leftJoin('user_info','user_info.sh_user_id','=','user.id')
			->where( 'tel', $tel )
			->select( 'user.id','user.tel', 'user.real_name', 'user.nick_name', 'user.password', 'user.locked', 'user.sh_id','user.signature','user.head_pic','user.money','user.salt','user.is_real','user_info.ip_address','user_info.ip','user.os_type','user.is_new_user' )
			->get();
		if($row){
			$row[0]->money = intval($row[0]->money);
		}

		return $row;
	}

	/**
	 * 根据用户id获取用户基本信息
	 */
	public function getUserInfoByUserId($userId) {
		return DB::table( 'user' )
			->select( 'id', 'tel', 'real_name', 'nick_name', 'password', 'locked', 'sh_id', 'email', 'status' )
			->where( 'id', $userId )
			->get();
	}


	/*
	 * 加密密码
	 */
	public function encryptPassword($password, $salt){
		$password = strtolower($password);
		$password = sha1($password.$salt);
		return $password;
	}

}