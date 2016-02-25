<?php
/**
* 系统 
* @author wangkenan@shinc.net
* @version v1.0
* @copyright shinc
*/

namespace App\Http\Models\System;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class SystemModel extends Model {
	public function __construct(){
		$this->init();
	}

	private function init(){
		$this->nowDateTime = date('Y-m-d H:i:s');
	}
	public function addOpinion($content, $userId){
		$res = DB::table('system_opinion')->insert(array('content' => $content, 'user_id'=>$userId));
		return $res;
	}

	/*
	 * 审核ios 开关
	 */
	public function checkIOSAudit($userAgent, $version=''){
		if( stripos($userAgent,'iPhone')!==false || stripos($userAgent, 'iOS')!==false ){
			// 版本号
			$version_number = DB::table('app_version')->where('os_type', 2)->orderBy('id','DESC')->take(1)->pluck('version_number');
			if($version != '' && $version_number!=$version){
				return false;
			}

			$row = DB::table('system_config')->select('ios_audit')->first();
			return $row->ios_audit;
		}
		return false;
	}

	/*
	 *	 * 支付开关
	 *
	 */
	public function getPayConfig(){
		$row = DB::table('system_config')->select('alipay_show','weixinpay_show')->first();
		$row->alipay_show = (bool)$row->alipay_show;
		$row->weixinpay_show = (bool)$row->weixinpay_show;
		return $row;
	}

}
