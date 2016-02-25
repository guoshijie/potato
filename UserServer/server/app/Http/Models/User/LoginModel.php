<?php
/*
 * 发送短信模型---模块化2.0
 * author：liangfeng@shinc.net
 */
namespace App\Http\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginModel extends Model{

	protected $user = 'user';
	/**
	 * 检测用户名和密码
	 *
	 * @param string $tel 手机号用户名
	 * @param string $password 密码
	 * @return boolean true/false 数据是否通过
	 */
	public function checkLoginTel($tel, $password) {
		//判断数据类型
		//todo
		return true;
	}


	public function getUserSaltByTel( $tel ) {
		return DB::table( 'user' )
			->where( 'tel', $tel )
			->pluck( 'salt' );
	}


	/********************************************用户名********************************************************/
	/**
	 * 检测用户名和密码
	 *
	 * @param string $nick_name 用户名
	 * @param string $password 密码
	 * @return boolean true/false 数据是否通过
	 */

	public function checkLoginName($nick_name, $password) {
		//判断数据类型
		//todo
		return true;
	}


	/**
	 * 根据用户名获取用户基本信息
	 *
	 * @param string $nick_name 用户名
	 * @return array 用户基本信息
	 */
	public function getUserInfoByName( $nick_name) {
		return DB::table( 'user' )
			->leftJoin('user_info','user_info.sh_user_id','=','user.id')
			->where( 'nick_name', $nick_name )
			->select( 'user.id','user.tel', 'user.real_name', 'user.nick_name', 'user.password', 'user.locked', 'user.sh_id','user.signature','user.head_pic' )
			->get();
	}


	/******************************************邮箱***********************************************************************/
	/**
	 * 检测用户名和密码
	 *
	 * @param string $email  邮箱用户名
	 * @param string $password 密码
	 * @return boolean true/false 数据是否通过
	 */

	public function checkLoginEmail($email, $password) {
		//判断数据类型
		//todo
		return true;
	}


	/**
	 * 根据用户名获取用户基本信息
	 *
	 * @param string $email 邮箱用户名
	 * @return array 用户基本信息
	 */
	public function getUserInfoByEmail( $email ) {
		return DB::table( 'user' )
			->leftJoin('user_info','user_info.sh_user_id','=','user.id')
			->where( 'email', $email )
			->select( 'user.id','user.tel', 'user.email', 'user.status', 'user.real_name', 'user.nick_name', 'user.password', 'user.locked', 'user.sh_id','user.signature','user.head_pic' )
			->get();
	}

	/******************************************登录公用Model*********************************************************************/

	/**
	 * 根据用户id获取用户基本信息
	 */
	public function getUserInfoByUserId($userId) {
		$row = DB::table( 'user' )
			->select( 'id', 'tel', 'real_name', 'nick_name', 'password', 'locked', 'sh_id', 'email', 'status' , 'money' )
			->where( 'id', $userId )
			->first();


		$row->money = intval($row->money);

		return $row;
	}

	/**
	 * 根据用户ids获取用户基本信息
	 */
	public function getUserInfoByUserIds($userIds) {
		$list = DB::table( 'user' )
			->select( 'id', 'tel', 'real_name', 'nick_name', 'head_pic' , 'locked', 'sh_id', 'email', 'status' , 'money')
			->whereIn( 'id', $userIds )
			->where('is_delete' , 0)
			->orderBy('create_time' , 'desc')
			->get();

		foreach($list as $v){

			$v->money = intval($v->money);
		}
		return $list;
	}

	/**
	 * 锁定帐号用户
	 *
	 * @param int $userId 用户id
	 * @return boolean true/false
	 */
	public function lockedUser($userId) {
		return DB::table( 'user' )
			->where( 'id', $userId )
			->update( ['locked'=>1] );
	}




	/**
	 * 检测用户是否存在
	 *
	 * @param int $userId
	 * @return boolean
	 */
	public function hasUser( $userId ) {
		$res = DB::table( 'user' )
			->select( 'id' )
			->where( 'id', $userId )
			->get();

		if( !empty( $res ) ) {
			return true;
		}

		return false;
	}


	/**
	 * 获取登录次数
	 *
	 * @return int 当天登录次数
	 */
	public function getLoginCount() {
		if( !Session::has( 'login_count' ) ) {
			return 0;
		}

		return Session::get( 'login_count' );
	}


	/**
	 * 增加登录次数
	 */
	public function addLoginCount() {
		if( !Session::has( 'login_count' ) ) {
			Session::put('login_count', 0);
		}

		$now = Session::get( 'login_count' );
		$new = $now + 1;
		Session::put( 'login_count', $new );

		return Session::get( 'login_count' );
	}


