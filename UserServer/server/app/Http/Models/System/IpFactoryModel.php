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


class IpFactoryModel extends Model {
	public function __construct(){
		$this->init();
	}

	private function init(){
		$this->table = 'ip_factory';
		$this->nowDateTime = date('Y-m-d H:i:s');
	}

	/**
	 * 随机获取一个ip
	 */
	public function getRandomIp() {
		$maxid = DB::table('ip_factory')->max('id');
		$id = rand(1,$maxid);
		$ip = DB::table('ip_factory')->where('id', $id)->first();
		return $ip;
	}

}
