<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Models\Model;
use Illuminate\Support\Facades\Log;

class DemoModel extends Model{
/*
 * 
	//单例模式访问本package 的接口
	public static function getInstance(){
		is_null(self::$instance) && self::$instance = new self(); 
		return self::$instance;
	}
 */
	public function __construct(){
		$this->init();
	}

	public function init(){
		$this->goodsT = 'goods';	// 数据库表名
		$this->categoryT = 'goods_category';	// 数据库表名
		//$this->dateTime = date('Y-m-d H:i:s');
		//$this->dateTimestamp = time();
	}


	/*
	 * 添加
	 */
	public function add($data){
		$newId = DB::table($this->goodsT)->insertGetId($data);
		if(!$newId){
			$this->log($this->goodsT.'添加失败, $data：'.var_export($data,true));
			return false;
		}

		// 更新数量统计
		//DB::table($this->categoryT)->where('id', $data['goods_category'])->increment('goods_num');
		return $newId;
	}

	/*
	 * 修改
	 */
	public function edit($data, $id){
		//$oldDetail = DB::table($this->goodsT)->select('goods_category')->where('id', $id)->first();
		$up = DB::table($this->goodsT)->where('id', $id)->update($data);
		if(!$up){
			$this->log($this->goodsT.'修改失败, $data:'.var_export($data,true));
			return false;
		}
		/* 更新数量统计
		if(isset($data['category_id']) && $data['category_id']!=$oldDetail->category_id){
			DB::table($this->categoryT)->where('id', $oldDetail->category_id)->decrement('goods_num');
			DB::table($this->categoryT)->where('id', $data['category_id'])->increment('goods_num');
		}
		 */
		return $up;
	}

	/*
	 * 删除
	 */
	public function del($id){
		//$d = DB::table($this->goodsT)->where('id', $id)->delete();
		$d = DB::table($this->goodsT)->where('id', $id)->update(array('is_delete',1));
		if(!$d){
			$this->log($this->goodsT.'删除失败, $id:'.$id);
			return false;
		}
		// 更新数量统计
		//DB::table($this->categoryT)->where('id', $data['category_id'])->decrement('goods_num');
		return $d;
	}

	/*
	 * 列表
	 */
	public function getList($offset, $length, $where=array()){
		$table = DB::table($this->goodsT);
		$table->where('is_delete',0);
		$table->orderBy('id', 'DESC');
		if(!empty($where)){
			// 特殊情况
			if(isset($where['big_pig'])){
				$table->where('pig', '>', $where['big_pig']);
				unset($where['big_pig']);
			}

			// 常规where
			foreach($where as $k=>$v){
				if(is_array($v)){
					$table->whereIn($k, $v);
				}
				$table->where($k, $v);
			}
		}
		$list = $table->get();

		return $list;
	}

	/*
	 * 详情
	 */
	public function getDetail($id){
		$detail = DB::table($this->goodsT)->where('id', $id)->first();
		return $detail;
	}


	private function log($msg){
		Log::error("--- DemoModel error ---\n".$msg);
	}

}