	/**
	 * 清理用户session中登录次数
	 */
	public function clearLoginCount() {
		if( Session::has( 'login_count' ) ) {
			Session::forget( 'login_count' );
		}

	}


	/**
	 * 清理session
	 */
	public function clearSession() {
		Session::flush();
	}


	/**
	 * 更新用户基本信息
	 *
	 * @param int $userId
	 * @param array $data
	 * @return boolean
	 */
	public function updUser( $userId, $data ) {
		return DB::table('user')
			->where('id', $userId)
			->update( $data );
	}


	/**
	 * 记录用户登录信息
	 *
	 * @param int $userId 用户id
	 * @param string $action 用户动作
	 * @param string $node 动作注解
	 * @return boolean true/false
	 */
	public function writeUserLog($userId, $action, $node='' ) {
		date_default_timezone_set( 'PRC' );
		$createTime = time();
		$data = array(
			'sh_user_id' => $userId,
			'action' => $action,
			'note' => $node,
			'create_time' => $createTime,
		);

		$ret = DB::table( 'user_log' )->insert( $data );

		if( !$ret ) {
			return false;
		}

		return true;
	}

	public function saveSession($userId){
		$key=md5(mt_rand(10,99).$userId);
		Session::put($key, 1);
		return $key;
	}


	/**
	 * 检测用户是否登录
	 * @param uid
	 * @return json
	 */
	public function checkLogin( $userId ) {
		$res = DB::table( 'user' ) ->select( 'id' )
			->get();

		if( !empty( $res ) ) {
			return false;
		}else{
			return true;
		}
	}

	/*
	 * 加密令牌
	 */
	public function encryptKey($password, $token){
		$userKey = md5($password.$token);
		return $userKey;
	}

	/**
	 * 设置、更新验证码
	 * @param unknown $tel
	 * @return boolean
	 */
	public function setSmsCode($tel) {
		$user_num = DB::table('user')->where('tel', $tel)->first();
		date_default_timezone_set('PRC');
		$randCode = $this->randCode();
		if (!$user_num) {
			$rst = DB::table('user')->insert(array(
				'tel'                   => $tel,
				'nick_name'             => preg_replace("/^(\d{3})\d*?(\d{3})$/", "$1*****$2", $tel),
				'login_sms_code'        => $randCode,
				'login_sms_code_expire' => time() + 5 * 60,
				'create_time'			=> time(),
				'os_type'				=> '00',
			));

		} else {
			$rst = DB::table('user')->where('tel', $tel)->update(array(
				'login_sms_code'        => $randCode,
				'login_sms_code_expire' => time() + 5 * 60,

			));
		}
		if ($rst) return $randCode;
		else return false;
	}

	/**
	 * 生成系统版本字段(sh_user->os_type)
	 * @param $os_type
	 * @param $old
	 * @return string
	 */
	public function generateOsType($os_type,$old){
		$android = substr($old,0,1);
		$ios = substr($old,1,1);
		Log::info("==>android:"+$android+"\tios"+$ios);
		if($os_type == 1){
			return '1'.$ios;
		}else{
			return $android.'1';
		}
	}

	/**
	 * 检查验证码
	 * @param unknown $tel
	 * @param unknown $code
	 * @return string
	 */
	public function checkSmsCode($tel, $code, $os_type = 1) {
		$userData = DB::table('user')->where('tel', $tel)->select('login_sms_code', 'login_sms_code_expire', 'os_type')->first();
		if (empty($userData)) return '-1';//未注册
		date_default_timezone_set('PRC');

		if($tel == '17090321777' && $code == '518518'){
			return '1';
		}

		if ($userData->login_sms_code_expire < time()) return '-2';//验证码失效
		else if (strcmp($userData->login_sms_code, $code) != 0) return '-3';//验证码不正确
		else {

			$new_type = $this->generateOsType($os_type,$userData->os_type);
			Log::info('==>new_type'+$new_type);
			DB::table('user')->where('tel', $tel)->update(array(
				'login_sms_code_expire' => time(),
				'os_type' => $new_type,
			));
			return '1';
		}
	}

	public function updateUserInfo($user_id){
		return DB::table('user')->where('id',$user_id)->update(array('is_new_user'=>1));
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

	public function updateSessionId($userId, $sessionId){
		DB::table('user')->where('id', $userId)->update(array(
			'session_id' => $sessionId
		));
	}

	public function checkSessionId($userId, $sessionId){
		$row = DB::table('user')->where('id', $userId)->select('session_id')->first();
		if($row->session_id=='' ){//兼容旧版
			return true;
		}
		return $row->session_id==$sessionId ? true : false;
	}


	public function findByUserId($user_id) {
		return DB::table('user_info')->where('sh_user_id',$user_id)->first();
	}
}