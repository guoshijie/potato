<?php
namespace App\Http\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Libraries\Sms;

class VerifyModel extends Model {
	/**
	 * 发送验证短信
	 * @param string $tel 手机号
	 * @param number $activeTime 验证码有效时间，默认5分钟
	 * @param number $codeLen 验证码长度，默认6位
	 * @return boolean
	 */
	public function sendVerifyCode($tel, $activeTime = 5, $codeLen = 6) {
		//生成验证码
		$st = '';
		for ($i=1;$i<=$codeLen;$i++) $st .= mt_rand(0, 9);
		$content = $tel."您的验证码是：". $st .";该验证码在5分钟内有效";
		//发送短信
		$res = Sms::sendShortMessage($tel, $content);
		if (strcmp($res['status'], '1') != 0) return array('code' => $res['status'], 'msg' => $res['msg']);
		//有效时间5分钟
		$liveTime = time() + $activeTime * 60;
		//将验证码写入数据库
		$data = $this->writeVerify($tel, $st, $liveTime);
		Log::info('发送验证码记录日记,1为成功，0为失败，验证为：  '.print_r($data,1).'   验证码:'.$st);
		if ($data) return array('code' => '1', 'msg' => '发送成功');
		else array('code' => '0', 'msg' => '发送失败');
	}
	/**
	 * 找回密码-检测验证码
	 * @param string $tel 手机号
	 * @param string $code 验证码
	 * @return boolean
	 */
	public function checkVerify($tel, $code) {
		$data = DB::table ('user_vertify_code')
				->where('tel', $tel)
				->where('vertify_code', strtoupper($code))
				->where('live_time', '>', time())
				->count();
		if($data > 0) return true;
		else return false;
	}

	/**
	 * 验证码入库
	 * @param string $tel 电话号
	 * @param string $code 验证码
	 * @param integer $liveTime 存活最大期限
	 * @return boolean
	 */
	private function writeVerify($tel, $code, $liveTime) {
		return DB::table ('user_vertify_code')->insert(array(
				'tel' => $tel,
				'vertify_code' => $code,
				'live_time' => $liveTime
		));
	}


	/**
	 * 检测短信验证码
	 * 
	 * @param	string	$tel 手机号	
	 * @param	string	$code	验证码
	 * @return boolean
	 */
	public function checkVerifyCode( $tel, $code ) {
		//debug($tel);
		$res = DB::table( 'user_vertify_code' ) ->select('id')
				->where( 'tel', $tel )
				->where( 'vertify_code', strtoupper($code) )
				->where( 'live_time', '>', time() )
				->count();
		
		
		return  $res;
	}

	/**
	*	检测校验网络验证码
	*	
	*
	*/
	public function checkCaptcha(){
		
	}
}