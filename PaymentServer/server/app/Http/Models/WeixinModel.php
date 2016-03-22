<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Model;
use Illuminate\Support\Facades\Log;

class WeixinModel extends Model{

	protected $table = 'jnl_weixin';

	/*
	 * 查询一条记录 by id
	 */
	public function load($id)
	{
		return DB::table($this->table)->where('transaction_id', $id)->first();
	}


	/*
	 * 更新
	 */
	public function update($id, $data)
	{
		return DB::table($this->table)->where('transaction_id', $id)->update($data);
	}
}
