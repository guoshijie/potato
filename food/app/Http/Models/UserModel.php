<?php
namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class UserModel extends Model {

	public function __construct(){
		parent::__construct();
		if (Session::hasOldInput('id')) Session::reflash('id');
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

	/**
	 * 根据手机号获取用户基本信息
	 *
	 * @param string $tel 手机号
	 * @return array 用户基本信息
	 */
	public function getUserInfoByMobile( $tel ) {
		return DB::table( 'user' )
			->leftJoin('user_info','user_info.sh_user_id','=','user.id')
			->where( 'tel', $tel )
			->select( 'user.id','user.tel', 'user.real_name', 'user.nick_name', 'user.locked', 'user.sh_id','user.signature','user.head_pic','session_id','is_new_user','ip_address','ip','os_type' )
			->get();
	}

}