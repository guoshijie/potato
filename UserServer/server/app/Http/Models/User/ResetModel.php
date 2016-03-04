<?php 
/**
 *	用户重置密码业务逻辑操作
 *
 *	@author		liangfeng@shinc.net
 *	@version	v1.0
 *	@copyright	shinc
 */
namespace App\Http\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ResetModel extends Model{


/***********************************手机号重置密码**************************************/


	/**
	 * 验证码入库
	 * @param string $tel 电话号
	 * @param string $code 验证码
	 * @param integer $liveTime 存活最大期限
	 * @return boolean
	 */
	public function writeVerify( $tel, $code, $liveTime ) {

		return DB::table('user_vertify_code')->insertGetId(array(
				'tel'=>$tel,
				'vertify_code'=>$code, 
				'live_time'=>$liveTime
		 ));
	}
	

	/**
	* 手机号修改密码--接收短信验证后修改
	* 
	* @param string $tel 手机号
	* @param string $password 新密码
	* @return boolean
	*/
	public function updatePassword( $tel, $password ) {

		return DB::table( 'user' ) ->where( 'tel', $tel ) 
					->update(array( 'password' => $password ) );
	}

	/**
	 * 检测用户是否存在
	 *
	 * @param	int		$userId
	 * @return boolean
	 */
	public function checkUserEmail( $email ) {

		$res = DB::table( 'user' )	->select( 'id' )
				->where( 'email', $email )
				->get();

		if( empty( $res ) ) {

			return true;
		}

		return false;
	}



	/**
	* 修改用户昵称
	*
	* @param string $nick_name 用户名
	* @return json
	*/
	public function updateNickname( $user_id, $nick_name ) {
		return DB::table( 'user' ) ->where( 'id', $user_id )
					->update(array( 'nick_name' => $nick_name ) );
	}



	/**
	* 修改用户头像
	*
	* @param string $head_pic 用户头像
	* @return json
	*/
	public function updateHeadPic( $user_id, $head_pic ) {
		return DB::table( 'user' ) ->where( 'id', $user_id )
					->update(array( 'head_pic' => $head_pic ) );
	}

	public function findByUserId($user_id) {
		return DB::table('user')->where('id',$user_id)->first();
	}

	/*
	 * 意见反馈
	 */
	public function setOpinionByUserId($user_id,$content){

		$data = array(
			'content'       => $content,
			'user_id'       => $user_id,
			'create_time'   => date('Y-m-d H:i:s')
		);

		return  DB::table('system_opinion')->insertGetId($data);
	}

}
