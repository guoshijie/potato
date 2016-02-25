<?php

namespace App\Http\Models\Demo;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DemoModel extends Model{

	// 查询一条记录
	public function find()
	{
		return DB::table('user')->where('id',199)->first();
	}
}
